<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . "/../../config/db.php";

$success = $_SESSION["success"] ?? "";
$error = $_SESSION["error"] ?? "";

unset($_SESSION["success"], $_SESSION["error"]);

$stmt = $pdo->query("
    SELECT *
    FROM courses
    ORDER BY created_at DESC
");

$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Courses | FSC Consultancy</title>

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

        /* =========================
           DASHBOARD LAYOUT
        ========================= */

        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        /* =========================
           SIDEBAR
           SAME AS DASHBOARD
        ========================= */

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
            border-right: 1px solid #e5e7eb;
        }

        .sidebar-brand {
            padding: 25px 20px;
            border-bottom: 1px solid #3c3d3f;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-brand .brand-icon {
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

        .sidebar-brand h2 {
            margin: 0;
            font-size: 19px;
            line-height: 1.3;
            color: rgb(14, 171, 214);
        }

        .sidebar-brand span {
            display: block;
            font-size: 11px;
            color: #8a94a5;
            margin-top: 2px;
        }

        .sidebar-nav {
            padding: 20px 12px;
            flex: 1;
        }

        .sidebar-nav .nav-item {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 5px;
            color: #a4adba;
            font-size: 13px;
            transition: 0.2s ease;
        }

        .sidebar-nav .nav-item i {
            width: 18px;
            text-align: center;
            font-size: 13px;
        }

        .sidebar-nav .nav-item:hover,
        .sidebar-nav .nav-item.active {
            background: #f0f4f8;
            color: #102f52;
        }

        /* =========================
           SIDEBAR BOTTOM
        ========================= */

        .sidebar-bottom {
            padding: 15px 12px 18px;
            border-top: 1px solid #3c3d3f;
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
            background: #0EABD6;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
        }

        .admin-user strong {
            display: block;
            color: #0EABD6;
            font-size: 12px;
            font-weight: 600;
        }

        .admin-user span {
            display: block;
            color: #8a94a5;
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
            background: #172033;
            color: #c8b7b7;
            font-size: 13px;
            font-weight: 600;
            transition: 0.2s ease;
            text-align: center;        }

        .logout-btn:hover {
            background: #b91c1c;
            color: #fef2f2;
        }

        .logout-btn i {
            width: 18px;
            text-align: center;
        }

        /* =========================
           MAIN
        ========================= */

        .main-content {
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

        /* =========================
           PAGE HEADER
        ========================= */

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            gap: 15px;
        }

        .page-header h2 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .page-header p {
            color: #6b7280;
            font-size: 14px;
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

        /* =========================
           ALERTS
        ========================= */

        .alert {
            padding: 13px 16px;
            border-radius: 7px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* =========================
           TABLE
        ========================= */

        .table-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1050px;
        }

        th {
            background: #f9fafb;
            color: #374151;
            font-size: 13px;
            font-weight: 600;
            text-align: left;
            padding: 15px;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 14px;
            color: #374151;
            vertical-align: middle;
        }

        tbody tr:hover {
            background: #fafafa;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .course-title {
            font-weight: 600;
            color: #111827;
            max-width: 250px;
        }

        .muted {
            color: #6b7280;
        }

        /* =========================
           STATUS
        ========================= */

        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-active {
            background: #dcfce7;
            color: #166534;
        }

        .status-inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        /* =========================
           ACTIONS
        ========================= */

        .actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 7px 11px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .edit-btn {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .edit-btn:hover {
            background: #dbeafe;
        }

        .delete-btn {
            background: #fef2f2;
            color: #dc2626;
        }

        .delete-btn:hover {
            background: #fee2e2;
        }

        /* =========================
           EMPTY
        ========================= */

        .empty {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }

        .empty h3 {
            color: #111827;
            margin-bottom: 8px;
            font-size: 18px;
        }

        .empty p {
            font-size: 14px;
            margin-bottom: 20px;
        }

        /* =========================
           MOBILE HEADER
        ========================= */

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

        /* =========================
           RESPONSIVE
        ========================= */

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

            .main-content {
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

            .page-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .page-header .btn {
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

        }

    </style>

</head>

<body>

<div class="admin-layout">

    <!-- =========================
         SIDEBAR
         SAME AS DASHBOARD
    ========================== -->

    <aside class="sidebar" id="sidebar">

        <div class="sidebar-brand">

            <div class="brand-icon">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>

            <div>
                <h2>Foreign Study</h2>
                <span>Consultant</span>
            </div>

        </div>

        <nav class="sidebar-nav">

            <!-- Dashboard -->
            <a
                href="../dashboard.php"
                class="nav-item"
            >
                <i class="fa-solid fa-chart-line"></i>
                <span>Dashboard</span>
            </a>

            <!-- Courses -->
            <a
                href="index.php"
                class="nav-item active"
            >
                <i class="fa-solid fa-book-open"></i>
                <span>Courses</span>
            </a>

            <!-- Destinations -->
            <a
                href="../destinations/index.php"
                class="nav-item"
            >
                <i class="fa-solid fa-earth-americas"></i>
                <span>Destinations</span>
            </a>

            <!-- Universities -->
            <a
                href="../universities/index.php"
                class="nav-item"
            >
                <i class="fa-solid fa-building-columns"></i>
                <span>Universities</span>
            </a>

            <!-- Scholarships -->
            <a
                href="../scholarships/index.php"
                class="nav-item"
            >
                <i class="fa-solid fa-award"></i>
                <span>Scholarships</span>
            </a>

            <!-- Contact Messages -->
            <a
                href="../contact-messages/index.php"
                class="nav-item"
            >
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


    <!-- =========================
         MAIN CONTENT
    ========================== -->

    <main class="main-content">

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

            <h1>Courses</h1>

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

                <div>

                    <h2>Courses</h2>

                    <p>
                        Manage all study programs and courses.
                    </p>

                </div>

                <a
                    href="add.php"
                    class="btn btn-primary"
                >
                    + Add New Course
                </a>

            </div>


            <!-- SUCCESS MESSAGE -->

            <?php if ($success): ?>

                <div class="alert alert-success">
                    <?= htmlspecialchars($success) ?>
                </div>

            <?php endif; ?>


            <!-- ERROR MESSAGE -->

            <?php if ($error): ?>

                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>

            <?php endif; ?>


            <!-- TABLE -->

            <div class="table-card">

                <?php if (empty($courses)): ?>

                    <div class="empty">

                        <h3>No Courses Found</h3>

                        <p>
                            You have not added any courses yet.
                        </p>

                        <a
                            href="add.php"
                            class="btn btn-primary"
                        >
                            Add Your First Course
                        </a>

                    </div>

                <?php else: ?>

                    <div class="table-wrapper">

                        <table>

                            <thead>

                                <tr>

                                    <th>#</th>
                                    <th>Course</th>
                                    <th>Level</th>
                                    <th>Field</th>
                                    <th>Country</th>
                                    <th>Language</th>
                                    <th>Intake</th>
                                    <th>Tuition Fee</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th>Actions</th>

                                </tr>

                            </thead>

                            <tbody>

                            <?php foreach ($courses as $index => $course): ?>

                                <tr>

                                    <td>
                                        <?= $index + 1 ?>
                                    </td>

                                    <td>

                                        <div class="course-title">
                                            <?= htmlspecialchars(
                                                $course["title"]
                                            ) ?>
                                        </div>

                                        <div class="muted">
                                            <?= htmlspecialchars(
                                                $course["slug"]
                                            ) ?>
                                        </div>

                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $course["level"]
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $course["field"]
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $course["country"]
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $course["language"]
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $course["intake"]
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $course["tuition_fee"]
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $course["duration"]
                                        ) ?>
                                    </td>

                                    <td>

                                        <?php if ($course["status"] === "Active"): ?>

                                            <span class="status status-active">
                                                Active
                                            </span>

                                        <?php else: ?>

                                            <span class="status status-inactive">
                                                Inactive
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                    <td>

                                        <div class="actions">

                                            <a
                                                href="edit.php?id=<?= (int) $course["id"] ?>"
                                                class="action-btn edit-btn"
                                            >
                                                Edit
                                            </a>

                                            <a
                                                href="delete.php?id=<?= (int) $course["id"] ?>"
                                                class="action-btn delete-btn"
                                                onclick="return confirm('Are you sure you want to delete this course?');"
                                            >
                                                Delete
                                            </a>

                                        </div>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                <?php endif; ?>

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


document.querySelectorAll(".sidebar .nav-item").forEach(function (link) {

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

</script>

</body>

</html>