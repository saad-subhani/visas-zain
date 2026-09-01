<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . "/../../config/db.php";

$errors = [];

$title = "";
$description = "";
$amount = "";
$deadline = "";
$eligible_countries = "";
$study_level = "";
$requirements = "";
$official_source = "";
$how_to_apply = "";
$status = "active";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $amount = trim($_POST["amount"] ?? "");
    $deadline = trim($_POST["deadline"] ?? "");
    $eligible_countries = trim($_POST["eligible_countries"] ?? "");
    $study_level = trim($_POST["study_level"] ?? "");
    $requirements = trim($_POST["requirements"] ?? "");
    $official_source = trim($_POST["official_source"] ?? "");
    $how_to_apply = trim($_POST["how_to_apply"] ?? "");
    $status = $_POST["status"] ?? "active";

    if ($title === "") {
        $errors[] = "Scholarship title is required.";
    }

    if ($description === "") {
        $errors[] = "Description is required.";
    }

    if ($amount === "") {
        $errors[] = "Scholarship amount is required.";
    }

    if ($deadline === "") {
        $errors[] = "Deadline is required.";
    }

    if ($eligible_countries === "") {
        $errors[] = "Eligible countries are required.";
    }

    if ($study_level === "") {
        $errors[] = "Study level is required.";
    }

    if ($requirements === "") {
        $errors[] = "Requirements are required.";
    }

    if ($official_source === "") {
        $errors[] = "Official source is required.";
    }

    if ($how_to_apply === "") {
        $errors[] = "How to apply information is required.";
    }

    if (!in_array($status, ["active", "inactive"], true)) {
        $errors[] = "Invalid status selected.";
    }

    if (empty($errors)) {

        try {

            $stmt = $pdo->prepare("
                INSERT INTO scholarships
                (
                    title,
                    description,
                    amount,
                    deadline,
                    eligible_countries,
                    study_level,
                    requirements,
                    official_source,
                    how_to_apply,
                    status
                )
                VALUES
                (
                    :title,
                    :description,
                    :amount,
                    :deadline,
                    :eligible_countries,
                    :study_level,
                    :requirements,
                    :official_source,
                    :how_to_apply,
                    :status
                )
            ");

            $stmt->execute([
                ":title" => $title,
                ":description" => $description,
                ":amount" => $amount,
                ":deadline" => $deadline,
                ":eligible_countries" => $eligible_countries,
                ":study_level" => $study_level,
                ":requirements" => $requirements,
                ":official_source" => $official_source,
                ":how_to_apply" => $how_to_apply,
                ":status" => $status
            ]);

            $_SESSION["success"] = "Scholarship added successfully.";

            header("Location: index.php");
            exit;

        } catch (PDOException $e) {

            $errors[] = "Unable to add scholarship. Please try again.";

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

    <title>Add Scholarship | Edworldly Consultancy</title>

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

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 15px;
            border: 1px solid #dfe3e9;
            background: #ffffff;
            color: #475467;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
        }

        .back-btn:hover {
            background: #f8f9fb;
        }

        .form-card {
            background: #ffffff;
            border: 1px solid #e8ebf0;
            border-radius: 12px;
            padding: 28px;
        }

        .form-section {
            margin-bottom: 30px;
        }

        .form-section:last-child {
            margin-bottom: 0;
        }

        .form-section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 14px;
            margin-bottom: 20px;
            border-bottom: 1px solid #edf0f4;
        }

        .form-section-title i {
            width: 32px;
            height: 32px;
            border-radius: 7px;
            background: #f0f2f6;
            color: #172033;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
        }

        .form-section-title h2 {
            font-size: 15px;
            color: #172033;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-group label {
            font-size: 12px;
            font-weight: 600;
            color: #344054;
            margin-bottom: 8px;
        }

        .required {
            color: #d92d20;
        }

        .form-control {
            width: 100%;
            min-height: 45px;
            border: 1px solid #dfe3e9;
            border-radius: 8px;
            padding: 10px 13px;
            font-family: inherit;
            font-size: 13px;
            color: #344054;
            background: #ffffff;
            outline: none;
            transition: 0.2s ease;
            box-sizing: border-box;
        }

        .form-control:focus {
            border-color: #172033;
            box-shadow: 0 0 0 3px rgba(23, 32, 51, 0.07);
        }

        textarea.form-control {
            min-height: 115px;
            resize: vertical;
            line-height: 1.6;
        }

        select.form-control {
            cursor: pointer;
        }

        .help-text {
            margin-top: 6px;
            color: #98a2b3;
            font-size: 11px;
        }

        .error-box {
            background: #fff1f1;
            border: 1px solid #ffd6d3;
            color: #b42318;
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 22px;
        }

        .error-box-title {
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 7px;
        }

        .error-box ul {
            padding-left: 20px;
            margin: 0;
        }

        .error-box li {
            font-size: 12px;
            margin: 4px 0;
        }

        .form-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            padding-top: 22px;
            border-top: 1px solid #edf0f4;
        }

        .cancel-btn {
            padding: 11px 18px;
            border-radius: 8px;
            border: 1px solid #dfe3e9;
            background: #ffffff;
            color: #475467;
            font-size: 13px;
            font-weight: 600;
        }

        .save-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 19px;
            border-radius: 8px;
            border: none;
            background: #172033;
            color: #ffffff;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        .save-btn:hover {
            background: #27344d;
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

            .page-header {
                align-items: flex-start;
                flex-direction: column;
            }

        }

    </style>

</head>

<body>

<div class="admin-layout">

    <!-- SIDEBAR -->

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


            <a href="index.php" class="nav-item active">

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


    <!-- MAIN -->

    <main class="main-content">

        <header class="topbar">

            <div>

                <h1>Add Scholarship</h1>

                <p>
                    Create a new scholarship opportunity.
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

                    <h1>Scholarship Information</h1>

                    <p>
                        Enter complete information about the scholarship.
                    </p>

                </div>


                <a href="index.php" class="back-btn">

                    <i class="fa-solid fa-arrow-left"></i>

                    Back to Scholarships

                </a>

            </div>


            <?php if (!empty($errors)): ?>

                <div class="error-box">

                    <div class="error-box-title">

                        <i class="fa-solid fa-circle-exclamation"></i>

                        Please fix the following errors:

                    </div>

                    <ul>

                        <?php foreach ($errors as $error): ?>

                            <li>
                                <?= htmlspecialchars($error) ?>
                            </li>

                        <?php endforeach; ?>

                    </ul>

                </div>

            <?php endif; ?>


            <div class="form-card">

                <form method="POST">

                    <!-- BASIC INFORMATION -->

                    <div class="form-section">

                        <div class="form-section-title">

                            <i class="fa-solid fa-award"></i>

                            <h2>Basic Information</h2>

                        </div>


                        <div class="form-grid">


                            <div class="form-group full">

                                <label for="title">

                                    Scholarship Title

                                    <span class="required">*</span>

                                </label>

                                <input
                                    type="text"
                                    id="title"
                                    name="title"
                                    class="form-control"
                                    placeholder="e.g. Chevening Scholarships"
                                    value="<?= htmlspecialchars($title) ?>"
                                    required
                                >

                            </div>


                            <div class="form-group full">

                                <label for="description">

                                    Description

                                    <span class="required">*</span>

                                </label>

                                <textarea
                                    id="description"
                                    name="description"
                                    class="form-control"
                                    placeholder="Enter a brief description of the scholarship..."
                                    required
                                ><?= htmlspecialchars($description) ?></textarea>

                            </div>


                        </div>

                    </div>


                    <!-- FUNDING & DEADLINE -->

                    <div class="form-section">

                        <div class="form-section-title">

                            <i class="fa-solid fa-coins"></i>

                            <h2>Funding & Deadline</h2>

                        </div>


                        <div class="form-grid">


                            <div class="form-group">

                                <label for="amount">

                                    Scholarship Amount

                                    <span class="required">*</span>

                                </label>

                                <input
                                    type="text"
                                    id="amount"
                                    name="amount"
                                    class="form-control"
                                    placeholder="e.g. Fully Funded"
                                    value="<?= htmlspecialchars($amount) ?>"
                                    required
                                >

                            </div>


                            <div class="form-group">

                                <label for="deadline">

                                    Application Deadline

                                    <span class="required">*</span>

                                </label>

                                <input
                                    type="date"
                                    id="deadline"
                                    name="deadline"
                                    class="form-control"
                                    value="<?= htmlspecialchars($deadline) ?>"
                                    required
                                >

                            </div>


                        </div>

                    </div>


                    <!-- ELIGIBILITY -->

                    <div class="form-section">

                        <div class="form-section-title">

                            <i class="fa-solid fa-user-check"></i>

                            <h2>Eligibility</h2>

                        </div>


                        <div class="form-grid">


                            <div class="form-group">

                                <label for="eligible_countries">

                                    Eligible Countries

                                    <span class="required">*</span>

                                </label>

                                <input
                                    type="text"
                                    id="eligible_countries"
                                    name="eligible_countries"
                                    class="form-control"
                                    placeholder="e.g. Pakistan, India, Bangladesh"
                                    value="<?= htmlspecialchars($eligible_countries) ?>"
                                    required
                                >

                                <span class="help-text">
                                    Separate multiple countries with commas.
                                </span>

                            </div>


                            <div class="form-group">

                                <label for="study_level">

                                    Study Level

                                    <span class="required">*</span>

                                </label>

                                <select
                                    id="study_level"
                                    name="study_level"
                                    class="form-control"
                                    required
                                >

                                    <option value="">
                                        Select study level
                                    </option>

                                    <option
                                        value="Undergraduate"
                                        <?= $study_level === "Undergraduate" ? "selected" : "" ?>
                                    >
                                        Undergraduate
                                    </option>

                                    <option
                                        value="Postgraduate"
                                        <?= $study_level === "Postgraduate" ? "selected" : "" ?>
                                    >
                                        Postgraduate
                                    </option>

                                    <option
                                        value="Masters"
                                        <?= $study_level === "Masters" ? "selected" : "" ?>
                                    >
                                        Masters
                                    </option>

                                    <option
                                        value="PhD"
                                        <?= $study_level === "PhD" ? "selected" : "" ?>
                                    >
                                        PhD
                                    </option>

                                    <option
                                        value="All Levels"
                                        <?= $study_level === "All Levels" ? "selected" : "" ?>
                                    >
                                        All Levels
                                    </option>

                                </select>

                            </div>


                            <div class="form-group full">

                                <label for="requirements">

                                    Requirements

                                    <span class="required">*</span>

                                </label>

                                <textarea
                                    id="requirements"
                                    name="requirements"
                                    class="form-control"
                                    placeholder="Enter eligibility requirements, academic requirements, documents, etc..."
                                    required
                                ><?= htmlspecialchars($requirements) ?></textarea>

                            </div>


                        </div>

                    </div>


                    <!-- APPLICATION -->

                    <div class="form-section">

                        <div class="form-section-title">

                            <i class="fa-solid fa-file-circle-check"></i>

                            <h2>Application Information</h2>

                        </div>


                        <div class="form-grid">


                            <div class="form-group full">

                                <label for="official_source">

                                    Official Source

                                    <span class="required">*</span>

                                </label>

                                <input
                                    type="url"
                                    id="official_source"
                                    name="official_source"
                                    class="form-control"
                                    placeholder="https://www.example.com/scholarship"
                                    value="<?= htmlspecialchars($official_source) ?>"
                                    required
                                >

                                <span class="help-text">
                                    Add the official scholarship website or application page.
                                </span>

                            </div>


                            <div class="form-group full">

                                <label for="how_to_apply">

                                    How to Apply

                                    <span class="required">*</span>

                                </label>

                                <textarea
                                    id="how_to_apply"
                                    name="how_to_apply"
                                    class="form-control"
                                    placeholder="Explain the application process step by step..."
                                    required
                                ><?= htmlspecialchars($how_to_apply) ?></textarea>

                            </div>


                        </div>

                    </div>


                    <!-- STATUS -->

                    <div class="form-section">

                        <div class="form-section-title">

                            <i class="fa-solid fa-toggle-on"></i>

                            <h2>Status</h2>

                        </div>


                        <div class="form-grid">


                            <div class="form-group">

                                <label for="status">

                                    Scholarship Status

                                    <span class="required">*</span>

                                </label>

                                <select
                                    id="status"
                                    name="status"
                                    class="form-control"
                                    required
                                >

                                    <option
                                        value="active"
                                        <?= $status === "active" ? "selected" : "" ?>
                                    >
                                        Active
                                    </option>

                                    <option
                                        value="inactive"
                                        <?= $status === "inactive" ? "selected" : "" ?>
                                    >
                                        Inactive
                                    </option>

                                </select>

                            </div>


                        </div>

                    </div>


                    <!-- ACTIONS -->

                    <div class="form-actions">

                        <a href="index.php" class="cancel-btn">
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="save-btn"
                        >

                            <i class="fa-solid fa-check"></i>

                            Save Scholarship

                        </button>

                    </div>


                </form>

            </div>

        </section>

    </main>

</div>

</body>

</html>