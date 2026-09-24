<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . "/../../config/db.php";

$errors = [];

$country_name = "";
$slug = "";
$description = "";
$why_study = "";
$tuition_range = "";
$living_cost = "";
$visa_info = "";
$image_url = "";
$status = "Active";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $country_name = trim($_POST["country_name"] ?? "");
    $slug = trim($_POST["slug"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $why_study = trim($_POST["why_study"] ?? "");
    $tuition_range = trim($_POST["tuition_range"] ?? "");
    $living_cost = trim($_POST["living_cost"] ?? "");
    $visa_info = trim($_POST["visa_info"] ?? "");
    $status = $_POST["status"] ?? "Active";

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


    /* IMAGE VALIDATION */

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


    if (empty($errors)) {

        try {

            $check = $pdo->prepare("
                SELECT id
                FROM destinations
                WHERE slug = :slug
            ");

            $check->execute([
                ":slug" => $slug
            ]);

            if ($check->fetch()) {

                $errors[] = "This slug already exists.";

            } else {


                /* IMAGE UPLOAD */

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


                    if (!move_uploaded_file(
                        $_FILES["image"]["tmp_name"],
                        $uploadPath
                    )) {

                        $errors[] = "Unable to upload image.";

                    } else {

                        $image_url =
                            "../../uploads/destinations/"
                            . $fileName;

                    }

                }


                if (empty($errors)) {

                    $stmt = $pdo->prepare("
                        INSERT INTO destinations
                        (
                            country_name,
                            slug,
                            description,
                            why_study,
                            tuition_range,
                            living_cost,
                            visa_info,
                            image_url,
                            status
                        )
                        VALUES
                        (
                            :country_name,
                            :slug,
                            :description,
                            :why_study,
                            :tuition_range,
                            :living_cost,
                            :visa_info,
                            :image_url,
                            :status
                        )
                    ");

                    $stmt->execute([

                        ":country_name" => $country_name,

                        ":slug" => $slug,

                        ":description" => $description,

                        ":why_study" => $why_study,

                        ":tuition_range" => $tuition_range,

                        ":living_cost" => $living_cost,

                        ":visa_info" => $visa_info,

                        ":image_url" => $image_url,

                        ":status" => $status

                    ]);


                    $_SESSION["success"] =
                        "Destination added successfully.";

                    header("Location: index.php");

                    exit;

                }

            }

        } catch (PDOException $e) {

            $errors[] =
                "Unable to add destination. Please try again.";

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

    <title>Add Destination | Foreign Study Consultants</title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <link
        rel="stylesheet"
        href="../../assets/css/admin.css"
    >

    <style>

        .mobile-menu-btn {
            display: none;
            width: 40px;
            height: 40px;
            border: 1px solid #e8ebf0;
            border-radius: 8px;
            background: #ffffff;
            color: #172033;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 17px;
            flex-shrink: 0;
        }

        .mobile-menu-btn:hover {
            background: #f8f9fb;
        }

        .sidebar-overlay {
            display: none;
        }

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
            flex-shrink: 0;
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
            min-width: 0;
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
            line-height: 1.5;
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

        .cancel-btn:hover {
            background: #f8f9fb;
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

        @media (max-width: 900px) {

            .sidebar {
                position: fixed !important;
                top: 0;
                left: 0;
                bottom: 0;
                width: 250px !important;
                z-index: 1000;
                transform: translateX(-100%);
                transition: transform 0.25s ease;
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .sidebar-overlay {
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.35);
                z-index: 999;
                display: none;
            }

            .sidebar-overlay.active {
                display: block;
            }

            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
            }

            .mobile-menu-btn {
                display: inline-flex;
            }

            .topbar {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .topbar > div:first-child {
                min-width: 0;
                flex: 1;
            }

            .topbar h1 {
                font-size: 20px;
            }

            .topbar p {
                font-size: 12px;
            }

            .topbar-right {
                flex-shrink: 0;
            }

        }

        @media (max-width: 700px) {

            .form-grid {
                grid-template-columns: 1fr;
                gap: 16px;
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

            .back-btn {
                width: 100%;
                justify-content: center;
            }

            .form-actions {
                flex-direction: column-reverse;
                align-items: stretch;
            }

            .cancel-btn,
            .save-btn {
                width: 100%;
                justify-content: center;
                text-align: center;
                box-sizing: border-box;
            }

        }

        @media (max-width: 500px) {

            .content {
                padding-left: 14px !important;
                padding-right: 14px !important;
            }

            .topbar {
                padding-left: 14px !important;
                padding-right: 14px !important;
            }

            .topbar-right {
                display: none;
            }

            .topbar h1 {
                font-size: 18px;
            }

            .topbar p {
                font-size: 11px;
            }

            .page-header h1 {
                font-size: 20px;
            }

            .page-header p {
                font-size: 12px;
            }

            .form-card {
                padding: 16px;
                border-radius: 10px;
            }

            .form-section {
                margin-bottom: 24px;
            }

            .form-section-title {
                margin-bottom: 16px;
            }

            .form-section-title h2 {
                font-size: 14px;
            }

            .form-control {
                min-height: 44px;
                font-size: 13px;
            }

            textarea.form-control {
                min-height: 105px;
            }

        }

    </style>

</head>

<body>

<div class="admin-layout">

    <div
        class="sidebar-overlay"
        id="sidebarOverlay"
        onclick="closeSidebar()"
    ></div>


    <aside class="sidebar" id="sidebar">

        <div class="sidebar-brand">

            <div class="brand-icon">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>

            <div>
                <h2>Foreign Study</h2>
                <span>Consultants</span>
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


            <a href="../contact-messages/index.php" class="nav-item">

                <i class="fa-solid fa-envelope"></i>

                <span>Contact Messages</span>

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

            <button
                type="button"
                class="mobile-menu-btn"
                onclick="openSidebar()"
                aria-label="Open menu"
            >

                <i class="fa-solid fa-bars"></i>

            </button>


            <div>

                <h1>Add Destination</h1>

                <p>
                    Create a new study abroad destination.
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
                        Enter information about the study destination.
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

                <form method="POST" enctype="multipart/form-data">


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
                                    placeholder="e.g. United Kingdom"
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
                                    placeholder="e.g. united-kingdom"
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
                                    placeholder="Brief description of studying in this country..."
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
                                    placeholder="Explain why international students should choose this destination..."
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
                                    placeholder="e.g. £12,000 - £25,000 per year"
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
                                    placeholder="e.g. £800 - £1,200 per month"
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
                                    placeholder="Enter important student visa information..."
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

                                <label for="image">
                                    Destination Image
                                </label>

                                <input
                                    type="file"
                                    id="image"
                                    name="image"
                                    class="form-control"
                                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                >

                                <span class="help-text">
                                    Optional. JPG, PNG or WEBP. Maximum size 5MB.
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

                        <a href="index.php" class="cancel-btn">
                            Cancel
                        </a>

                        <button type="submit" class="save-btn">

                            <i class="fa-solid fa-check"></i>

                            Save Destination

                        </button>

                    </div>


                </form>

            </div>

        </section>

    </main>

</div>


<script>

function openSidebar() {

    document.getElementById("sidebar").classList.add("mobile-open");

    document.getElementById("sidebarOverlay").classList.add("active");

    document.body.style.overflow = "hidden";

}


function closeSidebar() {

    document.getElementById("sidebar").classList.remove("mobile-open");

    document.getElementById("sidebarOverlay").classList.remove("active");

    document.body.style.overflow = "";

}


document.querySelectorAll(".sidebar .nav-item").forEach(function(link) {

    link.addEventListener("click", function() {

        if (window.innerWidth <= 900) {
            closeSidebar();
        }

    });

});


window.addEventListener("resize", function() {

    if (window.innerWidth > 900) {
        closeSidebar();
    }

});

</script>

</body>

</html>