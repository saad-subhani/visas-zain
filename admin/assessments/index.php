<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . "/../../config/db.php";

try {
    $stmt = $pdo->query("
        SELECT *
        FROM assessments
        ORDER BY id DESC
    ");

    $assessments = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $assessments = [];
    $error = "Unable to load assessments.";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Free Assessments</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f8fafc;
            color: #0f172a;
        }

        .container {
            width: 95%;
            max-width: 1400px;
            margin: 40px auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            gap: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 30px;
        }

        .header p {
            margin: 7px 0 0;
            color: #64748b;
            font-size: 14px;
        }

        .back-btn {
            text-decoration: none;
            background: #0f172a;
            color: white;
            padding: 11px 18px;
            border-radius: 8px;
            font-size: 13px;
        }

        .back-btn:hover {
            background: #6d4aff;
        }

        .table-wrapper {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            overflow-x: auto;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }

        th,
        td {
            padding: 16px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
            font-size: 13px;
        }

        th {
            background: #f8fafc;
            color: #475569;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: #fafaff;
        }

        .name {
            font-weight: 700;
            color: #0f172a;
        }

        .email {
            color: #64748b;
        }

        .badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            background: #ede9fe;
            color: #6d4aff;
            font-size: 11px;
            font-weight: 700;
        }

        .view-btn {
            display: inline-block;
            padding: 8px 13px;
            background: #0f172a;
            color: white;
            text-decoration: none;
            border-radius: 7px;
            font-size: 11px;
        }

        .view-btn:hover {
            background: #6d4aff;
        }

        .empty {
            padding: 60px 20px;
            text-align: center;
            color: #64748b;
        }

        .error {
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            background: #fee2e2;
            color: #b91c1c;
        }

        @media (max-width: 700px) {
            .header {
                align-items: flex-start;
                flex-direction: column;
            }

            .header h1 {
                font-size: 24px;
            }

            .back-btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <div class="header">

        <div>
            <h1>Free Assessments</h1>

            <p>
                View students who submitted the free eligibility assessment.
            </p>
        </div>

        <a href="../dashboard.php" class="back-btn">
            ← Dashboard
        </a>

    </div>

    <?php if (isset($error)): ?>

        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>

    <div class="table-wrapper">

        <?php if (count($assessments) > 0): ?>

            <table>

                <thead>

                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>Email</th>
                        <th>WhatsApp</th>
                        <th>Qualification</th>
                        <th>Destination</th>
                        <th>Study Level</th>
                        <th>Action</th>
                    </tr>

                </thead>

                <tbody>

                <?php foreach ($assessments as $assessment): ?>

                    <tr>

                        <td>
                            <?= htmlspecialchars($assessment["id"]) ?>
                        </td>

                        <td>
                            <div class="name">
                                <?= htmlspecialchars($assessment["name"]) ?>
                            </div>
                        </td>

                        <td>
                            <div class="email">
                                <?= htmlspecialchars($assessment["email"]) ?>
                            </div>
                        </td>

                        <td>
                            <?= htmlspecialchars($assessment["whatsapp"]) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($assessment["qualification"]) ?>
                        </td>

                        <td>

                            <span class="badge">
                                <?= htmlspecialchars($assessment["destination"]) ?>
                            </span>

                        </td>

                        <td>
                            <?= htmlspecialchars($assessment["study_level"]) ?>
                        </td>

                        <td>

                            <a
                                href="view.php?id=<?= urlencode($assessment["id"]) ?>"
                                class="view-btn"
                            >
                                View Details
                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        <?php else: ?>

            <div class="empty">

                <h3>No assessments found</h3>

                <p>
                    Submitted free assessments will appear here.
                </p>

            </div>

        <?php endif; ?>

    </div>

</div>

</body>

</html>