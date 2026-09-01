<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . "/../../config/db.php";

$errors = [];

$title = "";
$slug = "";
$description = "";
$level = "";
$field = "";
$country = "";
$language = "";
$intake = "";
$tuition_fee = "";
$duration = "";
$requirements = "";
$status = "active";


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
| FORM SUBMISSION
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"] ?? "");
    $slug = trim($_POST["slug"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $level = trim($_POST["level"] ?? "");
    $field = trim($_POST["field"] ?? "");
    $country = trim($_POST["country"] ?? "");
    $language = trim($_POST["language"] ?? "");
    $intake = trim($_POST["intake"] ?? "");
    $tuition_fee = trim($_POST["tuition_fee"] ?? "");
    $duration = trim($_POST["duration"] ?? "");
    $requirements = trim($_POST["requirements"] ?? "");
    $status = $_POST["status"] ?? "active";


    /*
    |--------------------------------------------------------------------------
    | AUTO GENERATE SLUG IF EMPTY
    |--------------------------------------------------------------------------
    */

    if ($slug === "") {
        $slug = generateSlug($title);
    } else {
        $slug = generateSlug($slug);
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    if ($title === "") {
        $errors[] = "Course title is required.";
    }

    if ($slug === "") {
        $errors[] = "Course slug is required.";
    }

    if ($description === "") {
        $errors[] = "Description is required.";
    }

    if (!in_array($level, ["Bachelor", "Master", "PhD"], true)) {
        $errors[] = "Please select a valid course level.";
    }

    if ($field === "") {
        $errors[] = "Field is required.";
    }

    if ($country === "") {
        $errors[] = "Country is required.";
    }

    if ($language === "") {
        $errors[] = "Language is required.";
    }

    if ($intake === "") {
        $errors[] = "Intake is required.";
    }

    if ($tuition_fee === "") {
        $errors[] = "Tuition fee is required.";
    }

    if ($duration === "") {
        $errors[] = "Duration is required.";
    }

    if ($requirements === "") {
        $errors[] = "Requirements are required.";
    }

    if (!in_array($status, ["active", "inactive"], true)) {
        $errors[] = "Invalid status selected.";
    }


    /*
    |--------------------------------------------------------------------------
    | CHECK DUPLICATE SLUG
    |--------------------------------------------------------------------------
    */

    if ($slug !== "") {

        $slugCheck = $pdo->prepare(
            "SELECT id FROM courses WHERE slug = :slug LIMIT 1"
        );

        $slugCheck->execute([
            ":slug" => $slug
        ]);

        if ($slugCheck->fetch()) {
            $errors[] = "This course slug already exists. Please use a different slug.";
        }
    }


    /*
    |--------------------------------------------------------------------------
    | INSERT COURSE
    |--------------------------------------------------------------------------
    */

    if (empty($errors)) {

        try {

            $stmt = $pdo->prepare("
                INSERT INTO courses
                (
                    title,
                    slug,
                    description,
                    level,
                    field,
                    country,
                    language,
                    intake,
                    tuition_fee,
                    duration,
                    requirements,
                    status
                )
                VALUES
                (
                    :title,
                    :slug,
                    :description,
                    :level,
                    :field,
                    :country,
                    :language,
                    :intake,
                    :tuition_fee,
                    :duration,
                    :requirements,
                    :status
                )
            ");

            $stmt->execute([
                ":title" => $title,
                ":slug" => $slug,
                ":description" => $description,
                ":level" => $level,
                ":field" => $field,
                ":country" => $country,
                ":language" => $language,
                ":intake" => $intake,
                ":tuition_fee" => $tuition_fee,
                ":duration" => $duration,
                ":requirements" => $requirements,
                ":status" => $status
            ]);

            $_SESSION["success"] = "Course added successfully.";

            header("Location: index.php");
            exit;

        } catch (PDOException $e) {

            $errors[] = "Unable to add course. Please try again.";

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

    <title>Add Course | Edworldly Consultancy</title>

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
            cursor: pointer;
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


        /* MOBILE MENU */

        .mobile-menu-btn {
            display: none;
            width: 40px;
            height: 40px;
            border: 1px solid #dfe3e9;
            border-radius: 8px;
            background: #ffffff;
            color: #172033;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            cursor: pointer;
            flex-shrink: 0;
        }

        .sidebar-overlay {
            display: none;
        }


        /* MOBILE */

        @media (max-width: 700px) {

            .mobile-menu-btn {
                display: inline-flex;
            }

            .sidebar {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 270px;
                height: 100vh;
                z-index: 1001;
                overflow-y: auto;
            }

            .sidebar.mobile-open {
                display: flex;
            }

            .sidebar-overlay {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.35);
                z-index: 1000;
            }

            .sidebar-overlay.active {
                display: block;
            }

            .main-content {
                width: 100%;
                margin-left: 0;
            }

            .topbar {
                position: relative;
                padding: 15px 16px;
            }

            .topbar h1 {
                font-size: 18px;
            }

            .topbar p {
                font-size: 11px;
            }

            .topbar-right {
                display: none;
            }

            .content {
                padding: 16px;
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

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: auto;
            }

            .form-card {
                padding: 20px;
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

            <a href="index.php" class="nav-item active">
                <i class="fa-solid fa-book-open"></i>
                <span>Courses</span>
            </a>

            <a href="../destinations/index.php" class="nav-item">
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


    <!-- MAIN CONTENT -->

    <main class="main-content">

        <header class="topbar">

            <button
                type="button"
                class="mobile-menu-btn"
                id="mobileMenuBtn"
                aria-label="Open menu"
            >
                <i class="fa-solid fa-bars"></i>
            </button>


            <div>

                <h1>Add Course</h1>

                <p>
                    Create a new study programme.
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

            <!-- PAGE HEADER -->

            <div class="page-header">

                <div>

                    <h1>Course Information</h1>

                    <p>
                        Enter all required information for the new course.
                    </p>

                </div>

                <a href="index.php" class="back-btn">

                    <i class="fa-solid fa-arrow-left"></i>

                    Back to Courses

                </a>

            </div>


            <!-- ERRORS -->

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


            <!-- FORM -->

            <div class="form-card">

                <form method="POST">


                    <!-- BASIC INFORMATION -->

                    <div class="form-section">

                        <div class="form-section-title">

                            <i class="fa-solid fa-book-open"></i>

                            <h2>Basic Information</h2>

                        </div>


                        <div class="form-grid">

                            <div class="form-group full">

                                <label for="title">
                                    Course Title
                                    <span class="required">*</span>
                                </label>

                                <input
                                    type="text"
                                    id="title"
                                    name="title"
                                    class="form-control"
                                    placeholder="e.g. BSc Computer Science"
                                    value="<?= htmlspecialchars($title) ?>"
                                    required
                                >

                            </div>


                            <!-- SLUG -->

                            <div class="form-group full">

                                <label for="slug">
                                    Course Slug
                                    <span class="required">*</span>
                                </label>

                                <input
                                    type="text"
                                    id="slug"
                                    name="slug"
                                    class="form-control"
                                    placeholder="e.g. bsc-computer-science"
                                    value="<?= htmlspecialchars($slug) ?>"
                                    required
                                >

                                <span class="help-text">
                                    URL-friendly version of the course title. Example: bsc-computer-science
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
                                    placeholder="Enter course description..."
                                    required
                                ><?= htmlspecialchars($description) ?></textarea>

                            </div>


                            <div class="form-group">

                                <label for="level">
                                    Level
                                    <span class="required">*</span>
                                </label>

                                <select
                                    id="level"
                                    name="level"
                                    class="form-control"
                                    required
                                >

                                    <option value="">
                                        Select Level
                                    </option>

                                    <option
                                        value="Bachelor"
                                        <?= $level === "Bachelor" ? "selected" : "" ?>
                                    >
                                        Bachelor
                                    </option>

                                    <option
                                        value="Master"
                                        <?= $level === "Master" ? "selected" : "" ?>
                                    >
                                        Master
                                    </option>

                                    <option
                                        value="PhD"
                                        <?= $level === "PhD" ? "selected" : "" ?>
                                    >
                                        PhD
                                    </option>

                                </select>

                            </div>


                            <div class="form-group">

                                <label for="field">
                                    Field
                                    <span class="required">*</span>
                                </label>

                                <input
                                    type="text"
                                    id="field"
                                    name="field"
                                    class="form-control"
                                    placeholder="e.g. Computer Science"
                                    value="<?= htmlspecialchars($field) ?>"
                                    required
                                >

                            </div>

                        </div>

                    </div>


                    <!-- STUDY DETAILS -->

                    <div class="form-section">

                        <div class="form-section-title">

                            <i class="fa-solid fa-globe"></i>

                            <h2>Study Details</h2>

                        </div>


                        <div class="form-grid">

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

                                <label for="language">
                                    Language
                                    <span class="required">*</span>
                                </label>

                                <input
                                    type="text"
                                    id="language"
                                    name="language"
                                    class="form-control"
                                    placeholder="e.g. English"
                                    value="<?= htmlspecialchars($language) ?>"
                                    required
                                >

                            </div>


                            <div class="form-group">

                                <label for="intake">
                                    Intake
                                    <span class="required">*</span>
                                </label>

                                <input
                                    type="text"
                                    id="intake"
                                    name="intake"
                                    class="form-control"
                                    placeholder="e.g. September 2026"
                                    value="<?= htmlspecialchars($intake) ?>"
                                    required
                                >

                            </div>


                            <div class="form-group">

                                <label for="duration">
                                    Duration
                                    <span class="required">*</span>
                                </label>

                                <input
                                    type="text"
                                    id="duration"
                                    name="duration"
                                    class="form-control"
                                    placeholder="e.g. 3 Years"
                                    value="<?= htmlspecialchars($duration) ?>"
                                    required
                                >

                            </div>


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
                                    placeholder="e.g. £18,000 per year"
                                    value="<?= htmlspecialchars($tuition_fee) ?>"
                                    required
                                >

                            </div>

                        </div>

                    </div>


                    <!-- REQUIREMENTS -->

                    <div class="form-section">

                        <div class="form-section-title">

                            <i class="fa-solid fa-list-check"></i>

                            <h2>Requirements</h2>

                        </div>


                        <div class="form-grid">

                            <div class="form-group full">

                                <label for="requirements">
                                    Admission Requirements
                                    <span class="required">*</span>
                                </label>

                                <textarea
                                    id="requirements"
                                    name="requirements"
                                    class="form-control"
                                    placeholder="Enter academic and admission requirements..."
                                    required
                                ><?= htmlspecialchars($requirements) ?></textarea>

                                <span class="help-text">
                                    Mention academic qualifications, English requirements and any other important conditions.
                                </span>

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
                                    Course Status
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

                            Save Course

                        </button>

                    </div>


                </form>

            </div>

        </section>

    </main>

</div>


<!-- MOBILE SIDEBAR OVERLAY -->

<div
    class="sidebar-overlay"
    id="sidebarOverlay"
></div>


<script>

    const mobileMenuBtn = document.getElementById("mobileMenuBtn");
    const sidebar = document.querySelector(".sidebar");
    const sidebarOverlay = document.getElementById("sidebarOverlay");

    mobileMenuBtn.addEventListener("click", function () {

        sidebar.classList.toggle("mobile-open");
        sidebarOverlay.classList.toggle("active");

    });


    sidebarOverlay.addEventListener("click", function () {

        sidebar.classList.remove("mobile-open");
        sidebarOverlay.classList.remove("active");

    });


    const sidebarLinks = sidebar.querySelectorAll("a");

    sidebarLinks.forEach(function (link) {

        link.addEventListener("click", function () {

            sidebar.classList.remove("mobile-open");
            sidebarOverlay.classList.remove("active");

        });

    });


    /*
    |--------------------------------------------------------------------------
    | AUTO GENERATE SLUG FROM COURSE TITLE
    |--------------------------------------------------------------------------
    */

    const titleInput = document.getElementById("title");
    const slugInput = document.getElementById("slug");

    titleInput.addEventListener("input", function () {

        if (slugInput.dataset.edited !== "true") {

            let slug = this.value
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9]+/g, "-")
                .replace(/^-+|-+$/g, "");

            slugInput.value = slug;

        }

    });


    /*
    |--------------------------------------------------------------------------
    | IF USER MANUALLY EDITS SLUG
    |--------------------------------------------------------------------------
    */

    slugInput.addEventListener("input", function () {

        this.dataset.edited = "true";

        this.value = this.value
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9]+/g, "-")
            .replace(/^-+|-+$/g, "");

    });

</script>

</body>

</html>