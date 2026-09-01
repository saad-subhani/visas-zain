<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . "/../../config/db.php";

$courses = [];

try {
    $stmt = $pdo->query("
        SELECT *
        FROM courses
        ORDER BY created_at DESC
    ");

    $courses = $stmt->fetchAll();

} catch (PDOException $e) {
    $_SESSION["error"] = "Unable to load courses.";
}

$success = $_SESSION["success"] ?? "";
$error = $_SESSION["error"] ?? "";

unset($_SESSION["success"], $_SESSION["error"]);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Courses | Edworldly Consultancy</title>

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
            margin-bottom: 5px;
        }

        .page-header p {
            font-size: 13px;
            color: #7e8899;
        }

        .add-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #172033;
            color: #ffffff;
            padding: 11px 17px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            transition: 0.2s ease;
        }

        .add-btn:hover {
            background: #27344d;
        }

        .alert {
            padding: 13px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .alert.success {
            background: #ecfdf3;
            color: #067647;
            border: 1px solid #abefc6;
        }

        .alert.error {
            background: #fff1f1;
            color: #b42318;
            border: 1px solid #ffd6d3;
        }

        .table-card {
            background: #ffffff;
            border: 1px solid #e8ebf0;
            border-radius: 12px;
            overflow: hidden;
        }

        .table-top {
            padding: 18px 20px;
            border-bottom: 1px solid #edf0f4;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .table-top h2 {
            font-size: 15px;
            color: #172033;
        }

        .course-count {
            color: #7e8899;
            font-size: 12px;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .courses-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 950px;
        }

        .courses-table th {
            background: #f8f9fb;
            color: #687386;
            font-size: 11px;
            font-weight: 700;
            text-align: left;
            padding: 13px 16px;
            border-bottom: 1px solid #e8ebf0;
            white-space: nowrap;
        }

        .courses-table td {
            padding: 15px 16px;
            border-bottom: 1px solid #edf0f4;
            color: #364152;
            font-size: 12px;
            vertical-align: middle;
        }

        .courses-table tr:last-child td {
            border-bottom: none;
        }

        .courses-table tbody tr:hover {
            background: #fafbfc;
        }

        .course-title {
            color: #172033;
            font-weight: 600;
            font-size: 13px;
            max-width: 220px;
        }

        .course-field {
            color: #6f7a8c;
            margin-top: 4px;
            font-size: 11px;
        }

        .level-badge {
            display: inline-flex;
            padding: 5px 9px;
            border-radius: 20px;
            background: #eef2ff;
            color: #4338ca;
            font-size: 10px;
            font-weight: 700;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
        }

        .status-badge.active {
            background: #ecfdf3;
            color: #067647;
        }

        .status-badge.inactive {
            background: #f2f4f7;
            color: #667085;
        }

        .status-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: currentColor;
        }

        .actions {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .action-btn {
            width: 31px;
            height: 31px;
            border-radius: 7px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            transition: 0.2s ease;
        }

        .edit-btn {
            background: #eff6ff;
            color: #2563eb;
        }

        .edit-btn:hover {
            background: #dbeafe;
        }

        .delete-btn {
            background: #fff1f2;
            color: #dc2626;
        }

        .delete-btn:hover {
            background: #ffe4e6;
        }

        .empty-state {
            padding: 70px 20px;
            text-align: center;
        }

        .empty-state-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 15px;
            border-radius: 50%;
            background: #f2f4f7;
            color: #98a2b3;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .empty-state h3 {
            font-size: 16px;
            color: #344054;
            margin-bottom: 6px;
        }

        .empty-state p {
            color: #98a2b3;
            font-size: 12px;
            margin-bottom: 18px;
        }


        /* =========================
           MOBILE MENU
        ========================= */

        .mobile-menu-btn {
            display: none;
        }

        .sidebar-overlay {
            display: none;
        }


        @media (max-width: 650px) {

            .page-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .add-btn {
                width: 100%;
                justify-content: center;
            }


            /* MOBILE SIDEBAR */

            .sidebar {
                width: 255px;
                transform: translateX(-100%);
                transition: transform 0.25s ease;
                box-shadow: 8px 0 25px rgba(0, 0, 0, 0.15);
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .sidebar-brand {
                height: 82px;
                padding: 0 22px;
                justify-content: flex-start;
            }

            .sidebar-brand > div:last-child {
                display: block;
            }

            .sidebar-nav {
                padding: 22px 14px;
            }

            .nav-item {
                justify-content: flex-start;
                padding: 12px 14px;
            }

            .nav-item span {
                display: inline;
            }

            .sidebar-bottom {
                padding: 18px 14px;
            }

            .admin-user {
                justify-content: flex-start;
                padding: 5px 8px 16px;
            }

            .admin-user > div:last-child {
                display: block;
            }

            .logout-btn {
                display: flex;
            }


            /* MAIN */

            .main-content {
                width: 100%;
                margin-left: 0;
            }


            /* TOPBAR */

            .topbar {
                min-height: 70px;
                height: 70px;
                padding: 0 15px;
                gap: 12px;
            }

            .topbar h1 {
                font-size: 19px;
            }

            .topbar p {
                font-size: 11px;
            }

            .topbar-left {
                display: flex;
                align-items: center;
                gap: 12px;
                min-width: 0;
            }

            .topbar-left > div {
                min-width: 0;
            }

            .topbar-date {
                display: none;
            }


            /* HAMBURGER */

            .mobile-menu-btn {
                width: 38px;
                height: 38px;
                flex-shrink: 0;
                border: 1px solid #e1e5eb;
                border-radius: 8px;
                background: #ffffff;
                color: #172033;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                font-size: 16px;
            }

            .mobile-menu-btn:hover {
                background: #f5f7fb;
            }


            /* OVERLAY */

            .sidebar-overlay {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.35);
                z-index: 90;
                display: none;
            }

            .sidebar-overlay.active {
                display: block;
            }


            /* CONTENT */

            .content {
                padding: 20px 15px 35px;
            }

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


        <!-- TOPBAR -->

        <header class="topbar">

            <div class="topbar-left">

                <button
                    type="button"
                    class="mobile-menu-btn"
                    id="mobileMenuBtn"
                    aria-label="Open menu"
                >

                    <i class="fa-solid fa-bars"></i>

                </button>


                <div>

                    <h1>Courses</h1>

                    <p>
                        Manage all study programmes and courses.
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


            <!-- PAGE HEADER -->

            <div class="page-header">

                <div>

                    <h1>All Courses</h1>

                    <p>
                        Add, edit or remove study programmes.
                    </p>

                </div>


                <a href="add.php" class="add-btn">

                    <i class="fa-solid fa-plus"></i>

                    Add Course

                </a>

            </div>


            <!-- FLASH MESSAGES -->

            <?php if ($success): ?>

                <div class="alert success">

                    <i class="fa-solid fa-circle-check"></i>

                    <?= htmlspecialchars($success) ?>

                </div>

            <?php endif; ?>


            <?php if ($error): ?>

                <div class="alert error">

                    <i class="fa-solid fa-circle-exclamation"></i>

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>


            <!-- TABLE -->

            <div class="table-card">


                <div class="table-top">

                    <h2>Course Records</h2>

                    <span class="course-count">

                        <?= count($courses) ?> courses

                    </span>

                </div>


                <?php if (count($courses) > 0): ?>

                    <div class="table-wrapper">

                        <table class="courses-table">

                            <thead>

                                <tr>

                                    <th>#</th>

                                    <th>COURSE</th>

                                    <th>LEVEL</th>

                                    <th>COUNTRY</th>

                                    <th>LANGUAGE</th>

                                    <th>INTAKE</th>

                                    <th>STATUS</th>

                                    <th>ACTIONS</th>

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

                                            <?= htmlspecialchars($course["title"]) ?>

                                        </div>

                                        <div class="course-field">

                                            <?= htmlspecialchars($course["field"]) ?>

                                        </div>

                                    </td>


                                    <td>

                                        <span class="level-badge">

                                            <?= htmlspecialchars($course["level"]) ?>

                                        </span>

                                    </td>


                                    <td>

                                        <?= htmlspecialchars($course["country"]) ?>

                                    </td>


                                    <td>

                                        <?= htmlspecialchars($course["language"]) ?>

                                    </td>


                                    <td>

                                        <?= htmlspecialchars($course["intake"]) ?>

                                    </td>


                                    <td>

                                        <?php if ($course["status"] === "active"): ?>

                                            <span class="status-badge active">

                                                <span class="status-dot"></span>

                                                Active

                                            </span>

                                        <?php else: ?>

                                            <span class="status-badge inactive">

                                                <span class="status-dot"></span>

                                                Inactive

                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <td>

                                        <div class="actions">

                                            <a
                                                href="edit.php?id=<?= (int) $course["id"] ?>"
                                                class="action-btn edit-btn"
                                                title="Edit Course"
                                            >
                                                <i class="fa-solid fa-pen"></i>
                                            </a>


                                            <a
                                                href="delete.php?id=<?= (int) $course["id"] ?>"
                                                class="action-btn delete-btn"
                                                title="Delete Course"
                                                onclick="return confirm('Are you sure you want to delete this course?');"
                                            >
                                                <i class="fa-solid fa-trash"></i>
                                            </a>

                                        </div>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                <?php else: ?>

                    <div class="empty-state">

                        <div class="empty-state-icon">

                            <i class="fa-solid fa-book-open"></i>

                        </div>

                        <h3>No Courses Found</h3>

                        <p>
                            You haven't added any courses yet.
                        </p>

                        <a href="add.php" class="add-btn">

                            <i class="fa-solid fa-plus"></i>

                            Add First Course

                        </a>

                    </div>

                <?php endif; ?>


            </div>


        </section>

    </main>

</div>


<script>

    const mobileMenuBtn = document.getElementById("mobileMenuBtn");
    const sidebar = document.getElementById("sidebar");
    const sidebarOverlay = document.getElementById("sidebarOverlay");

    mobileMenuBtn.addEventListener("click", function () {

        sidebar.classList.toggle("mobile-open");
        sidebarOverlay.classList.toggle("active");

    });


    sidebarOverlay.addEventListener("click", function () {

        sidebar.classList.remove("mobile-open");
        sidebarOverlay.classList.remove("active");

    });


    document.querySelectorAll(".sidebar .nav-item").forEach(function (item) {

        item.addEventListener("click", function () {

            sidebar.classList.remove("mobile-open");
            sidebarOverlay.classList.remove("active");

        });

    });

</script>

</body>

</html>