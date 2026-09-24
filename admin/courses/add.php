<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . "/../../config/db.php";

$error = "";

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
    $status = trim($_POST["status"] ?? "Active");

    if (
        $title === "" ||
        $description === "" ||
        $level === "" ||
        $field === "" ||
        $country === "" ||
        $language === "" ||
        $intake === "" ||
        $tuition_fee === "" ||
        $duration === "" ||
        $requirements === ""
    ) {

        $error = "Please fill in all required fields.";

    } elseif (!in_array($level, ["Bachelor", "Master", "PhD"], true)) {

        $error = "Invalid course level.";

    } elseif (!in_array($status, ["Active", "Inactive"], true)) {

        $error = "Invalid course status.";

    } else {

        if ($slug === "") {

            $slug = strtolower($title);

            $slug = preg_replace(
                '/[^a-z0-9]+/i',
                '-',
                $slug
            );

            $slug = trim($slug, '-');
        }

        try {

            $check = $pdo->prepare("
                SELECT id
                FROM courses
                WHERE slug = ?
                LIMIT 1
            ");

            $check->execute([$slug]);

            if ($check->fetch()) {

                $error = "A course with this slug already exists.";

            } else {

                $stmt = $pdo->prepare("
                    INSERT INTO courses (
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
                    VALUES (
                        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                    )
                ");

                $stmt->execute([
                    $title,
                    $slug,
                    $description,
                    $level,
                    $field,
                    $country,
                    $language,
                    $intake,
                    $tuition_fee,
                    $duration,
                    $requirements,
                    $status
                ]);

                $_SESSION["success"] = "Course added successfully.";

                header("Location: index.php");
                exit;
            }

        } catch (PDOException $e) {

            $error = "Something went wrong while adding the course.";

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

    <title>Add Course | FSC Consultancy</title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f7fa;
            color: #111827;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */

        .sidebar {
            width: 250px;
            background: #111827;
            color: #ffffff;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 1000;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .brand {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .brand-inner {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-icon {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            background: #102f52;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .brand-text h2 {
            font-size: 19px;
            line-height: 1.3;
            color: rgb(14, 171, 214);
        }

        .brand-text span {
            display: block;
            font-size: 11px;
            color: #9ca3af;
            margin-top: 2px;
        }

        .nav {
            padding: 20px 12px;
            flex: 1;
        }

        .nav a {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 5px;
            color: #d1d5db;
            font-size: 13px;
            transition: 0.2s ease;
        }

        .nav a i {
            width: 18px;
            text-align: center;
            font-size: 13px;
        }

        .nav a:hover,
        .nav a.active {
            background: #1f2937;
            color: #ffffff;
        }

        /* SIDEBAR BOTTOM */

        .sidebar-bottom {
            padding: 15px 12px 18px;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        .admin-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 8px;
            margin-bottom: 10px;
        }

        .user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #1f2937;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
        }

        .admin-user strong {
            display: block;
            color: #ffffff;
            font-size: 12px;
            font-weight: 600;
        }

        .admin-user span {
            display: block;
            color: #9ca3af;
            font-size: 10px;
            margin-top: 2px;
        }

        .change-password-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 11px 14px;
            margin-bottom: 8px;
            border-radius: 8px;
            background: #ffffff;
            color: #102f52;
            font-size: 13px;
            font-weight: 600;
            border: 1px solid #e8ebf0;
            transition: all 0.2s ease;
        }

        .change-password-btn:hover {
            background: #102f52;
            color: #ffffff;
            border-color: #102f52;
        }

        .change-password-btn i {
            width: 18px;
            text-align: center;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 11px 14px;
            border-radius: 8px;
            background: #1f2937;
            color: #fca5a5;
            font-size: 13px;
            font-weight: 600;
            transition: 0.2s ease;
        }

        .logout-btn:hover {
            background: #374151;
            color: #fecaca;
        }

        .logout-btn i {
            width: 18px;
            text-align: center;
        }

        /* MAIN */

        .main {
            margin-left: 250px;
            width: calc(100% - 250px);
            min-height: 100vh;
        }

        .topbar {
            height: 70px;
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
        }

        .topbar h1 {
            font-size: 22px;
            font-weight: 600;
        }

        .admin-info {
            font-size: 14px;
            color: #6b7280;
        }

        .content {
            padding: 30px;
        }

        .page-header {
            margin-bottom: 25px;
        }

        .page-header h2 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .page-header p {
            color: #6b7280;
            font-size: 14px;
        }

        /* FORM */

        .form-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 30px;
            max-width: 1100px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        label {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 12px 13px;
            border: 1px solid #d1d5db;
            border-radius: 7px;
            background: #ffffff;
            color: #111827;
            font-size: 14px;
            outline: none;
            transition: 0.2s ease;
            font-family: inherit;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #111827;
            box-shadow: 0 0 0 3px rgba(17,24,39,0.08);
        }

        textarea {
            min-height: 140px;
            resize: vertical;
            line-height: 1.6;
        }

        .help-text {
            color: #6b7280;
            font-size: 12px;
            margin-top: 6px;
        }

        /* ALERT */

        .alert {
            padding: 13px 16px;
            border-radius: 7px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* BUTTONS */

        .form-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid #e5e7eb;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 11px 18px;
            border-radius: 7px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }

        .btn-primary {
            background: #111827;
            color: #ffffff;
        }

        .btn-primary:hover {
            background: #1f2937;
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        /* MOBILE HEADER */

        .mobile-header {
            display: none;
        }

        .mobile-menu-btn {
            width: 40px;
            height: 40px;
            border: 1px solid #e1e5eb;
            border-radius: 8px;
            background: #ffffff;
            color: #102f52;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            cursor: pointer;
        }

        .sidebar-overlay {
            display: none;
        }

        @media (max-width: 900px) {

            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.25s ease;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .sidebar-overlay {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.35);
                z-index: 999;
            }

            .sidebar-overlay.show {
                display: block;
            }

            .main {
                margin-left: 0;
                width: 100%;
            }

            .mobile-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 12px 16px;
                background: #ffffff;
                border-bottom: 1px solid #e8ebf0;
                position: sticky;
                top: 0;
                z-index: 900;
            }

            .mobile-brand {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .mobile-brand-icon {
                width: 34px;
                height: 34px;
                border-radius: 8px;
                background: #102f52;
                color: #ffffff;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 14px;
            }

            .mobile-brand-text h2 {
                margin: 0;
                font-size: 15px;
                color: #102f52;
            }

            .mobile-brand-text span {
                display: block;
                margin-top: 1px;
                font-size: 10px;
                color: #8a94a5;
            }

            .topbar {
                padding-top: 18px;
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

        }

        @media (max-width: 650px) {

            .topbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
                height: auto;
                padding: 18px 16px;
            }

            .admin-info {
                display: none;
            }

            .content {
                padding: 18px 14px;
            }

        }

        @media (max-width: 600px) {

            .form-actions {
                flex-direction: column-reverse;
            }

            .form-actions .btn {
                width: 100%;
            }

        }

        @media (max-width: 420px) {

            .mobile-header {
                padding: 10px 12px;
            }

            .content {
                padding: 16px 12px;
            }

            .form-card {
                padding: 16px;
            }

        }

    </style>

</head>

<body>

<div class="layout">

    <!-- SIDEBAR -->

    <aside class="sidebar" id="sidebar">

        <div class="brand">

            <div class="brand-inner">

                <div class="brand-icon">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>

                <div class="brand-text">
                    <h2>FSC</h2>
                    <span>Consultancy</span>
                </div>

            </div>

        </div>

        <nav class="nav">

            <a href="../dashboard.php">

                <i class="fa-solid fa-chart-line"></i>

                <span>Dashboard</span>

            </a>

            <a href="index.php" class="active">

                <i class="fa-solid fa-book-open"></i>

                <span>Courses</span>

            </a>

            <a href="../destinations/index.php">

                <i class="fa-solid fa-earth-americas"></i>

                <span>Destinations</span>

            </a>

            <a href="../universities/index.php">

                <i class="fa-solid fa-building-columns"></i>

                <span>Universities</span>

            </a>

            <a href="../scholarships/index.php">

                <i class="fa-solid fa-award"></i>

                <span>Scholarships</span>

            </a>

            <a href="../contact-messages/index.php">

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
                        <?= htmlspecialchars(
                            $_SESSION["admin_username"] ?? "Admin"
                        ) ?>
                    </strong>

                    <span>Administrator</span>

                </div>

            </div>

            <a
                href="../change-password.php"
                class="change-password-btn"
            >

                <i class="fa-solid fa-key"></i>

                <span>Change Password</span>

            </a>

            <a
                href="../logout.php"
                class="logout-btn"
            >

                <i class="fa-solid fa-right-from-bracket"></i>

                <span>Logout</span>

            </a>

        </div>

    </aside>


    <!-- SIDEBAR OVERLAY -->

    <div
        class="sidebar-overlay"
        id="sidebarOverlay"
        onclick="closeSidebar()"
    ></div>


    <!-- MAIN -->

    <main class="main">

        <!-- MOBILE HEADER -->

        <div class="mobile-header">

            <div class="mobile-brand">

                <div class="mobile-brand-icon">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>

                <div class="mobile-brand-text">

                    <h2>FSC Consultancy</h2>

                    <span>Admin Panel</span>

                </div>

            </div>

            <button
                type="button"
                class="mobile-menu-btn"
                onclick="toggleSidebar()"
                aria-label="Open menu"
            >

                <i class="fa-solid fa-bars"></i>

            </button>

        </div>


        <!-- TOPBAR -->

        <header class="topbar">

            <h1>Add Course</h1>

            <div class="admin-info">

                Welcome,
                <?= htmlspecialchars(
                    $_SESSION["admin_username"] ?? "Admin"
                ) ?>

            </div>

        </header>


        <!-- CONTENT -->

        <section class="content">

            <div class="page-header">

                <h2>Add New Course</h2>

                <p>
                    Add a new study program to the Courses section.
                </p>

            </div>


            <?php if ($error): ?>

                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>

            <?php endif; ?>


            <div class="form-card">

                <form method="POST">

                    <div class="form-grid">

                        <!-- TITLE -->

                        <div class="form-group full">

                            <label for="title">
                                Course Title *
                            </label>

                            <input
                                type="text"
                                id="title"
                                name="title"
                                value="<?= htmlspecialchars(
                                    $_POST["title"] ?? ""
                                ) ?>"
                                placeholder="e.g. Bachelor of Computer Science"
                                required
                            >

                        </div>


                        <!-- SLUG -->

                        <div class="form-group full">

                            <label for="slug">
                                Slug
                            </label>

                            <input
                                type="text"
                                id="slug"
                                name="slug"
                                value="<?= htmlspecialchars(
                                    $_POST["slug"] ?? ""
                                ) ?>"
                                placeholder="e.g. bachelor-of-computer-science"
                            >

                            <div class="help-text">
                                Leave empty to generate automatically from the course title.
                            </div>

                        </div>


                        <!-- LEVEL -->

                        <div class="form-group">

                            <label for="level">
                                Study Level *
                            </label>

                            <select
                                id="level"
                                name="level"
                                required
                            >

                                <option value="">
                                    Select Level
                                </option>

                                <option
                                    value="Bachelor"
                                    <?= (($_POST["level"] ?? "") === "Bachelor")
                                        ? "selected"
                                        : "" ?>
                                >
                                    Bachelor
                                </option>

                                <option
                                    value="Master"
                                    <?= (($_POST["level"] ?? "") === "Master")
                                        ? "selected"
                                        : "" ?>
                                >
                                    Master
                                </option>

                                <option
                                    value="PhD"
                                    <?= (($_POST["level"] ?? "") === "PhD")
                                        ? "selected"
                                        : "" ?>
                                >
                                    PhD
                                </option>

                            </select>

                        </div>


                        <!-- FIELD -->

                        <div class="form-group">

                            <label for="field">
                                Field *
                            </label>

                            <input
                                type="text"
                                id="field"
                                name="field"
                                value="<?= htmlspecialchars(
                                    $_POST["field"] ?? ""
                                ) ?>"
                                placeholder="e.g. Computer Science"
                                required
                            >

                        </div>


                        <!-- COUNTRY -->

                        <div class="form-group">

                            <label for="country">
                                Country *
                            </label>

                            <input
                                type="text"
                                id="country"
                                name="country"
                                value="<?= htmlspecialchars(
                                    $_POST["country"] ?? ""
                                ) ?>"
                                placeholder="e.g. United Kingdom"
                                required
                            >

                        </div>


                        <!-- LANGUAGE -->

                        <div class="form-group">

                            <label for="language">
                                Language *
                            </label>

                            <input
                                type="text"
                                id="language"
                                name="language"
                                value="<?= htmlspecialchars(
                                    $_POST["language"] ?? ""
                                ) ?>"
                                placeholder="e.g. English"
                                required
                            >

                        </div>


                        <!-- INTAKE -->

                        <div class="form-group">

                            <label for="intake">
                                Intake *
                            </label>

                            <input
                                type="text"
                                id="intake"
                                name="intake"
                                value="<?= htmlspecialchars(
                                    $_POST["intake"] ?? ""
                                ) ?>"
                                placeholder="e.g. September 2027"
                                required
                            >

                        </div>


                        <!-- TUITION -->

                        <div class="form-group">

                            <label for="tuition_fee">
                                Tuition Fee *
                            </label>

                            <input
                                type="text"
                                id="tuition_fee"
                                name="tuition_fee"
                                value="<?= htmlspecialchars(
                                    $_POST["tuition_fee"] ?? ""
                                ) ?>"
                                placeholder="e.g. £15,000 - £20,000 per year"
                                required
                            >

                        </div>


                        <!-- DURATION -->

                        <div class="form-group">

                            <label for="duration">
                                Duration *
                            </label>

                            <input
                                type="text"
                                id="duration"
                                name="duration"
                                value="<?= htmlspecialchars(
                                    $_POST["duration"] ?? ""
                                ) ?>"
                                placeholder="e.g. 3 Years"
                                required
                            >

                        </div>


                        <!-- STATUS -->

                        <div class="form-group">

                            <label for="status">
                                Status *
                            </label>

                            <select
                                id="status"
                                name="status"
                                required
                            >

                                <option
                                    value="Active"
                                    <?= (($_POST["status"] ?? "Active") === "Active")
                                        ? "selected"
                                        : "" ?>
                                >
                                    Active
                                </option>

                                <option
                                    value="Inactive"
                                    <?= (($_POST["status"] ?? "") === "Inactive")
                                        ? "selected"
                                        : "" ?>
                                >
                                    Inactive
                                </option>

                            </select>

                        </div>


                        <!-- DESCRIPTION -->

                        <div class="form-group full">

                            <label for="description">
                                Description *
                            </label>

                            <textarea
                                id="description"
                                name="description"
                                placeholder="Write a detailed description of the course..."
                                required
                            ><?= htmlspecialchars(
                                $_POST["description"] ?? ""
                            ) ?></textarea>

                        </div>


                        <!-- REQUIREMENTS -->

                        <div class="form-group full">

                            <label for="requirements">
                                Requirements *
                            </label>

                            <textarea
                                id="requirements"
                                name="requirements"
                                placeholder="Enter admission requirements..."
                                required
                            ><?= htmlspecialchars(
                                $_POST["requirements"] ?? ""
                            ) ?></textarea>

                        </div>

                    </div>


                    <div class="form-actions">

                        <a
                            href="index.php"
                            class="btn btn-secondary"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Add Course
                        </button>

                    </div>

                </form>

            </div>

        </section>

    </main>

</div>


<script>

function toggleSidebar() {

    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebarOverlay");

    sidebar.classList.toggle("open");
    overlay.classList.toggle("show");

}

function closeSidebar() {

    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebarOverlay");

    sidebar.classList.remove("open");
    overlay.classList.remove("show");

}

document.querySelectorAll(".sidebar .nav a").forEach(function (link) {

    link.addEventListener("click", function () {

        if (window.innerWidth <= 900) {
            closeSidebar();
        }

    });

});

window.addEventListener("resize", function () {

    if (window.innerWidth > 900) {
        closeSidebar();
    }

});


/* Automatically generate slug from title */

const titleInput = document.getElementById("title");
const slugInput = document.getElementById("slug");

titleInput.addEventListener("input", function () {

    if (slugInput.dataset.manual === "true") {
        return;
    }

    let slug = this.value
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9]+/g, "-")
        .replace(/^-+|-+$/g, "");

    slugInput.value = slug;

});

slugInput.addEventListener("input", function () {

    this.dataset.manual = "true";

});

</script>

</body>

</html>