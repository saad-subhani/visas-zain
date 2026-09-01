<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . "/../../config/db.php";

$id = isset($_GET["id"])
    ? (int) $_GET["id"]
    : 0;

$message = null;
$error = "";

if ($id <= 0) {

    $error = "Invalid message ID.";

} else {

    try {

        $stmt = $pdo->prepare("
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
            WHERE id = :id
            LIMIT 1
        ");

        $stmt->execute([
            ":id" => $id
        ]);

        $message = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$message) {
            $error = "Contact message not found.";
        } else {

            if ($message["status"] !== "read") {

                $update = $pdo->prepare("
                    UPDATE contact_messages
                    SET status = 'read'
                    WHERE id = :id
                ");

                $update->execute([
                    ":id" => $id
                ]);

                $message["status"] = "read";
            }
        }

    } catch (PDOException $e) {

        $error = "Unable to load contact message.";

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

    <title>View Contact Message | Edworldly Consultancy</title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <link rel="stylesheet" href="../../assets/css/admin.css">

    <style>

        .message-view-card {
            max-width: 900px;
            background: #ffffff;
            border: 1px solid #e7ebf0;
            border-radius: 14px;
            padding: 30px;
        }

        .message-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding-bottom: 22px;
            border-bottom: 1px solid #edf0f3;
        }

        .message-header h2 {
            margin: 0;
            color: #172033;
            font-size: 22px;
        }

        .message-header p {
            margin: 6px 0 0;
            color: #8a94a5;
            font-size: 12px;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 8px;
            background: #f1f4f8;
            color: #172033;
            text-decoration: none;
            font-size: 12px;
            font-weight: 700;
        }

        .back-btn:hover {
            background: #e8edf3;
        }

        .message-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 25px;
        }

        .info-box {
            background: #f8fafc;
            border: 1px solid #e7ebf0;
            border-radius: 10px;
            padding: 18px;
        }

        .info-label {
            display: block;
            margin-bottom: 7px;
            color: #8a94a5;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .info-value {
            color: #172033;
            font-size: 14px;
            word-break: break-word;
        }

        .full-width {
            grid-column: 1 / -1;
        }

        .message-content {
            white-space: pre-wrap;
            line-height: 1.8;
        }

        .status {
            display: inline-flex;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
        }

        .status-read {
            background: #eaf8ef;
            color: #16803c;
        }

        .delete-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 25px;
            padding: 11px 17px;
            border: none;
            border-radius: 8px;
            background: #fff1f1;
            color: #c62828;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
        }

        .delete-btn:hover {
            background: #ffe2e2;
        }

        .error-box {
            max-width: 700px;
            padding: 20px;
            border-radius: 10px;
            background: #fff1f1;
            color: #c62828;
        }

        @media (max-width: 650px) {

            .message-view-card {
                padding: 20px;
            }

            .message-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .message-grid {
                grid-template-columns: 1fr;
            }

            .full-width {
                grid-column: auto;
            }

        }

    </style>

</head>

<body>

<div class="admin-layout">

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
                        <?= htmlspecialchars($_SESSION["admin_username"]) ?>
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


    <main class="main-content">

        <header class="topbar">

            <div>

                <h1>View Contact Message</h1>

                <p>
                    Read the complete enquiry submitted through the website.
                </p>

            </div>

        </header>


        <section class="content">

            <?php if ($error !== ""): ?>

                <div class="error-box">
                    <?= htmlspecialchars($error) ?>
                </div>

            <?php elseif ($message): ?>

                <div class="message-view-card">

                    <div class="message-header">

                        <div>

                            <h2>
                                <?= htmlspecialchars(
                                    $message["subject"] ?: "Contact Enquiry"
                                ) ?>
                            </h2>

                            <p>
                                Received on
                                <?= date(
                                    "d M Y, h:i A",
                                    strtotime($message["created_at"])
                                ) ?>
                            </p>

                        </div>

                        <a
                            href="index.php"
                            class="back-btn"
                        >
                            <i class="fa-solid fa-arrow-left"></i>
                            Back
                        </a>

                    </div>


                    <div class="message-grid">

                        <div class="info-box">

                            <span class="info-label">
                                Name
                            </span>

                            <div class="info-value">
                                <?= htmlspecialchars($message["name"]) ?>
                            </div>

                        </div>


                        <div class="info-box">

                            <span class="info-label">
                                Email
                            </span>

                            <div class="info-value">
                                <?= htmlspecialchars($message["email"]) ?>
                            </div>

                        </div>


                        <div class="info-box">

                            <span class="info-label">
                                Phone
                            </span>

                            <div class="info-value">
                                <?= $message["phone"]
                                    ? htmlspecialchars($message["phone"])
                                    : "—"
                                ?>
                            </div>

                        </div>


                        <div class="info-box">

                            <span class="info-label">
                                Status
                            </span>

                            <div class="info-value">

                                <span class="status status-read">
                                    Read
                                </span>

                            </div>

                        </div>


                        <div class="info-box full-width">

                            <span class="info-label">
                                Subject
                            </span>

                            <div class="info-value">
                                <?= $message["subject"]
                                    ? htmlspecialchars($message["subject"])
                                    : "—"
                                ?>
                            </div>

                        </div>


                        <div class="info-box full-width">

                            <span class="info-label">
                                Message
                            </span>

                            <div class="info-value message-content">
                                <?= htmlspecialchars($message["message"]) ?>
                            </div>

                        </div>

                    </div>


                    <form
                        action="delete.php"
                        method="POST"
                        onsubmit="return confirm('Are you sure you want to delete this message?');"
                    >

                        <input
                            type="hidden"
                            name="id"
                            value="<?= (int) $message["id"] ?>"
                        >

                        <button
                            type="submit"
                            class="delete-btn"
                        >

                            <i class="fa-solid fa-trash"></i>

                            Delete Message

                        </button>

                    </form>

                </div>

            <?php endif; ?>

        </section>

    </main>

</div>

</body>

</html>