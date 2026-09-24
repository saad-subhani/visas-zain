<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . "/../../config/db.php";


/*
|--------------------------------------------------------------------------
| SLUG GENERATOR
|--------------------------------------------------------------------------
*/

function generateSlug($text)
{
    $text = strtolower(trim($text));

    $text = preg_replace('/[^a-z0-9]+/', '-', $text);

    $text = trim($text, '-');

    return $text;
}


/*
|--------------------------------------------------------------------------
| GET UNIVERSITY ID
|--------------------------------------------------------------------------
*/

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    $_SESSION["error"] = "Invalid university ID.";
    header("Location: index.php");
    exit;
}

$errors = [];


/*
|--------------------------------------------------------------------------
| FETCH UNIVERSITY
|--------------------------------------------------------------------------
*/

try {

    $stmt = $pdo->prepare("
        SELECT *
        FROM universities
        WHERE id = :id
        LIMIT 1
    ");

    $stmt->execute([
        ":id" => $id
    ]);

    $university = $stmt->fetch();

    if (!$university) {
        $_SESSION["error"] = "University not found.";
        header("Location: index.php");
        exit;
    }

} catch (PDOException $e) {

    $_SESSION["error"] = "Unable to load university.";
    header("Location: index.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| DEFAULT VALUES
|--------------------------------------------------------------------------
*/

$name = $university["name"] ?? "";
$slug = $university["slug"] ?? "";
$country = $university["country"] ?? "";
$city = $university["city"] ?? "";
$description = $university["description"] ?? "";
$programmes = $university["programmes"] ?? "";
$tuition_fee = $university["tuition_fee"] ?? "";
$intake_dates = $university["intake_dates"] ?? "";
$requirements = $university["requirements"] ?? "";
$english_requirements = $university["english_requirements"] ?? "";
$scholarships_available = $university["scholarships_available"] ?? "no";
$official_url = $university["official_url"] ?? "";
$image_url = $university["image_url"] ?? "";
$status = $university["status"] ?? "active";


/*
|--------------------------------------------------------------------------
| FORM SUBMISSION
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $slug = generateSlug($name);
    $country = trim($_POST["country"] ?? "");
    $city = trim($_POST["city"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $programmes = trim($_POST["programmes"] ?? "");
    $tuition_fee = trim($_POST["tuition_fee"] ?? "");
    $intake_dates = trim($_POST["intake_dates"] ?? "");
    $requirements = trim($_POST["requirements"] ?? "");
    $english_requirements = trim($_POST["english_requirements"] ?? "");
    $scholarships_available = $_POST["scholarships_available"] ?? "no";
    $official_url = trim($_POST["official_url"] ?? "");
    $status = $_POST["status"] ?? "active";


    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    if ($name === "") {
        $errors[] = "University name is required.";
    }

    if ($country === "") {
        $errors[] = "Country is required.";
    }

    if ($city === "") {
        $errors[] = "City is required.";
    }

    if ($description === "") {
        $errors[] = "Description is required.";
    }

    if ($programmes === "") {
        $errors[] = "Programmes are required.";
    }

    if ($tuition_fee === "") {
        $errors[] = "Tuition fee is required.";
    }

    if ($intake_dates === "") {
        $errors[] = "Intake dates are required.";
    }

    if ($requirements === "") {
        $errors[] = "Requirements are required.";
    }

    if ($english_requirements === "") {
        $errors[] = "English requirements are required.";
    }

    if (!in_array($scholarships_available, ["yes", "no"], true)) {
        $errors[] = "Invalid scholarship option.";
    }

    if (
        $official_url !== ""
        && !filter_var($official_url, FILTER_VALIDATE_URL)
    ) {
        $errors[] = "Please enter a valid official website URL.";
    }

    if (!in_array($status, ["active", "inactive"], true)) {
        $errors[] = "Invalid status selected.";
    }


    /*
    |--------------------------------------------------------------------------
    | CHECK SLUG
    |--------------------------------------------------------------------------
    */

    if ($slug === "") {

        $errors[] = "Unable to generate university slug.";

    } else {

        try {

            $slugStmt = $pdo->prepare("
                SELECT id
                FROM universities
                WHERE slug = :slug
                AND id != :id
                LIMIT 1
            ");

            $slugStmt->execute([
                ":slug" => $slug,
                ":id" => $id
            ]);

            if ($slugStmt->fetch()) {
                $errors[] = "Another university with this name already exists.";
            }

        } catch (PDOException $e) {

            $errors[] = "Unable to check university slug.";
        }
    }


    /*
    |--------------------------------------------------------------------------
    | IMAGE UPLOAD
    |--------------------------------------------------------------------------
    */

    $newImageUploaded = false;
    $newImagePath = "";
    $oldImageUrl = $image_url;

    if (
        isset($_FILES["image"])
        && $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE
    ) {

        if ($_FILES["image"]["error"] !== UPLOAD_ERR_OK) {

            $errors[] = "Unable to upload the image.";

        } else {

            $file = $_FILES["image"];

            $allowedTypes = [
                "image/jpeg" => "jpg",
                "image/png" => "png",
                "image/webp" => "webp"
            ];

            $fileType = mime_content_type($file["tmp_name"]);

            if (!isset($allowedTypes[$fileType])) {

                $errors[] = "Only JPG, PNG and WEBP images are allowed.";

            } elseif ($file["size"] > 5 * 1024 * 1024) {

                $errors[] = "Image size must be less than 5MB.";

            } else {

                $extension = $allowedTypes[$fileType];

                $uploadDirectory = __DIR__ . "/../../uploads/universities/";

                if (!is_dir($uploadDirectory)) {

                    if (!mkdir($uploadDirectory, 0777, true)) {
                        $errors[] = "Unable to create image upload directory.";
                    }
                }

                if (empty($errors)) {

                    $fileName = uniqid("university_", true) . "." . $extension;

                    $uploadPath = $uploadDirectory . $fileName;

                    if (move_uploaded_file($file["tmp_name"], $uploadPath)) {

                        $image_url = "uploads/universities/" . $fileName;

                        $newImageUploaded = true;
                        $newImagePath = $uploadPath;

                    } else {

                        $errors[] = "Unable to save the uploaded image.";
                    }
                }
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE DATABASE
    |--------------------------------------------------------------------------
    */

    if (empty($errors)) {

        try {

            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                UPDATE universities

                SET
                    name = :name,
                    slug = :slug,
                    country = :country,
                    city = :city,
                    description = :description,
                    programmes = :programmes,
                    tuition_fee = :tuition_fee,
                    intake_dates = :intake_dates,
                    requirements = :requirements,
                    english_requirements = :english_requirements,
                    scholarships_available = :scholarships_available,
                    official_url = :official_url,
                    image_url = :image_url,
                    status = :status

                WHERE id = :id
            ");

            $stmt->execute([
                ":name" => $name,
                ":slug" => $slug,
                ":country" => $country,
                ":city" => $city,
                ":description" => $description,
                ":programmes" => $programmes,
                ":tuition_fee" => $tuition_fee,
                ":intake_dates" => $intake_dates,
                ":requirements" => $requirements,
                ":english_requirements" => $english_requirements,
                ":scholarships_available" => $scholarships_available,
                ":official_url" => $official_url,
                ":image_url" => $image_url,
                ":status" => $status,
                ":id" => $id
            ]);

            $pdo->commit();


            /*
            |--------------------------------------------------------------------------
            | DELETE OLD IMAGE AFTER SUCCESSFUL DATABASE UPDATE
            |--------------------------------------------------------------------------
            */

            if (
                $newImageUploaded
                && !empty($oldImageUrl)
                && $oldImageUrl !== $image_url
            ) {

                $oldImagePath = __DIR__ . "/../../" . ltrim($oldImageUrl, "/");

                if (
                    is_file($oldImagePath)
                    && realpath($oldImagePath) !== realpath($newImagePath)
                ) {
                    @unlink($oldImagePath);
                }
            }


            $_SESSION["success"] = "University updated successfully.";

            header("Location: index.php");
            exit;

        } catch (PDOException $e) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }


            /*
            |--------------------------------------------------------------------------
            | REMOVE NEW IMAGE IF DATABASE UPDATE FAILS
            |--------------------------------------------------------------------------
            */

            if (
                $newImageUploaded
                && !empty($newImagePath)
                && is_file($newImagePath)
            ) {
                @unlink($newImagePath);
            }

            $image_url = $oldImageUrl;

            $errors[] = "Unable to update university. Please try again.";
        }
    }


    /*
    |--------------------------------------------------------------------------
    | REMOVE NEW IMAGE IF VALIDATION FAILS
    |--------------------------------------------------------------------------
    */

    if (
        !empty($errors)
        && $newImageUploaded
        && !empty($newImagePath)
        && is_file($newImagePath)
    ) {

        @unlink($newImagePath);

        $image_url = $oldImageUrl;
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

    <title>Edit University | Edworldly Consultancy</title>

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
            box-sizing: border-box;
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
            margin-top: 12px;
        }

        .current-image img {
            width: 180px;
            height: 110px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e8ebf0;
        }

        .current-image p {
            margin-top: 6px;
            color: #98a2b3;
            font-size: 11px;
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

    <!-- SIDEBAR -->

    <aside class="sidebar">

        <div class="sidebar-brand">

            <div class="brand-icon">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>

            <div>
                <h2>Edworldly</h2>
                <span>Consultancy</span>
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


            <a href="../destinations/index.php" class="nav-item">

                <i class="fa-solid fa-earth-americas"></i>

                <span>Destinations</span>

            </a>


            <a href="index.php" class="nav-item active">

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
                        <?= htmlspecialchars($_SESSION["admin_username"]) ?>
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


    <!-- MAIN -->

    <main class="main-content">

        <header class="topbar">

            <div>

                <h1>Edit University</h1>

                <p>
                    Update university information.
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

                    <h1>
                        <?= htmlspecialchars($name) ?>
                    </h1>

                    <p>
                        Edit the university details below.
                    </p>

                </div>


                <a href="index.php" class="back-btn">

                    <i class="fa-solid fa-arrow-left"></i>

                    Back to Universities

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

                    <!-- BASIC INFORMATION -->

                    <div class="form-section">

                        <div class="form-section-title">

                            <i class="fa-solid fa-building-columns"></i>

                            <h2>Basic Information</h2>

                        </div>


                        <div class="form-grid">

                            <div class="form-group">

                                <label for="name">
                                    University Name
                                    <span class="required">*</span>
                                </label>

                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    class="form-control"
                                    value="<?= htmlspecialchars($name) ?>"
                                    required
                                >

                            </div>


                            <div class="form-group">

                                <label for="country">
                                    Country
                                    <span class="required">*</span>
                                </label>

                                <input
                                    type="text"
                                    id="country"
                                    name="country"
                                    class="form-control"
                                    value="<?= htmlspecialchars($country) ?>"
                                    required
                                >

                            </div>


                            <div class="form-group">

                                <label for="city">
                                    City
                                    <span class="required">*</span>
                                </label>

                                <input
                                    type="text"
                                    id="city"
                                    name="city"
                                    class="form-control"
                                    value="<?= htmlspecialchars($city) ?>"
                                    required
                                >

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


                    <!-- PROGRAMMES -->

                    <div class="form-section">

                        <div class="form-section-title">

                            <i class="fa-solid fa-book-open"></i>

                            <h2>Programmes</h2>

                        </div>


                        <div class="form-grid">

                            <div class="form-group full">

                                <label for="programmes">
                                    Available Programmes
                                    <span class="required">*</span>
                                </label>

                                <textarea
                                    id="programmes"
                                    name="programmes"
                                    class="form-control"
                                    required
                                ><?= htmlspecialchars($programmes) ?></textarea>

                            </div>

                        </div>

                    </div>


                    <!-- FEES & INTAKES -->

                    <div class="form-section">

                        <div class="form-section-title">

                            <i class="fa-solid fa-coins"></i>

                            <h2>Fees & Intakes</h2>

                        </div>


                        <div class="form-grid">

                            <div class="form-group">

                                <label for="tuition_fee">
                                    Tuition Fee
                                    <span class="required">*</span>
                                </label>

                                <input
                                    type="text"
                                    id="tuition_fee"
                                    name="tuition_fee"
                                    class="form-control"
                                    value="<?= htmlspecialchars($tuition_fee) ?>"
                                    required
                                >

                            </div>


                            <div class="form-group">

                                <label for="intake_dates">
                                    Intake Dates
                                    <span class="required">*</span>
                                </label>

                                <input
                                    type="text"
                                    id="intake_dates"
                                    name="intake_dates"
                                    class="form-control"
                                    value="<?= htmlspecialchars($intake_dates) ?>"
                                    required
                                >

                            </div>

                        </div>

                    </div>


                    <!-- REQUIREMENTS -->

                    <div class="form-section">

                        <div class="form-section-title">

                            <i class="fa-solid fa-list-check"></i>

                            <h2>Admission Requirements</h2>

                        </div>


                        <div class="form-grid">

                            <div class="form-group full">

                                <label for="requirements">
                                    General Requirements
                                    <span class="required">*</span>
                                </label>

                                <textarea
                                    id="requirements"
                                    name="requirements"
                                    class="form-control"
                                    required
                                ><?= htmlspecialchars($requirements) ?></textarea>

                            </div>


                            <div class="form-group full">

                                <label for="english_requirements">
                                    English Language Requirements
                                    <span class="required">*</span>
                                </label>

                                <textarea
                                    id="english_requirements"
                                    name="english_requirements"
                                    class="form-control"
                                    required
                                ><?= htmlspecialchars($english_requirements) ?></textarea>

                            </div>

                        </div>

                    </div>


                    <!-- SCHOLARSHIPS -->

                    <div class="form-section">

                        <div class="form-section-title">

                            <i class="fa-solid fa-award"></i>

                            <h2>Scholarships</h2>

                        </div>


                        <div class="form-grid">

                            <div class="form-group">

                                <label for="scholarships_available">
                                    Scholarships Available
                                    <span class="required">*</span>
                                </label>

                                <select
                                    id="scholarships_available"
                                    name="scholarships_available"
                                    class="form-control"
                                    required
                                >

                                    <option
                                        value="yes"
                                        <?= $scholarships_available === "yes" ? "selected" : "" ?>
                                    >
                                        Yes - Available
                                    </option>

                                    <option
                                        value="no"
                                        <?= $scholarships_available === "no" ? "selected" : "" ?>
                                    >
                                        No - Not Available
                                    </option>

                                </select>

                            </div>

                        </div>

                    </div>


                    <!-- WEBSITE & IMAGE -->

                    <div class="form-section">

                        <div class="form-section-title">

                            <i class="fa-solid fa-image"></i>

                            <h2>Media & Website</h2>

                        </div>


                        <div class="form-grid">

                            <div class="form-group full">

                                <label for="official_url">
                                    Official University Website
                                </label>

                                <input
                                    type="url"
                                    id="official_url"
                                    name="official_url"
                                    class="form-control"
                                    value="<?= htmlspecialchars($official_url) ?>"
                                >

                            </div>


                            <div class="form-group full">

                                <label for="image">
                                    Replace University Image
                                </label>

                                <input
                                    type="file"
                                    id="image"
                                    name="image"
                                    class="form-control"
                                    accept="image/jpeg,image/png,image/webp"
                                >

                                <span class="help-text">
                                    Leave empty to keep the current image.
                                </span>


                                <?php if (!empty($image_url)): ?>

                                    <div class="current-image">

                                        <img
                                            src="../../<?= htmlspecialchars($image_url) ?>"
                                            alt="University Image"
                                        >

                                        <p>
                                            Current university image
                                        </p>

                                    </div>

                                <?php endif; ?>

                            </div>

                        </div>

                    </div>


                    <!-- STATUS -->

                    <div class="form-section">

                        <div class="form-section-title">

                            <i class="fa-solid fa-toggle-on"></i>

                            <h2>Status</h2>

                        </div>


                        <div class="form-grid">

                            <div class="form-group">

                                <label for="status">
                                    University Status
                                    <span class="required">*</span>
                                </label>

                                <select
                                    id="status"
                                    name="status"
                                    class="form-control"
                                    required
                                >

                                    <option
                                        value="active"
                                        <?= $status === "active" ? "selected" : "" ?>
                                    >
                                        Active
                                    </option>

                                    <option
                                        value="inactive"
                                        <?= $status === "inactive" ? "selected" : "" ?>
                                    >
                                        Inactive
                                    </option>

                                </select>

                            </div>

                        </div>

                    </div>


                    <!-- ACTIONS -->

                    <div class="form-actions">

                        <a href="index.php" class="cancel-btn">
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="save-btn"
                        >

                            <i class="fa-solid fa-save"></i>

                            Update University

                        </button>

                    </div>

                </form>

            </div>

        </section>

    </main>

</div>

</body>

</html>