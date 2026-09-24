<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . "/../../config/db.php";

$universities = [];

try {

    $stmt = $pdo->query("
        SELECT *
        FROM universities
        ORDER BY created_at DESC
    ");

    $universities = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $_SESSION["error"] = "Unable to load universities.";

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

    <title>Universities | FSC Consultancy</title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <link
        rel="stylesheet"
        href="../../assets/css/admin.css"
    >

    <style>

        /* =========================================================
           PAGE
        ========================================================= */

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

        .add-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #102f52;
            color: #ffffff;
            padding: 11px 17px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            transition: 0.2s ease;
            text-decoration: none;
        }

        .add-btn:hover {
            background: #0b2745;
        }


        /* =========================================================
           ALERTS
        ========================================================= */

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


        /* =========================================================
           TABLE
        ========================================================= */

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

        .university-count {
            color: #7e8899;
            font-size: 12px;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .universities-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1100px;
        }

        .universities-table th {
            background: #f8f9fb;
            color: #687386;
            font-size: 11px;
            font-weight: 700;
            text-align: left;
            padding: 13px 16px;
            border-bottom: 1px solid #e8ebf0;
            white-space: nowrap;
        }

        .universities-table td {
            padding: 15px 16px;
            border-bottom: 1px solid #edf0f4;
            color: #364152;
            font-size: 12px;
            vertical-align: middle;
        }

        .universities-table tr:last-child td {
            border-bottom: none;
        }

        .universities-table tbody tr:hover {
            background: #fafbfc;
        }

        .university-name {
            color: #172033;
            font-weight: 600;
            font-size: 13px;
            max-width: 230px;
        }

        .university-city {
            color: #8a94a5;
            font-size: 10px;
            margin-top: 4px;
        }

        .country-name {
            color: #475467;
            font-size: 12px;
            font-weight: 500;
        }

        .programmes-text {
            max-width: 230px;
            color: #667085;
            line-height: 1.5;
        }


        /* =========================================================
           SCHOLARSHIP
        ========================================================= */

        .scholarship-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            background: #f0fdf4;
            color: #15803d;
        }

        .scholarship-badge.no {
            background: #f2f4f7;
            color: #667085;
        }


        /* =========================================================
           STATUS
        ========================================================= */

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


        /* =========================================================
           ACTIONS
        ========================================================= */

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
            text-decoration: none;
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


        /* =========================================================
           EMPTY STATE
        ========================================================= */

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

        .add-btn-small {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #102f52;
            color: #ffffff;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
        }

        .add-btn-small:hover {
            background: #0b2745;
        }


        /* =========================================================
           MOBILE
        ========================================================= */

        .mobile-header {
            display: none;
        }

        .mobile-menu-btn {
            display: none;
            width: 40px;
            height: 40px;
            border: 1px solid #e1e5eb;
            border-radius: 8px;
            background: #ffffff;
            color: #102f52;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 17px;
        }

        .sidebar-overlay {
            display: none;
        }


        /* =========================================================
           RESPONSIVE
        ========================================================= */

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

            .mobile-menu-btn {
                display: inline-flex;
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

            .page-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .add-btn {
                width: 100%;
                justify-content: center;
                box-sizing: border-box;
            }

            .table-top {
                padding: 15px;
            }

            .universities-table {
                min-width: 1100px;
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

    <!-- =========================================================
         SIDEBAR OVERLAY
    ========================================================== -->

    <div
        class="sidebar-overlay"
        id="sidebarOverlay"
        onclick="closeSidebar()"
    ></div>


    <!-- =========================================================
         SIDEBAR - EXACT DASHBOARD STYLE
    ========================================================== -->

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
                href="../dashboard.php"
                class="nav-item"
            >
                <i class="fa-solid fa-chart-line"></i>
                <span>Dashboard</span>
            </a>


            <a
                href="../courses/index.php"
                class="nav-item"
            >
                <i class="fa-solid fa-book-open"></i>
                <span>Courses</span>
            </a>


            <a
                href="../destinations/index.php"
                class="nav-item"
            >
                <i class="fa-solid fa-earth-americas"></i>
                <span>Destinations</span>
            </a>


            <a
                href="index.php"
                class="nav-item active"
            >
                <i class="fa-solid fa-building-columns"></i>
                <span>Universities</span>
            </a>


            <a
                href="../scholarships/index.php"
                class="nav-item"
            >
                <i class="fa-solid fa-award"></i>
                <span>Scholarships</span>
            </a>


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


    <!-- =========================================================
         MAIN
    ========================================================== -->

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

                <h1>Universities</h1>

                <p>
                    Manage partner universities and institutions.
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


            <div class="page-header">

                <div>

                    <h1>All Universities</h1>

                    <p>
                        Add, edit or remove university records.
                    </p>

                </div>


                <a href="add.php" class="add-btn">

                    <i class="fa-solid fa-plus"></i>

                    Add University

                </a>

            </div>


            <!-- FLASH MESSAGE -->

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

                    <h2>University Records</h2>

                    <span class="university-count">

                        <?= count($universities) ?> universities

                    </span>

                </div>


                <?php if (count($universities) > 0): ?>

                    <div class="table-wrapper">

                        <table class="universities-table">

                            <thead>

                                <tr>

                                    <th>#</th>
                                    <th>UNIVERSITY</th>
                                    <th>COUNTRY</th>
                                    <th>PROGRAMMES</th>
                                    <th>TUITION FEE</th>
                                    <th>INTAKE DATES</th>
                                    <th>SCHOLARSHIPS</th>
                                    <th>STATUS</th>
                                    <th>ACTIONS</th>

                                </tr>

                            </thead>


                            <tbody>

                            <?php foreach ($universities as $index => $university): ?>

                                <tr>

                                    <td>
                                        <?= $index + 1 ?>
                                    </td>


                                    <td>

                                        <div class="university-name">

                                            <?= htmlspecialchars(
                                                $university["name"] ?? ""
                                            ) ?>

                                        </div>


                                        <div class="university-city">

                                            <i class="fa-solid fa-location-dot"></i>

                                            <?= htmlspecialchars(
                                                $university["city"] ?? ""
                                            ) ?>

                                        </div>

                                    </td>


                                    <td>

                                        <div class="country-name">

                                            <?= htmlspecialchars(
                                                $university["country"] ?? ""
                                            ) ?>

                                        </div>

                                    </td>


                                    <td>

                                        <div class="programmes-text">

                                            <?= htmlspecialchars(
                                                $university["programmes"] ?? ""
                                            ) ?>

                                        </div>

                                    </td>


                                    <td>

                                        <?= htmlspecialchars(
                                            $university["tuition_fee"] ?? ""
                                        ) ?>

                                    </td>


                                    <td>

                                        <?= htmlspecialchars(
                                            $university["intake_dates"] ?? ""
                                        ) ?>

                                    </td>


                                    <td>

                                        <?php if (
                                            ($university["scholarships_available"] ?? "") === "Yes"
                                        ): ?>

                                            <span class="scholarship-badge">

                                                <i class="fa-solid fa-check"></i>

                                                Available

                                            </span>

                                        <?php else: ?>

                                            <span class="scholarship-badge no">

                                                <i class="fa-solid fa-minus"></i>

                                                Not Available

                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <td>

                                        <?php if (
                                            ($university["status"] ?? "") === "Active"
                                        ): ?>

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
                                                href="edit.php?id=<?= (int) $university["id"] ?>"
                                                class="action-btn edit-btn"
                                                title="Edit University"
                                            >
                                                <i class="fa-solid fa-pen"></i>
                                            </a>


                                            <a
                                                href="delete.php?id=<?= (int) $university["id"] ?>"
                                                class="action-btn delete-btn"
                                                title="Delete University"
                                                onclick="return confirm('Are you sure you want to delete this university?');"
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

                            <i class="fa-solid fa-building-columns"></i>

                        </div>

                        <h3>No Universities Found</h3>

                        <p>
                            You haven't added any universities yet.
                        </p>

                        <a href="add.php" class="add-btn-small">

                            <i class="fa-solid fa-plus"></i>

                            Add First University

                        </a>

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