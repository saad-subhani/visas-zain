<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . "/../../config/db.php";

$messages = [];
$error = "";

try {

    $stmt = $pdo->query("
        SELECT
            id,
            name,
            email,
            phone,
            subject,
            message,
            status,
            created_at
        FROM contact_messages
        ORDER BY created_at DESC
    ");

    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $error = "Unable to load contact messages.";

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

    <title>Contact Messages | FSC Consultancy</title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <link rel="stylesheet" href="../../assets/css/admin.css">

    <style>

        .messages-card {
            background: #ffffff;
            border: 1px solid #e7ebf0;
            border-radius: 14px;
            overflow: hidden;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .messages-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1100px;
        }

        .messages-table th {
            background: #f7f9fb;
            color: #667085;
            font-size: 12px;
            font-weight: 700;
            text-align: left;
            padding: 15px 18px;
            border-bottom: 1px solid #e7ebf0;
            white-space: nowrap;
        }

        .messages-table td {
            padding: 16px 18px;
            border-bottom: 1px solid #edf0f3;
            color: #273247;
            font-size: 13px;
            vertical-align: middle;
        }

        .messages-table tbody tr:hover {
            background: #fafbfc;
        }

        .messages-table tbody tr:last-child td {
            border-bottom: none;
        }

        .name-cell {
            font-weight: 600;
            color: #172033;
        }

        .email-cell {
            color: #536174;
        }

        .subject-cell {
            font-weight: 600;
            color: #273247;
        }

        .message-cell {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .date-cell {
            white-space: nowrap;
            color: #7b8494;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
        }

        .status-new {
            background: #fff4e5;
            color: #b76e00;
        }

        .status-read {
            background: #eaf8ef;
            color: #16803c;
        }

        .status-replied {
            background: #eef4ff;
            color: #315edb;
        }

        .action-buttons {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .action-btn {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 12px;
        }

        .view-btn {
            background: #eef4ff;
            color: #315edb;
        }

        .view-btn:hover {
            background: #dfeaff;
        }

        .delete-btn {
            background: #fff1f1;
            color: #c62828;
        }

        .delete-btn:hover {
            background: #ffe2e2;
        }

        .empty-state {
            text-align: center;
            padding: 70px 20px;
        }

        .empty-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 18px;
            border-radius: 14px;
            background: #f1f4f8;
            color: #7b8494;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
        }

        .empty-state h3 {
            margin: 0 0 8px;
            color: #172033;
            font-size: 18px;
        }

        .empty-state p {
            margin: 0;
            color: #8a94a5;
            font-size: 13px;
        }

        .success-alert {
            margin-bottom: 20px;
            padding: 13px 16px;
            border-radius: 9px;
            background: #eaf8ef;
            color: #16803c;
            font-size: 13px;
        }

        .error-alert {
            margin-bottom: 20px;
            padding: 13px 16px;
            border-radius: 9px;
            background: #fff1f1;
            color: #c62828;
            font-size: 13px;
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
                display: none;
            }

            .sidebar-overlay.show {
                display: block;
            }

            .main-content {
                width: 100%;
                margin-left: 0;
            }

            .mobile-header {
                display: flex !important;
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
                width: 40px;
                height: 40px;
                border: 1px solid #e1e5eb;
                border-radius: 8px;
                background: #ffffff;
                color: #172033;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 17px;
                cursor: pointer;
            }

        }

        @media (min-width: 901px) {

            .mobile-header {
                display: none !important;
            }

            .sidebar-overlay {
                display: none !important;
            }

        }

    </style>

</head>

<body>

<div class="admin-layout">

    <!-- EXACT DASHBOARD SIDEBAR -->

    <aside class="sidebar" id="sidebar">

        <div class="sidebar-brand">

            <div class="brand-icon">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>

            <div>
                <h2>FSC</h2>
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

            <a href="../universities/index.php" class="nav-item">
                <i class="fa-solid fa-building-columns"></i>
                <span>Universities</span>
            </a>

            <a href="../scholarships/index.php" class="nav-item">
                <i class="fa-solid fa-award"></i>
                <span>Scholarships</span>
            </a>

            <a href="index.php" class="nav-item active">
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


            <a href="../change-password.php" class="change-password-btn">
                <i class="fa-solid fa-key"></i>
                <span>Change Password</span>
            </a>


            <a href="../logout.php" class="logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i>
                Logout
            </a>

        </div>

    </aside>


    <div
        class="sidebar-overlay"
        id="sidebarOverlay"
        onclick="closeSidebar()"
    ></div>


    <!-- MAIN -->

    <main class="main-content">

        <div class="mobile-header">

            <div>

                <strong style="color:#172033;">
                    FSC
                </strong>

                <span
                    style="
                        display:block;
                        font-size:10px;
                        color:#8a94a5;
                    "
                >
                    Consultancy
                </span>

            </div>

            <button
                type="button"
                class="mobile-menu-btn"
                onclick="toggleSidebar()"
            >
                <i class="fa-solid fa-bars"></i>
            </button>

        </div>


        <header class="topbar">

            <div>

                <h1>Contact Messages</h1>

                <p>
                    View messages submitted through your website contact form.
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

            <?php if (isset($_GET["deleted"])): ?>

                <div class="success-alert">
                    <i class="fa-solid fa-circle-check"></i>
                    Contact message deleted successfully.
                </div>

            <?php endif; ?>


            <?php if ($error !== ""): ?>

                <div class="error-alert">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?= htmlspecialchars($error) ?>
                </div>

            <?php endif; ?>


            <div class="section-header">

                <div>

                    <h2>Submitted Messages</h2>

                    <p>
                        <?= count($messages) ?> message(s) received.
                    </p>

                </div>

            </div>


            <div class="messages-card">

                <?php if (count($messages) > 0): ?>

                    <div class="table-wrapper">

                        <table class="messages-table">

                            <thead>

                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Subject</th>
                                    <th>Message</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>

                            </thead>


                            <tbody>

                            <?php foreach ($messages as $message): ?>

                                <tr>

                                    <td>
                                        <?= (int) $message["id"] ?>
                                    </td>

                                    <td class="name-cell">
                                        <?= htmlspecialchars($message["name"]) ?>
                                    </td>

                                    <td class="email-cell">
                                        <?= htmlspecialchars($message["email"]) ?>
                                    </td>

                                    <td>
                                        <?= $message["phone"]
                                            ? htmlspecialchars($message["phone"])
                                            : "—"
                                        ?>
                                    </td>

                                    <td class="subject-cell">
                                        <?= $message["subject"]
                                            ? htmlspecialchars($message["subject"])
                                            : "—"
                                        ?>
                                    </td>

                                    <td
                                        class="message-cell"
                                        title="<?= htmlspecialchars($message["message"]) ?>"
                                    >
                                        <?= htmlspecialchars($message["message"]) ?>
                                    </td>

                                    <td>

                                        <?php if ($message["status"] === "New"): ?>

                                            <span class="status-badge status-new">
                                                <i class="fa-solid fa-envelope"></i>
                                                New
                                            </span>

                                        <?php elseif ($message["status"] === "Read"): ?>

                                            <span class="status-badge status-read">
                                                <i class="fa-solid fa-check"></i>
                                                Read
                                            </span>

                                        <?php else: ?>

                                            <span class="status-badge status-replied">
                                                <i class="fa-solid fa-reply"></i>
                                                Replied
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                    <td class="date-cell">

                                        <?= date(
                                            "d M Y, h:i A",
                                            strtotime($message["created_at"])
                                        ) ?>

                                    </td>

                                    <td>

                                        <div class="action-buttons">

                                            <a
                                                href="view.php?id=<?= (int) $message["id"] ?>"
                                                class="action-btn view-btn"
                                                title="View Message"
                                            >
                                                <i class="fa-solid fa-eye"></i>
                                            </a>


                                            <form
                                                action="delete.php"
                                                method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this message?');"
                                                style="margin:0;"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?= (int) $message["id"] ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    class="action-btn delete-btn"
                                                    title="Delete Message"
                                                >
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                <?php else: ?>

                    <div class="empty-state">

                        <div class="empty-icon">
                            <i class="fa-solid fa-envelope-open"></i>
                        </div>

                        <h3>No Contact Messages</h3>

                        <p>
                            Messages submitted through the website will appear here.
                        </p>

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

window.addEventListener("resize", function () {

    if (window.innerWidth > 900) {
        closeSidebar();
    }

});

</script>

</body>

</html>