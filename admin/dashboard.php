<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../config/db.php";

$coursesCount = 0;
$destinationsCount = 0;
$universitiesCount = 0;
$scholarshipsCount = 0;
$messagesCount = 0;

try {
    $coursesCount = $pdo->query(
        "SELECT COUNT(*) FROM courses"
    )->fetchColumn();

    $destinationsCount = $pdo->query(
        "SELECT COUNT(*) FROM destinations"
    )->fetchColumn();

    $universitiesCount = $pdo->query(
        "SELECT COUNT(*) FROM universities"
    )->fetchColumn();

    $scholarshipsCount = $pdo->query(
        "SELECT COUNT(*) FROM scholarships"
    )->fetchColumn();

    $messagesCount = $pdo->query(
        "SELECT COUNT(*) FROM contact_messages WHERE status = 'New'"
    )->fetchColumn();

} catch (PDOException $e) {
    $error = "Unable to load dashboard statistics.";
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

    <title>Dashboard | FSC Consultancy</title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <link rel="stylesheet" href="../assets/css/admin.css">

    <style>

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
            text-decoration: none;
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

        .message-stat {
            position: relative;
        }

        @media (max-width: 900px) {

            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                z-index: 1000;
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
                width: 100%;
                margin-left: 0;
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

        @media (max-width: 650px) {

            .topbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .topbar-right {
                width: 100%;
            }

            .topbar-date {
                width: fit-content;
            }

            .content {
                padding: 18px 14px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .welcome-card {
                padding: 20px;
            }

            .welcome-content h2 {
                font-size: 20px;
            }

            .welcome-icon {
                display: none;
            }

            .quick-actions {
                grid-template-columns: 1fr;
                gap: 12px;
            }

        }

        @media (max-width: 420px) {

            .mobile-header {
                padding: 10px 12px;
            }

            .content {
                padding: 16px 12px;
            }

            .stat-card {
                padding: 15px;
            }

            .quick-card {
                padding: 15px;
            }

        }
        .sidebar-brand{
            color:rgb(14, 171, 214);
        }

    </style>

</head>

<body>

<div class="admin-layout">

    <!-- SIDEBAR -->

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

            <a
                href="dashboard.php"
                class="nav-item active"
            >
                <i class="fa-solid fa-chart-line"></i>
                <span>Dashboard</span>
            </a>

            <a
                href="courses/index.php"
                class="nav-item"
            >
                <i class="fa-solid fa-book-open"></i>
                <span>Courses</span>
            </a>

            <a
                href="destinations/index.php"
                class="nav-item"
            >
                <i class="fa-solid fa-earth-americas"></i>
                <span>Destinations</span>
            </a>

            <a
                href="universities/index.php"
                class="nav-item"
            >
                <i class="fa-solid fa-building-columns"></i>
                <span>Universities</span>
            </a>

            <a
                href="scholarships/index.php"
                class="nav-item"
            >
                <i class="fa-solid fa-award"></i>
                <span>Scholarships</span>
            </a>

            <a
                href="contact-messages/index.php"
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
                href="change-password.php"
                class="change-password-btn"
            >
                <i class="fa-solid fa-key"></i>
                <span>Change Password</span>
            </a>

            <a
                href="logout.php"
                class="logout-btn"
            >
                <i class="fa-solid fa-right-from-bracket"></i>
                Logout
            </a>

        </div>

    </aside>

    <!-- SIDEBAR OVERLAY -->

    <div
        class="sidebar-overlay"
        id="sidebarOverlay"
        onclick="closeSidebar()"
    ></div>

    <!-- MAIN CONTENT -->

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

            <div>

                <h1>Dashboard</h1>

                <p>
                    Welcome back,
                    <?= htmlspecialchars(
                        $_SESSION["admin_username"] ?? "Admin"
                    ) ?>.
                </p>

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

            <?php if (isset($error)): ?>

                <div class="alert error">

                    <i class="fa-solid fa-circle-exclamation"></i>

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>

            <!-- STAT CARDS -->

            <div class="stats-grid">

                <div class="stat-card">

                    <div class="stat-icon courses-icon">
                        <i class="fa-solid fa-book-open"></i>
                    </div>

                    <div class="stat-info">

                        <span>Total Courses</span>

                        <h2>
                            <?= (int) $coursesCount ?>
                        </h2>

                    </div>

                    <a
                        href="courses/index.php"
                        class="stat-link"
                    >
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

                <div class="stat-card">

                    <div class="stat-icon destinations-icon">
                        <i class="fa-solid fa-earth-americas"></i>
                    </div>

                    <div class="stat-info">

                        <span>Destinations</span>

                        <h2>
                            <?= (int) $destinationsCount ?>
                        </h2>

                    </div>

                    <a
                        href="destinations/index.php"
                        class="stat-link"
                    >
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

                <div class="stat-card">

                    <div class="stat-icon universities-icon">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>

                    <div class="stat-info">

                        <span>Universities</span>

                        <h2>
                            <?= (int) $universitiesCount ?>
                        </h2>

                    </div>

                    <a
                        href="universities/index.php"
                        class="stat-link"
                    >
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

                <div class="stat-card">

                    <div class="stat-icon scholarships-icon">
                        <i class="fa-solid fa-award"></i>
                    </div>

                    <div class="stat-info">

                        <span>Scholarships</span>

                        <h2>
                            <?= (int) $scholarshipsCount ?>
                        </h2>

                    </div>

                    <a
                        href="scholarships/index.php"
                        class="stat-link"
                    >
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

                <div class="stat-card message-stat">

                    <div class="stat-icon">
                        <i class="fa-solid fa-envelope"></i>
                    </div>

                    <div class="stat-info">

                        <span>New Messages</span>

                        <h2>
                            <?= (int) $messagesCount ?>
                        </h2>

                    </div>

                    <a
                        href="contact-messages/index.php"
                        class="stat-link"
                    >
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

            </div>

            <!-- WELCOME -->

            <div class="welcome-card">

                <div class="welcome-content">

                    <span class="welcome-label">
                        ADMINISTRATION PANEL
                    </span>

                    <h2>
                        Manage Your Study Abroad Content
                    </h2>

                    <p>
                        Manage courses, destinations, universities,
                        scholarships and student enquiries from one
                        centralized dashboard.
                    </p>

                </div>

                <div class="welcome-icon">
                    <i class="fa-solid fa-globe"></i>
                </div>

            </div>

            <!-- QUICK ACTIONS -->

            <div class="section-header">

                <div>

                    <h2>Quick Actions</h2>

                    <p>
                        Manage your consultancy content quickly.
                    </p>

                </div>

            </div>

            <div class="quick-actions">

                <a
                    href="courses/add.php"
                    class="quick-card"
                >

                    <div class="quick-icon">
                        <i class="fa-solid fa-plus"></i>
                    </div>

                    <div>

                        <h3>Add Course</h3>

                        <p>
                            Create a new study programme
                        </p>

                    </div>

                    <i class="fa-solid fa-arrow-right"></i>

                </a>

                <a
                    href="destinations/add.php"
                    class="quick-card"
                >

                    <div class="quick-icon">
                        <i class="fa-solid fa-earth-americas"></i>
                    </div>

                    <div>

                        <h3>Add Destination</h3>

                        <p>
                            Add a new study destination
                        </p>

                    </div>

                    <i class="fa-solid fa-arrow-right"></i>

                </a>

                <a
                    href="universities/add.php"
                    class="quick-card"
                >

                    <div class="quick-icon">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>

                    <div>

                        <h3>Add University</h3>

                        <p>
                            Add a partner university
                        </p>

                    </div>

                    <i class="fa-solid fa-arrow-right"></i>

                </a>

                <a
                    href="scholarships/add.php"
                    class="quick-card"
                >

                    <div class="quick-icon">
                        <i class="fa-solid fa-award"></i>
                    </div>

                    <div>

                        <h3>Add Scholarship</h3>

                        <p>
                            Create a scholarship listing
                        </p>

                    </div>

                    <i class="fa-solid fa-arrow-right"></i>

                </a>

                <a
                    href="contact-messages/index.php"
                    class="quick-card"
                >

                    <div class="quick-icon">
                        <i class="fa-solid fa-envelope"></i>
                    </div>

                    <div>

                        <h3>View Messages</h3>

                        <p>
                            Check student enquiries
                        </p>

                    </div>

                    <i class="fa-solid fa-arrow-right"></i>

                </a>

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