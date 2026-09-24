<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . "/../../config/db.php";

$errors = [];

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    $_SESSION["error"] = "Invalid destination ID.";
    header("Location: index.php");
    exit;
}

/* =========================
   FETCH DESTINATION
========================= */

try {

    $stmt = $pdo->prepare("
        SELECT *
        FROM destinations
        WHERE id = :id
    ");

    $stmt->execute([
        ":id" => $id
    ]);

    $destination = $stmt->fetch();

    if (!$destination) {
        $_SESSION["error"] = "Destination not found.";
        header("Location: index.php");
        exit;
    }

} catch (PDOException $e) {

    $_SESSION["error"] = "Unable to load destination.";
    header("Location: index.php");
    exit;
}

/* =========================
   FORM VALUES
========================= */

$country_name = $destination["country_name"];
$slug = $destination["slug"];
$description = $destination["description"];
$why_study = $destination["why_study"];
$tuition_range = $destination["tuition_range"];
$living_cost = $destination["living_cost"];
$visa_info = $destination["visa_info"];
$image_url = $destination["image_url"];
$status = $destination["status"];

/* =========================
   UPDATE
========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $country_name = trim($_POST["country_name"] ?? "");
    $slug = trim($_POST["slug"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $why_study = trim($_POST["why_study"] ?? "");
    $tuition_range = trim($_POST["tuition_range"] ?? "");
    $living_cost = trim($_POST["living_cost"] ?? "");
    $visa_info = trim($_POST["visa_info"] ?? "");

    $status = $_POST["status"] ?? "Active";

    /* =========================
       REQUIRED FIELDS
    ========================= */

    if ($country_name === "") {
        $errors[] = "Country name is required.";
    }

    if ($slug === "") {
        $errors[] = "Slug is required.";
    }

    if ($description === "") {
        $errors[] = "Description is required.";
    }

    if ($why_study === "") {
        $errors[] = "Why study information is required.";
    }

    if ($tuition_range === "") {
        $errors[] = "Tuition range is required.";
    }

    if ($living_cost === "") {
        $errors[] = "Living cost is required.";
    }

    if ($visa_info === "") {
        $errors[] = "Visa information is required.";
    }

    if (!in_array($status, ["Active", "Inactive"], true)) {
        $errors[] = "Invalid status selected.";
    }

    /* =========================
       IMAGE VALIDATION
    ========================= */

    if (
        isset($_FILES["image"]) &&
        $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE
    ) {

        if ($_FILES["image"]["error"] !== UPLOAD_ERR_OK) {

            $errors[] = "There was an error uploading the image.";

        } else {

            $allowedTypes = [
                "image/jpeg",
                "image/png",
                "image/webp"
            ];

            $fileType = mime_content_type(
                $_FILES["image"]["tmp_name"]
            );

            if (!in_array($fileType, $allowedTypes, true)) {
                $errors[] = "Only JPG, PNG and WEBP images are allowed.";
            }

            if ($_FILES["image"]["size"] > 5 * 1024 * 1024) {
                $errors[] = "Image size must be less than 5MB.";
            }
        }
    }

    /* =========================
       DATABASE UPDATE
    ========================= */

    if (empty($errors)) {

        try {

            /* CHECK SLUG */

            $check = $pdo->prepare("
                SELECT id
                FROM destinations
                WHERE slug = :slug
                AND id != :id
            ");

            $check->execute([
                ":slug" => $slug,
                ":id" => $id
            ]);

            if ($check->fetch()) {

                $errors[] = "This slug already exists.";

            } else {

                /* =========================
                   NEW IMAGE
                ========================= */

                $newImage = false;

                if (
                    isset($_FILES["image"]) &&
                    $_FILES["image"]["error"] === UPLOAD_ERR_OK
                ) {

                    $uploadDirectory =
                        __DIR__ . "/../../uploads/destinations/";

                    if (!is_dir($uploadDirectory)) {

                        mkdir(
                            $uploadDirectory,
                            0777,
                            true
                        );
                    }

                    $extension = strtolower(
                        pathinfo(
                            $_FILES["image"]["name"],
                            PATHINFO_EXTENSION
                        )
                    );

                    $fileName =
                        uniqid("destination_", true)
                        . "."
                        . $extension;

                    $uploadPath =
                        $uploadDirectory . $fileName;

                    if (
                        move_uploaded_file(
                            $_FILES["image"]["tmp_name"],
                            $uploadPath
                        )
                    ) {

                        $newImage =
                            "../../uploads/destinations/"
                            . $fileName;

                    } else {

                        $errors[] =
                            "Unable to upload new image.";
                    }
                }

                /* =========================
                   UPDATE RECORD
                ========================= */

                if (empty($errors)) {

                    if ($newImage !== false) {

                        $stmt = $pdo->prepare("
                            UPDATE destinations
                            SET
                                country_name = :country_name,
                                slug = :slug,
                                description = :description,
                                why_study = :why_study,
                                tuition_range = :tuition_range,
                                living_cost = :living_cost,
                                visa_info = :visa_info,
                                image_url = :image_url,
                                status = :status
                            WHERE id = :id
                        ");

                        $stmt->execute([
                            ":country_name" => $country_name,
                            ":slug" => $slug,
                            ":description" => $description,
                            ":why_study" => $why_study,
                            ":tuition_range" => $tuition_range,
                            ":living_cost" => $living_cost,
                            ":visa_info" => $visa_info,
                            ":image_url" => $newImage,
                            ":status" => $status,
                            ":id" => $id
                        ]);

                        /* =========================
                           DELETE OLD IMAGE
                        ========================= */

                        if (!empty($image_url)) {

                            $relativeOldImage = str_replace(
                                "../../",
                                "",
                                $image_url
                            );

                            $oldImagePath =
                                __DIR__ . "/../../"
                                . $relativeOldImage;

                            if (
                                file_exists($oldImagePath) &&
                                is_file($oldImagePath)
                            ) {
                                unlink($oldImagePath);
                            }
                        }

                        $image_url = $newImage;

                    } else {

                        /* UPDATE WITHOUT IMAGE */

                        $stmt = $pdo->prepare("
                            UPDATE destinations
                            SET
                                country_name = :country_name,
                                slug = :slug,
                                description = :description,
                                why_study = :why_study,
                                tuition_range = :tuition_range,
                                living_cost = :living_cost,
                                visa_info = :visa_info,
                                status = :status
                            WHERE id = :id
                        ");

                        $stmt->execute([
                            ":country_name" => $country_name,
                            ":slug" => $slug,
                            ":description" => $description,
                            ":why_study" => $why_study,
                            ":tuition_range" => $tuition_range,
                            ":living_cost" => $living_cost,
                            ":visa_info" => $visa_info,
                            ":status" => $status,
                            ":id" => $id
                        ]);
                    }

                    $_SESSION["success"] =
                        "Destination updated successfully.";

                    header("Location: index.php");
                    exit;
                }
            }

        } catch (PDOException $e) {

            $errors[] =
                "Unable to update destination. Please try again.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Destination | Foreign Study Consultants</title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <link
        rel="stylesheet"
        href="../../assets/css/admin.css"
    >

    <style>

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 24px;
        }

        .page-header h1 {
            font-size: 23px;
            color: #172033;
            margin-bottom: 5px;
        }

        .page-header p {
            font-size: 13px;
            color: #7e8899;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 15px;
            border: 1px solid #dfe3e9;
            background: #ffffff;
            color: #475467;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
        }

        .back-btn:hover {
            background: #f8f9fb;
        }

        .form-card {
            background: #ffffff;
            border: 1px solid #e8ebf0;
            border-radius: 12px;
            padding: 28px;
        }

        .form-section {
            margin-bottom: 30px;
        }

        .form-section:last-child {
            margin-bottom: 0;
        }

        .form-section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 14px;
            margin-bottom: 20px;
            border-bottom: 1px solid #edf0f4;
        }

        .form-section-title i {
            width: 32px;
            height: 32px;
            border-radius: 7px;
            background: #f0f2f6;
            color: #172033;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
        }

        .form-section-title h2 {
            font-size: 15px;
            color: #172033;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-group label {
            font-size: 12px;
            font-weight: 600;
            color: #344054;
            margin-bottom: 8px;
        }

        .required {
            color: #d92d20;
        }

        .form-control {
            width: 100%;
            min-height: 45px;
            border: 1px solid #dfe3e9;
            border-radius: 8px;
            padding: 10px 13px;
            font-family: inherit;
            font-size: 13px;
            color: #344054;
            background: #ffffff;
            outline: none;
            transition: 0.2s ease;
        }

        .form-control:focus {
            border-color: #172033;
            box-shadow: 0 0 0 3px rgba(23, 32, 51, 0.07);
        }

        textarea.form-control {
            min-height: 115px;
            resize: vertical;
            line-height: 1.6;
        }

        select.form-control {
            cursor: pointer;
        }

        .help-text {
            margin-top: 6px;
            color: #98a2b3;
            font-size: 11px;
        }

        .error-box {
            background: #fff1f1;
            border: 1px solid #ffd6d3;
            color: #b42318;
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 22px;
        }

        .error-box-title {
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 7px;
        }

        .error-box ul {
            padding-left: 20px;
            margin: 0;
        }

        .error-box li {
            font-size: 12px;
            margin: 4px 0;
        }

        .current-image {
            width: 120px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e8ebf0;
            margin-bottom: 10px;
        }

        .no-image {
            width: 120px;
            height: 80px;
            border-radius: 8px;
            background: #f2f4f7;
            color: #98a2b3;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 10px;
        }

        .form-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            padding-top: 22px;
            border-top: 1px solid #edf0f4;
        }

        .cancel-btn {
            padding: 11px 18px;
            border-radius: 8px;
            border: 1px solid #dfe3e9;
            background: #ffffff;
            color: #475467;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
        }

        .save-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 19px;
            border-radius: 8px;
            border: none;
            background: #172033;
            color: #ffffff;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        .save-btn:hover {
            background: #27344d;
        }

        @media (max-width: 700px) {

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: auto;
            }

            .form-card {
                padding: 20px;
            }

            .page-header {
                align-items: flex-start;
                flex-direction: column;
            }

        }

    </style>

</head>

<body>

<div class="admin-layout">

    <aside class="sidebar">

        <div class="sidebar-brand">

            <div class="brand-icon">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>

            <div>
                <h2>FSC</h2>
                <span>Foreign Study Consultants</span>
            </div>

        </div>

        <nav class="sidebar-nav">

            <a href="../dashboard.php" class="nav-item">
                <i class="fa-solid fa-chart-line"></i>
                <span>Dashboard</span>
            </a>

            <a href="../courses/index.php" class="nav-item">
                <i class="fa-solid fa-book-open"></i>
                <span>Courses</span>
            </a>

            <a href="index.php" class="nav-item active">
                <i class="fa-solid fa-earth-americas"></i>
                <span>Destinations</span>
            </a>

            <a href="../universities/index.php" class="nav-item">
                <i class="fa-solid fa-building-columns"></i>
                <span>Universities</span>
            </a>

            <a href="../scholarships/index.php" class="nav-item">
                <i class="fa-solid fa-award"></i>
                <span>Scholarships</span>
            </a>

        </nav>

        <div class="sidebar-bottom">

            <div class="admin-user">

                <div class="user-avatar">
                    <i class="fa-solid fa-user"></i>
                </div>

                <div>

                    <strong>
                        <?= htmlspecialchars($_SESSION["admin_username"] ?? "Admin") ?>
                    </strong>

                    <span>Administrator</span>

                </div>

            </div>

            <a href="../logout.php" class="logout-btn">

                <i class="fa-solid fa-right-from-bracket"></i>

                Logout

            </a>

        </div>

    </aside>

    <main class="main-content">

        <header class="topbar">

            <div>

                <h1>Edit Destination</h1>

                <p>
                    Update study abroad destination information.
                </p>

            </div>

            <div class="topbar-right">

                <div class="topbar-date">

                    <i class="fa-regular fa-calendar"></i>

                    <?= date("d M Y") ?>

                </div>

            </div>

        </header>

        <section class="content">

            <div class="page-header">

                <div>

                    <h1>Destination Information</h1>

                    <p>
                        Update information about the study destination.
                    </p>

                </div>

                <a href="index.php" class="back-btn">

                    <i class="fa-solid fa-arrow-left"></i>

                    Back to Destinations

                </a>

            </div>

            <?php if (!empty($errors)): ?>

                <div class="error-box">

                    <div class="error-box-title">

                        <i class="fa-solid fa-circle-exclamation"></i>

                        Please fix the following errors:

                    </div>

                    <ul>

                        <?php foreach ($errors as $error): ?>

                            <li>
                                <?= htmlspecialchars($error) ?>
                            </li>

                        <?php endforeach; ?>

                    </ul>

                </div>

            <?php endif; ?>

            <div class="form-card">

                <form
                    method="POST"
                    enctype="multipart/form-data"
                >

                    <div class="form-section">

                        <div class="form-section-title">

                            <i class="fa-solid fa-earth-americas"></i>

                            <h2>Basic Information</h2>

                        </div>

                        <div class="form-grid">

                            <div class="form-group">

                                <label for="country_name">

                                    Country Name

                                    <span class="required">*</span>

                                </label>

                                <input
                                    type="text"
                                    id="country_name"
                                    name="country_name"
                                    class="form-control"
                                    value="<?= htmlspecialchars($country_name) ?>"
                                    required
                                >

                            </div>

                            <div class="form-group">

                                <label for="slug">

                                    Slug

                                    <span class="required">*</span>

                                </label>

                                <input
                                    type="text"
                                    id="slug"
                                    name="slug"
                                    class="form-control"
                                    value="<?= htmlspecialchars($slug) ?>"
                                    required
                                >

                                <span class="help-text">
                                    Use lowercase letters and hyphens only.
                                </span>

                            </div>

                            <div class="form-group full">

                                <label for="description">

                                    Description

                                    <span class="required">*</span>

                                </label>

                                <textarea
                                    id="description"
                                    name="description"
                                    class="form-control"
                                    required
                                ><?= htmlspecialchars($description) ?></textarea>

                            </div>

                        </div>

                    </div>

                    <div class="form-section">

                        <div class="form-section-title">

                            <i class="fa-solid fa-star"></i>

                            <h2>Why Study Here?</h2>

                        </div>

                        <div class="form-grid">

                            <div class="form-group full">

                                <label for="why_study">

                                    Why Study

                                    <span class="required">*</span>

                                </label>

                                <textarea
                                    id="why_study"
                                    name="why_study"
                                    class="form-control"
                                    required
                                ><?= htmlspecialchars($why_study) ?></textarea>

                            </div>

                        </div>

                    </div>

                    <div class="form-section">

                        <div class="form-section-title">

                            <i class="fa-solid fa-coins"></i>

                            <h2>Costs & Visa</h2>

                        </div>

                        <div class="form-grid">

                            <div class="form-group">

                                <label for="tuition_range">

                                    Tuition Range

                                    <span class="required">*</span>

                                </label>

                                <input
                                    type="text"
                                    id="tuition_range"
                                    name="tuition_range"
                                    class="form-control"
                                    value="<?= htmlspecialchars($tuition_range) ?>"
                                    required
                                >

                            </div>

                            <div class="form-group">

                                <label for="living_cost">

                                    Living Cost

                                    <span class="required">*</span>

                                </label>

                                <input
                                    type="text"
                                    id="living_cost"
                                    name="living_cost"
                                    class="form-control"
                                    value="<?= htmlspecialchars($living_cost) ?>"
                                    required
                                >

                            </div>

                            <div class="form-group full">

                                <label for="visa_info">

                                    Visa Information

                                    <span class="required">*</span>

                                </label>

                                <textarea
                                    id="visa_info"
                                    name="visa_info"
                                    class="form-control"
                                    required
                                ><?= htmlspecialchars($visa_info) ?></textarea>

                            </div>

                        </div>

                    </div>

                    <div class="form-section">

                        <div class="form-section-title">

                            <i class="fa-solid fa-image"></i>

                            <h2>Media</h2>

                        </div>

                        <div class="form-grid">

                            <div class="form-group full">

                                <label>
                                    Current Image
                                </label>

                                <?php if (!empty($image_url)): ?>

                                    <img
                                        src="<?= htmlspecialchars($image_url) ?>"
                                        alt="<?= htmlspecialchars($country_name) ?>"
                                        class="current-image"
                                    >

                                <?php else: ?>

                                    <div class="no-image">

                                        <i class="fa-solid fa-image"></i>

                                    </div>

                                <?php endif; ?>

                                <label for="image">
                                    Change Image
                                </label>

                                <input
                                    type="file"
                                    id="image"
                                    name="image"
                                    class="form-control"
                                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                >

                                <span class="help-text">
                                    Optional. Leave empty to keep the current image.
                                    JPG, PNG or WEBP. Maximum size 5MB.
                                </span>

                            </div>

                        </div>

                    </div>

                    <div class="form-section">

                        <div class="form-section-title">

                            <i class="fa-solid fa-toggle-on"></i>

                            <h2>Status</h2>

                        </div>

                        <div class="form-grid">

                            <div class="form-group">

                                <label for="status">

                                    Destination Status

                                    <span class="required">*</span>

                                </label>

                                <select
                                    id="status"
                                    name="status"
                                    class="form-control"
                                    required
                                >

                                    <option
                                        value="Active"
                                        <?= $status === "Active" ? "selected" : "" ?>
                                    >
                                        Active
                                    </option>

                                    <option
                                        value="Inactive"
                                        <?= $status === "Inactive" ? "selected" : "" ?>
                                    >
                                        Inactive
                                    </option>

                                </select>

                            </div>

                        </div>

                    </div>

                    <div class="form-actions">

                        <a
                            href="index.php"
                            class="cancel-btn"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="save-btn"
                        >

                            <i class="fa-solid fa-check"></i>

                            Update Destination

                        </button>

                    </div>

                </form>

            </div>

        </section>

    </main>

</div>

</body>

</html>