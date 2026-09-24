<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . "/../../config/db.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    $_SESSION["error"] = "Invalid scholarship ID.";
    header("Location: index.php");
    exit;
}

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

/*
|--------------------------------------------------------------------------
| Load Scholarship
|--------------------------------------------------------------------------
*/

try {

    $stmt = $pdo->prepare("
        SELECT *
        FROM scholarships
        WHERE id = :id
        LIMIT 1
    ");

    $stmt->execute([
        ":id" => $id
    ]);

    $scholarship = $stmt->fetch();

    if (!$scholarship) {
        $_SESSION["error"] = "Scholarship not found.";
        header("Location: index.php");
        exit;
    }

    $title = $scholarship["title"] ?? "";
    $description = $scholarship["description"] ?? "";
    $amount = $scholarship["amount"] ?? "";
    $deadline = $scholarship["deadline"] ?? "";
    $eligible_countries = $scholarship["eligible_countries"] ?? "";
    $study_level = $scholarship["study_level"] ?? "";
    $requirements = $scholarship["requirements"] ?? "";
    $official_source = $scholarship["official_source"] ?? "";
    $how_to_apply = $scholarship["how_to_apply"] ?? "";
    $status = $scholarship["status"] ?? "active";

} catch (PDOException $e) {

    $_SESSION["error"] = "Unable to load scholarship.";
    header("Location: index.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Update Scholarship
|--------------------------------------------------------------------------
*/

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
                UPDATE scholarships
                SET
                    title = :title,
                    description = :description,
                    amount = :amount,
                    deadline = :deadline,
                    eligible_countries = :eligible_countries,
                    study_level = :study_level,
                    requirements = :requirements,
                    official_source = :official_source,
                    how_to_apply = :how_to_apply,
                    status = :status
                WHERE id = :id
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
                ":status" => $status,
                ":id" => $id
            ]);

            $_SESSION["success"] = "Scholarship updated successfully.";

            header("Location: index.php");
            exit;

        } catch (PDOException $e) {

            $errors[] = "Unable to update scholarship. Please try again.";

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

    <title>Edit Scholarship | Foreign Study Consultant</title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <link
        rel="stylesheet"
        href="../../assets/css/admin.css"
    >

    <style>

        .mobile-header {
            display: none;
        }

        .mobile-menu-btn {
            width: 42px;
            height: 42px;
            border: 1px solid #e8ebf0;
            border-radius: 8px;
            background: #ffffff;
            color: #102f52;
            font-size: 18px;
            cursor: pointer;
            align-items: center;
            justify-content: center;
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

        .sidebar-brand {
            color: rgb(14, 171, 214);
        }

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

        .update-btn {
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

        .update-btn:hover {
            background: #27344d;
        }

        @media (max-width: 900px) {

            .mobile-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 14px 18px;
                background: #ffffff;
                border-bottom: 1px solid #e8ebf0;
            }

            .mobile-brand {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .mobile-brand-icon {
                width: 38px;
                height: 38px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #102f52;
                color: #ffffff;
            }

            .mobile-brand-text h2 {
                margin: 0;
                font-size: 15px;
                color: #172033;
            }

            .mobile-brand-text span {
                font-size: 11px;
                color: #7e8899;
            }

            .mobile-menu-btn {
                display: flex;
            }

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

            .topbar {
                padding: 18px;
            }

            .content {
                padding: 18px;
            }
        }

        @media (max-width: 700px) {

            .form-card {
                padding: 16px;
            }

            .form-actions {
                flex-direction: column-reverse;
                align-items: stretch;
            }

            .cancel-btn,
            .update-btn {
                width: 100%;
                justify-content: center;
                text-align: center;
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
                <h2>Foreign Study</h2>
                <span>Consultant</span>
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

            <a href="../change-password.php" class="change-password-btn">
                <i class="fa-solid fa-key"></i>
                Change Password
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

        <!-- MOBILE HEADER -->

        <div class="mobile-header">

            <div class="mobile-brand">

                <div class="mobile-brand-icon">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>

                <div class="mobile-brand-text">
                    <h2>Foreign Study</h2>
                    <span>Consultant</span>
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

                <h1>Edit Scholarship</h1>

                <p>
                    Update scholarship information.
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

                    <h1>Edit Scholarship</h1>

                    <p>
                        Update the details of this scholarship.
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
                                    required
                                ><?= htmlspecialchars($description) ?></textarea>

                            </div>

                        </div>

                    </div>


                    <!-- FUNDING -->

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
                                    value="<?= htmlspecialchars($official_source) ?>"
                                    required
                                >

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
                            class="update-btn"
                        >
                            <i class="fa-solid fa-rotate"></i>
                            Update Scholarship
                        </button>

                    </div>

                </form>

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