```php
<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . "/../../config/db.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    $_SESSION["error"] = "Invalid course ID.";
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM courses WHERE id = :id");
$stmt->execute([":id" => $id]);

$course = $stmt->fetch();

if (!$course) {
    $_SESSION["error"] = "Course not found.";
    header("Location: index.php");
    exit;
}

$errors = [];

$title = $course["title"];
$slug = $course["slug"];
$description = $course["description"];
$level = $course["level"];
$field = $course["field"];
$country = $course["country"];
$language = $course["language"];
$intake = $course["intake"];
$tuition_fee = $course["tuition_fee"];
$duration = $course["duration"];
$requirements = $course["requirements"];
$status = $course["status"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"] ?? "");

    // Automatically generate slug from course title
    $slug = strtolower(trim($title));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');

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

    if ($title === "") {
        $errors[] = "Course title is required.";
    }

    if ($slug === "") {
        $errors[] = "Unable to generate course slug.";
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

    // Check if slug already exists for another course
    if (empty($errors)) {

        $slugCheck = $pdo->prepare("
            SELECT id
            FROM courses
            WHERE slug = :slug
            AND id != :id
            LIMIT 1
        ");

        $slugCheck->execute([
            ":slug" => $slug,
            ":id" => $id
        ]);

        if ($slugCheck->fetch()) {
            $errors[] = "A course with this title/slug already exists.";
        }
    }

    if (empty($errors)) {

        try {

            $stmt = $pdo->prepare("
                UPDATE courses
                SET
                    title = :title,
                    slug = :slug,
                    description = :description,
                    level = :level,
                    field = :field,
                    country = :country,
                    language = :language,
                    intake = :intake,
                    tuition_fee = :tuition_fee,
                    duration = :duration,
                    requirements = :requirements,
                    status = :status,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id
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
                ":status" => $status,
                ":id" => $id
            ]);

            $_SESSION["success"] = "Course updated successfully.";

            header("Location: index.php");
            exit;

        } catch (PDOException $e) {

            $errors[] = "Unable to update course. Please try again.";

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

    <title>Edit Course | Edworldly Consultancy</title>

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

        /* MOBILE SIDEBAR */

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
            font-size: 17px;
            cursor: pointer;
            flex-shrink: 0;
        }

        .sidebar-close-btn {
            display: none;
        }

        .sidebar-overlay {
            display: none;
        }

        @media (max-width: 700px) {

            .mobile-menu-btn {
                display: inline-flex;
            }

            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                width: 260px;
                height: 100vh;
                z-index: 1001;
                transform: translateX(-100%);
                transition: transform 0.25s ease;
                box-shadow: 8px 0 25px rgba(0, 0, 0, 0.12);
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .sidebar-close-btn {
                display: flex;
                position: absolute;
                top: 15px;
                right: 15px;
                width: 32px;
                height: 32px;
                border: none;
                border-radius: 7px;
                background: #f2f4f7;
                color: #172033;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                font-size: 14px;
                z-index: 2;
            }

            .sidebar-overlay {
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.45);
                z-index: 1000;
                display: none;
            }

            .sidebar-overlay.active {
                display: block;
            }

            .main-content {
                width: 100%;
                margin-left: 0;
            }

            .topbar {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .topbar > div:first-child {
                flex: 1;
                min-width: 0;
            }

            .topbar h1 {
                font-size: 19px;
            }

            .topbar-right {
                display: none;
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
            }

        }

        @media (max-width: 400px) {

            .form-card {
                padding: 16px;
            }

            .form-section {
                margin-bottom: 24px;
            }

            .form-section-title h2 {
                font-size: 14px;
            }

            .form-control {
                font-size: 13px;
            }

        }

    </style>

</head>

<body>

<div class="admin-layout">

    <!-- SIDEBAR -->

    <aside class="sidebar" id="sidebar">

        <!-- MOBILE CLOSE BUTTON -->

        <button
            type="button"
            class="sidebar-close-btn"
            id="sidebarClose"
            aria-label="Close menu"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>


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


    <!-- MOBILE OVERLAY -->

    <div
        class="sidebar-overlay"
        id="sidebarOverlay"
    ></div>


    <!-- MAIN -->

    <main class="main-content">

        <header class="topbar">

            <!-- MOBILE HAMBURGER -->

            <button
                type="button"
                class="mobile-menu-btn"
                id="sidebarOpen"
                aria-label="Open menu"
            >
                <i class="fa-solid fa-bars"></i>
            </button>


            <div>

                <h1>Edit Course</h1>

                <p>
                    Update the selected study programme.
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

                    <h1>Edit Course</h1>

                    <p>
                        Update course information and status.
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
                                    value="<?= htmlspecialchars($title) ?>"
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
                                    required
                                ><?= htmlspecialchars($requirements) ?></textarea>

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

                        <button type="submit" class="save-btn">

                            <i class="fa-solid fa-floppy-disk"></i>

                            Update Course

                        </button>

                    </div>


                </form>

            </div>

        </section>

    </main>

</div>


<!-- MOBILE SIDEBAR SCRIPT -->

<script>

    const sidebar = document.getElementById("sidebar");
    const sidebarOpen = document.getElementById("sidebarOpen");
    const sidebarClose = document.getElementById("sidebarClose");
    const sidebarOverlay = document.getElementById("sidebarOverlay");

    function openSidebar() {
        sidebar.classList.add("mobile-open");
        sidebarOverlay.classList.add("active");
        document.body.style.overflow = "hidden";
    }

    function closeSidebar() {
        sidebar.classList.remove("mobile-open");
        sidebarOverlay.classList.remove("active");
        document.body.style.overflow = "";
    }

    sidebarOpen.addEventListener("click", openSidebar);

    sidebarClose.addEventListener("click", closeSidebar);

    sidebarOverlay.addEventListener("click", closeSidebar);

    document.querySelectorAll(".sidebar .nav-item").forEach(function (item) {
        item.addEventListener("click", closeSidebar);
    });

    document.querySelector(".logout-btn").addEventListener("click", closeSidebar);

</script>

</body>

</html>
```
