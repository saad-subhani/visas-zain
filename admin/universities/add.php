<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . "/../../config/db.php";

$errors = [];


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
| DEFAULT VALUES
|--------------------------------------------------------------------------
*/

$name = "";
$slug = "";
$country = "";
$city = "";
$description = "";
$programmes = "";
$tuition_fee = "";
$intake_dates = "";
$requirements = "";
$english_requirements = "";
$scholarships_available = "yes";
$official_url = "";
$image_url = "";
$status = "active";


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

    if ($official_url !== "" && !filter_var($official_url, FILTER_VALIDATE_URL)) {
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

        $slugStmt = $pdo->prepare("
            SELECT id
            FROM universities
            WHERE slug = :slug
            LIMIT 1
        ");

        $slugStmt->execute([
            ":slug" => $slug
        ]);

        if ($slugStmt->fetch()) {
            $errors[] = "A university with this name already exists.";
        }
    }


    /*
    |--------------------------------------------------------------------------
    | IMAGE UPLOAD
    |--------------------------------------------------------------------------
    */

    $image_url = "";

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
                    mkdir($uploadDirectory, 0777, true);
                }

                $fileName = uniqid("university_", true) . "." . $extension;

                $uploadPath = $uploadDirectory . $fileName;

                if (move_uploaded_file($file["tmp_name"], $uploadPath)) {

                    $image_url = "uploads/universities/" . $fileName;

                } else {

                    $errors[] = "Unable to save the uploaded image.";

                }
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SAVE UNIVERSITY
    |--------------------------------------------------------------------------
    */

    if (empty($errors)) {

        try {

            $stmt = $pdo->prepare("
                INSERT INTO universities
                (
                    name,
                    slug,
                    country,
                    city,
                    description,
                    programmes,
                    tuition_fee,
                    intake_dates,
                    requirements,
                    english_requirements,
                    scholarships_available,
                    official_url,
                    image_url,
                    status
                )
                VALUES
                (
                    :name,
                    :slug,
                    :country,
                    :city,
                    :description,
                    :programmes,
                    :tuition_fee,
                    :intake_dates,
                    :requirements,
                    :english_requirements,
                    :scholarships_available,
                    :official_url,
                    :image_url,
                    :status
                )
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
                ":status" => $status
            ]);

            $_SESSION["success"] = "University added successfully.";

            header("Location: index.php");
            exit;

        } catch (PDOException $e) {

            $errors[] = "Unable to add university. Please try again.";

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

    <title>Add University | Edworldly Consultancy</title>

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

        .image-preview {
            margin-top: 12px;
            display: none;
        }

        .image-preview img {
            width: 180px;
            height: 110px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e8ebf0;
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


        /* =========================================================
           HAMBURGER MENU
        ========================================================= */

        .mobile-menu-btn {
            display: none;
            width: 40px;
            height: 40px;
            border: 1px solid #e1e5eb;
            border-radius: 8px;
            background: #ffffff;
            color: #172033;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 17px;
        }

        .mobile-menu-btn:hover {
            background: #f8f9fb;
        }

        .sidebar-overlay {
            display: none;
        }


        /* =========================================================
           RESPONSIVE
        ========================================================= */

        @media (max-width: 900px) {

            .mobile-menu-btn {
                display: inline-flex;
            }

            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                z-index: 1000;
                transform: translateX(-100%);
                transition: transform 0.25s ease;
                box-shadow: 8px 0 25px rgba(0, 0, 0, 0.08);
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .sidebar-overlay {
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.35);
                z-index: 999;
            }

            .sidebar-overlay.active {
                display: block;
            }

            .main-content {
                margin-left: 0 !important;
                width: 100%;
            }

            .topbar {
                padding-left: 18px;
                padding-right: 18px;
            }

            .topbar > div:first-child {
                display: flex;
                align-items: center;
                gap: 12px;
            }

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

            .back-btn {
                width: 100%;
                justify-content: center;
                box-sizing: border-box;
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

            .topbar {
                min-height: auto;
            }

            .topbar > div:first-child {
                align-items: flex-start;
            }

            .mobile-menu-btn {
                flex-shrink: 0;
            }

            .topbar h1 {
                font-size: 18px;
            }

            .topbar p {
                font-size: 11px;
            }

            .topbar-right {
                display: none !important;
            }

            .content {
                padding-left: 14px;
                padding-right: 14px;
            }

            .page-header h1 {
                font-size: 20px;
            }

            .form-card {
                padding: 16px;
                border-radius: 10px;
            }

            .form-section {
                margin-bottom: 25px;
            }

            .form-section-title {
                margin-bottom: 16px;
            }

            .form-section-title h2 {
                font-size: 14px;
            }

            .form-control {
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


    <!-- MOBILE SIDEBAR OVERLAY -->

    <div
        class="sidebar-overlay"
        id="sidebarOverlay"
    ></div>


    <!-- SIDEBAR -->

    <aside class="sidebar" id="sidebar">

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


        <!-- TOPBAR -->

        <header class="topbar">

            <div>

                <!-- HAMBURGER -->

                <button
                    type="button"
                    class="mobile-menu-btn"
                    id="mobileMenuBtn"
                    aria-label="Open menu"
                >

                    <i class="fa-solid fa-bars"></i>

                </button>


                <div>

                    <h1>Add University</h1>

                    <p>
                        Create a new university record.
                    </p>

                </div>

            </div>


            <div class="topbar-right">

                <div class="topbar-date">

                    <i class="fa-regular fa-calendar"></i>

                    <?= date("d M Y") ?>

                </div>

            </div>

        </header>


        <!-- CONTENT -->

        <section class="content">

            <div class="page-header">

                <div>

                    <h1>University Information</h1>

                    <p>
                        Enter complete information about the university.
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

                <form method="POST" enctype="multipart/form-data">


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
                                    placeholder="e.g. University of Birmingham"
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
                                    placeholder="e.g. United Kingdom"
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
                                    placeholder="e.g. Birmingham"
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
                                    placeholder="Brief description of the university..."
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
                                    placeholder="e.g. Computer Science, Business Administration, Engineering, Law..."
                                    required
                                ><?= htmlspecialchars($programmes) ?></textarea>

                                <span class="help-text">
                                    Add the major programmes offered by this university.
                                </span>

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
                                    placeholder="e.g. £18,000 - £25,000 per year"
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
                                    placeholder="e.g. September, January, May"
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
                                    placeholder="e.g. Academic transcripts, passport, personal statement, references..."
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
                                    placeholder="e.g. IELTS 6.5 overall with no band below 6.0"
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
                                    placeholder="https://www.example.com"
                                    value="<?= htmlspecialchars($official_url) ?>"
                                >

                                <span class="help-text">
                                    Optional. Enter the official website of the university.
                                </span>

                            </div>


                            <div class="form-group full">

                                <label for="image">

                                    University Image

                                </label>

                                <input
                                    type="file"
                                    id="image"
                                    name="image"
                                    class="form-control"
                                    accept="image/jpeg,image/png,image/webp"
                                >

                                <span class="help-text">
                                    Optional. JPG, PNG or WEBP. Maximum size 5MB.
                                </span>


                                <div
                                    class="image-preview"
                                    id="imagePreview"
                                >

                                    <img
                                        id="previewImage"
                                        src=""
                                        alt="Image Preview"
                                    >

                                </div>

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

                            <i class="fa-solid fa-check"></i>

                            Save University

                        </button>

                    </div>

                </form>

            </div>

        </section>

    </main>

</div>


<script>

/* =========================================================
   IMAGE PREVIEW
========================================================= */

const imageInput = document.getElementById("image");
const imagePreview = document.getElementById("imagePreview");
const previewImage = document.getElementById("previewImage");

imageInput.addEventListener("change", function () {

    const file = this.files[0];

    if (!file) {

        imagePreview.style.display = "none";

        return;
    }

    const reader = new FileReader();

    reader.onload = function (event) {

        previewImage.src = event.target.result;

        imagePreview.style.display = "block";

    };

    reader.readAsDataURL(file);

});


/* =========================================================
   MOBILE SIDEBAR / HAMBURGER
========================================================= */

const mobileMenuBtn = document.getElementById("mobileMenuBtn");
const sidebar = document.getElementById("sidebar");
const sidebarOverlay = document.getElementById("sidebarOverlay");

function openSidebar() {

    sidebar.classList.add("mobile-open");
    sidebarOverlay.classList.add("active");

    mobileMenuBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';

}

function closeSidebar() {

    sidebar.classList.remove("mobile-open");
    sidebarOverlay.classList.remove("active");

    mobileMenuBtn.innerHTML = '<i class="fa-solid fa-bars"></i>';

}


mobileMenuBtn.addEventListener("click", function () {

    if (sidebar.classList.contains("mobile-open")) {

        closeSidebar();

    } else {

        openSidebar();

    }

});


sidebarOverlay.addEventListener("click", function () {

    closeSidebar();

});


/* Close sidebar after clicking a navigation link on mobile */

const sidebarLinks = sidebar.querySelectorAll(".nav-item, .logout-btn");

sidebarLinks.forEach(function (link) {

    link.addEventListener("click", function () {

        if (window.innerWidth <= 900) {

            closeSidebar();

        }

    });

});


/* Reset sidebar when returning to desktop */

window.addEventListener("resize", function () {

    if (window.innerWidth > 900) {

        sidebar.classList.remove("mobile-open");
        sidebarOverlay.classList.remove("active");

        mobileMenuBtn.innerHTML = '<i class="fa-solid fa-bars"></i>';

    }

});

</script>

</body>

</html>