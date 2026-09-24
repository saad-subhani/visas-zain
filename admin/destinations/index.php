<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . "/../../config/db.php";

$destinations = [];

try {

    $stmt = $pdo->query("
        SELECT *
        FROM destinations
        ORDER BY created_at DESC
    ");

    $destinations = $stmt->fetchAll();

} catch (PDOException $e) {

    $_SESSION["error"] = "Unable to load destinations.";

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

    <title>Destinations | Foreign Study Consultants</title>

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

        .destination-count {
            color: #7e8899;
            font-size: 12px;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .destinations-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 950px;
        }

        .destinations-table th {
            background: #f8f9fb;
            color: #687386;
            font-size: 11px;
            font-weight: 700;
            text-align: left;
            padding: 13px 16px;
            border-bottom: 1px solid #e8ebf0;
            white-space: nowrap;
        }

        .destinations-table td {
            padding: 15px 16px;
            border-bottom: 1px solid #edf0f4;
            color: #364152;
            font-size: 12px;
            vertical-align: middle;
        }

        .destinations-table tr:last-child td {
            border-bottom: none;
        }

        .destinations-table tbody tr:hover {
            background: #fafbfc;
        }

        .country-name {
            color: #172033;
            font-weight: 600;
            font-size: 13px;
        }

        .country-slug {
            color: #8a94a5;
            font-size: 10px;
            margin-top: 4px;
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

        .add-btn-small {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #172033;
            color: #ffffff;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
        }

        .mobile-menu-btn {
            display: none;
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 8px;
            background: #172033;
            color: #ffffff;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            cursor: pointer;
            flex-shrink: 0;
        }

        .sidebar-overlay {
            display: none;
        }

        @media (max-width: 768px) {

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
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .sidebar-overlay {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.35);
                z-index: 1000;
            }

            .sidebar-overlay.mobile-open {
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
                min-width: 0;
                flex: 1;
            }

            .topbar h1 {
                font-size: 19px;
            }

            .topbar p {
                font-size: 11px;
            }

            .topbar-right {
                flex-shrink: 0;
            }

            .topbar-date {
                font-size: 11px;
            }

            .content {
                width: 100%;
            }

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

            .table-top {
                padding: 15px;
            }

            .table-top h2 {
                font-size: 14px;
            }

            .destination-count {
                font-size: 11px;
            }

        }

        @media (max-width: 480px) {

            .topbar-date {
                display: none;
            }

            .mobile-menu-btn {
                width: 38px;
                height: 38px;
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
                id="mobileMenuBtn"
                onclick="toggleSidebar()"
                aria-label="Open menu"
            >
                <i class="fa-solid fa-bars"></i>
            </button>


            <div>

                <h1>Destinations</h1>

                <p>
                    Manage study abroad destinations.
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

                    <h1>All Destinations</h1>

                    <p>
                        Add, edit or remove study destinations.
                    </p>

                </div>


                <a href="add.php" class="add-btn">

                    <i class="fa-solid fa-plus"></i>

                    Add Destination

                </a>

            </div>


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


            <div class="table-card">


                <div class="table-top">

                    <h2>Destination Records</h2>

                    <span class="destination-count">

                        <?= count($destinations) ?> destinations

                    </span>

                </div>


                <?php if (count($destinations) > 0): ?>

                    <div class="table-wrapper">

                        <table class="destinations-table">

                            <thead>

                                <tr>

                                    <th>#</th>

                                    <th>COUNTRY</th>

                                    <th>TUITION RANGE</th>

                                    <th>LIVING COST</th>

                                    <th>VISA INFO</th>

                                    <th>STATUS</th>

                                    <th>ACTIONS</th>

                                </tr>

                            </thead>


                            <tbody>

                            <?php foreach ($destinations as $index => $destination): ?>

                                <tr>

                                    <td>
                                        <?= $index + 1 ?>
                                    </td>


                                    <td>

                                        <div class="country-name">

                                            <?= htmlspecialchars($destination["country_name"]) ?>

                                        </div>

                                        <div class="country-slug">

                                            /<?= htmlspecialchars($destination["slug"]) ?>

                                        </div>

                                    </td>


                                    <td>
                                        <?= htmlspecialchars($destination["tuition_range"]) ?>
                                    </td>


                                    <td>
                                        <?= htmlspecialchars($destination["living_cost"]) ?>
                                    </td>


                                    <td>

                                        <?php

                                        $visaInfo = $destination["visa_info"];

                                        if (strlen($visaInfo) > 45) {
                                            $visaInfo = substr($visaInfo, 0, 45) . "...";
                                        }

                                        echo htmlspecialchars($visaInfo);

                                        ?>

                                    </td>


                                    <td>

                                        <?php if ($destination["status"] === "Active"): ?>

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
                                                href="edit.php?id=<?= (int) $destination["id"] ?>"
                                                class="action-btn edit-btn"
                                                title="Edit Destination"
                                            >

                                                <i class="fa-solid fa-pen"></i>

                                            </a>


                                            <a
                                                href="delete.php?id=<?= (int) $destination["id"] ?>"
                                                class="action-btn delete-btn"
                                                title="Delete Destination"
                                                onclick="return confirm('Are you sure you want to delete this destination?');"
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

                            <i class="fa-solid fa-earth-americas"></i>

                        </div>

                        <h3>No Destinations Found</h3>

                        <p>
                            You haven't added any study destinations yet.
                        </p>

                        <a href="add.php" class="add-btn-small">

                            <i class="fa-solid fa-plus"></i>

                            Add First Destination

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
    const buttonIcon = document.querySelector("#mobileMenuBtn i");

    sidebar.classList.toggle("mobile-open");
    overlay.classList.toggle("mobile-open");

    if (sidebar.classList.contains("mobile-open")) {

        buttonIcon.classList.remove("fa-bars");
        buttonIcon.classList.add("fa-xmark");

    } else {

        buttonIcon.classList.remove("fa-xmark");
        buttonIcon.classList.add("fa-bars");

    }

}


function closeSidebar() {

    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebarOverlay");
    const buttonIcon = document.querySelector("#mobileMenuBtn i");

    sidebar.classList.remove("mobile-open");
    overlay.classList.remove("mobile-open");

    buttonIcon.classList.remove("fa-xmark");
    buttonIcon.classList.add("fa-bars");

}


document.querySelectorAll(".sidebar .nav-item").forEach(function (item) {

    item.addEventListener("click", function () {

        if (window.innerWidth <= 768) {
            closeSidebar();
        }

    });

});


window.addEventListener("resize", function () {

    if (window.innerWidth > 768) {
        closeSidebar();
    }

});

</script>

</body>

</html>