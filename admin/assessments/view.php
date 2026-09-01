<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . "/../../config/db.php";

$id = $_GET["id"] ?? null;

if (!$id || !is_numeric($id)) {
    header("Location: index.php");
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT *
        FROM assessments
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->execute([$id]);

    $assessment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$assessment) {
        header("Location: index.php");
        exit;
    }

} catch (PDOException $e) {
    die("Unable to load assessment.");
}

function e($value)
{
    return htmlspecialchars($value ?? "", ENT_QUOTES, "UTF-8");
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

    <title>
        Assessment Details
    </title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f8fafc;
            color: #0f172a;
            font-family: Arial, sans-serif;
        }

        .container {
            width: 94%;
            max-width: 1100px;
            margin: 40px auto;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 25px;
        }

        .topbar h1 {
            margin: 0;
            font-size: 28px;
        }

        .topbar p {
            margin: 7px 0 0;
            color: #64748b;
            font-size: 13px;
        }

        .back-btn {
            display: inline-block;
            padding: 11px 18px;
            border-radius: 8px;
            background: #0f172a;
            color: white;
            text-decoration: none;
            font-size: 13px;
        }

        .back-btn:hover {
            background: #6d4aff;
        }

        .card {
            margin-bottom: 20px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: white;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
        }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e2e8f0;
            background: #fafbff;
        }

        .card-header span {
            display: block;
            margin-bottom: 7px;
            color: #6d4aff;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .card-header h2 {
            margin: 0;
            font-size: 20px;
        }

        .details {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .detail {
            padding: 20px 24px;
            border-bottom: 1px solid #eef2f7;
        }

        .detail:nth-child(odd) {
            border-right: 1px solid #eef2f7;
        }

        .label {
            display: block;
            margin-bottom: 8px;
            color: #94a3b8;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        .value {
            color: #0f172a;
            font-size: 14px;
            font-weight: 600;
            line-height: 1.5;
            word-break: break-word;
        }

        .badge {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 999px;
            background: #ede9fe;
            color: #6d4aff;
            font-size: 11px;
            font-weight: 700;
        }

        .footer-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-top: 25px;
        }

        .student-id {
            color: #94a3b8;
            font-size: 11px;
        }

        .print-btn {
            border: none;
            cursor: pointer;
            padding: 11px 18px;
            border-radius: 8px;
            background: #6d4aff;
            color: white;
            font-size: 13px;
            font-weight: 600;
        }

        .print-btn:hover {
            background: #5938e8;
        }

        @media (max-width: 700px) {

            .container {
                width: 92%;
                margin: 25px auto;
            }

            .topbar {
                align-items: flex-start;
                flex-direction: column;
            }

            .topbar h1 {
                font-size: 24px;
            }

            .back-btn {
                width: 100%;
                text-align: center;
            }

            .details {
                grid-template-columns: 1fr;
            }

            .detail:nth-child(odd) {
                border-right: none;
            }

            .footer-actions {
                align-items: stretch;
                flex-direction: column;
            }

            .print-btn {
                width: 100%;
            }

        }

        @media print {

            body {
                background: white;
            }

            .container {
                width: 100%;
                max-width: none;
                margin: 0;
            }

            .back-btn,
            .print-btn {
                display: none;
            }

            .card {
                box-shadow: none;
                break-inside: avoid;
            }

        }

    </style>

</head>

<body>

<div class="container">

    <!-- TOP BAR -->

    <div class="topbar">

        <div>

            <h1>
                Assessment Details
            </h1>

            <p>
                Complete student eligibility assessment profile.
            </p>

        </div>

        <a
            href="index.php"
            class="back-btn"
        >
            ← Back to Assessments
        </a>

    </div>


    <!-- STEP 1 -->

    <div class="card">

        <div class="card-header">

            <span>
                Step 01
            </span>

            <h2>
                About You
            </h2>

        </div>

        <div class="details">

            <div class="detail">

                <span class="label">
                    Full Name
                </span>

                <div class="value">
                    <?= e($assessment["name"]) ?>
                </div>

            </div>


            <div class="detail">

                <span class="label">
                    WhatsApp Number
                </span>

                <div class="value">
                    <?= e($assessment["whatsapp"]) ?>
                </div>

            </div>


            <div class="detail">

                <span class="label">
                    Email Address
                </span>

                <div class="value">
                    <?= e($assessment["email"]) ?>
                </div>

            </div>


            <div class="detail">

                <span class="label">
                    Nationality / Current Country
                </span>

                <div class="value">
                    <?= e($assessment["country"]) ?>
                </div>

            </div>

        </div>

    </div>


    <!-- STEP 2 -->

    <div class="card">

        <div class="card-header">

            <span>
                Step 02
            </span>

            <h2>
                Academic Profile
            </h2>

        </div>

        <div class="details">

            <div class="detail">

                <span class="label">
                    Highest Qualification
                </span>

                <div class="value">
                    <?= e($assessment["qualification"]) ?>
                </div>

            </div>


            <div class="detail">

                <span class="label">
                    Field of Study
                </span>

                <div class="value">
                    <?= e($assessment["field"]) ?>
                </div>

            </div>


            <div class="detail">

                <span class="label">
                    CGPA / Percentage
                </span>

                <div class="value">
                    <?= e($assessment["percentage"]) ?>
                </div>

            </div>


            <div class="detail">

                <span class="label">
                    Graduation Year
                </span>

                <div class="value">
                    <?= e($assessment["graduation_year"]) ?>
                </div>

            </div>


            <div class="detail">

                <span class="label">
                    English Test
                </span>

                <div class="value">
                    <?= e($assessment["english_test"]) ?>
                </div>

            </div>

        </div>

    </div>


    <!-- STEP 3 -->

    <div class="card">

        <div class="card-header">

            <span>
                Step 03
            </span>

            <h2>
                Study Preferences
            </h2>

        </div>

        <div class="details">

            <div class="detail">

                <span class="label">
                    Preferred Destination
                </span>

                <div class="value">

                    <span class="badge">
                        <?= e($assessment["destination"]) ?>
                    </span>

                </div>

            </div>


            <div class="detail">

                <span class="label">
                    Preferred Study Level
                </span>

                <div class="value">
                    <?= e($assessment["study_level"]) ?>
                </div>

            </div>


            <div class="detail">

                <span class="label">
                    Preferred Intake
                </span>

                <div class="value">
                    <?= e($assessment["intake"]) ?>
                </div>

            </div>


            <div class="detail">

                <span class="label">
                    Estimated Budget
                </span>

                <div class="value">
                    <?= e($assessment["budget"]) ?>
                </div>

            </div>


            <div class="detail">

                <span class="label">
                    Interested Field
                </span>

                <div class="value">
                    <?= e($assessment["interested_field"]) ?>
                </div>

            </div>

        </div>

    </div>


    <!-- ACTIONS -->

    <div class="footer-actions">

        <div class="student-id">

            Assessment ID:
            #<?= e($assessment["id"]) ?>

        </div>

        <button
            type="button"
            onclick="window.print()"
            class="print-btn"
        >
            Print Assessment
        </button>

    </div>

</div>

</body>

</html>