<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../config/db.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $currentPassword = $_POST["current_password"] ?? "";
    $newPassword = $_POST["new_password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    if ($currentPassword === "" || $newPassword === "" || $confirmPassword === "") {

        $error = "All password fields are required.";

    } elseif (strlen($newPassword) < 8) {

        $error = "New password must be at least 8 characters long.";

    } elseif ($newPassword !== $confirmPassword) {

        $error = "New password and confirm password do not match.";

    } else {

        $stmt = $pdo->prepare(
            "SELECT password FROM admins WHERE id = ?"
        );

        $stmt->execute([$_SESSION["admin_id"]]);

        $admin = $stmt->fetch();

        if (!$admin || !password_verify($currentPassword, $admin["password"])) {

            $error = "Current password is incorrect.";

        } else {

            $newHash = password_hash(
                $newPassword,
                PASSWORD_DEFAULT
            );

            $update = $pdo->prepare(
                "UPDATE admins SET password = ? WHERE id = ?"
            );

            $update->execute([
                $newHash,
                $_SESSION["admin_id"]
            ]);

            $success = "Password changed successfully.";
        }
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

```
<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Change Password | Edworldly Consultancy</title>

<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
>

<link
    rel="stylesheet"
    href="../assets/css/admin.css"
>

<style>

    .password-page {
        max-width: 600px;
        margin: 0 auto;
    }

    .password-card {
        background: #ffffff;
        border: 1px solid #e8ebf0;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.04);
    }

    .password-card h2 {
        margin-bottom: 8px;
        color: #172033;
    }

    .password-card > p {
        margin-bottom: 25px;
        color: #7b8494;
        font-size: 14px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #273247;
    }

    .form-group input {
        width: 100%;
        height: 48px;
        border: 1px solid #dce2e9;
        border-radius: 9px;
        padding: 0 14px;
        font-size: 14px;
        outline: none;
    }

    .form-group input:focus {
        border-color: #172033;
        box-shadow: 0 0 0 3px rgba(23,32,51,0.08);
    }

    .password-btn {
        width: 100%;
        height: 48px;
        border: none;
        border-radius: 9px;
        background: #172033;
        color: #ffffff;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
    }

    .password-btn:hover {
        background: #25314a;
    }

    .alert {
        padding: 12px 14px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 13px;
    }

    .alert.error {
        background: #fff0f0;
        border: 1px solid #ffd1d1;
        color: #c62828;
    }

    .alert.success {
        background: #effaf3;
        border: 1px solid #c8efd5;
        color: #218838;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 20px;
        color: #172033;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
    }

    .back-link:hover {
        color: #6D4AFF;
    }

</style>
```

</head>

<body>

<div class="admin-layout">

```
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

        <a href="dashboard.php" class="nav-item">
            <i class="fa-solid fa-chart-line"></i>
            <span>Dashboard</span>
        </a>

        <a href="courses/index.php" class="nav-item">
            <i class="fa-solid fa-book-open"></i>
            <span>Courses</span>
        </a>

        <a href="destinations/index.php" class="nav-item">
            <i class="fa-solid fa-earth-americas"></i>
            <span>Destinations</span>
        </a>

        <a href="universities/index.php" class="nav-item">
            <i class="fa-solid fa-building-columns"></i>
            <span>Universities</span>
        </a>

        <a href="scholarships/index.php" class="nav-item">
            <i class="fa-solid fa-award"></i>
            <span>Scholarships</span>
        </a>

        <a href="contact-messages/index.php" class="nav-item">
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

        <a href="change-password.php" class="logout-btn">
            <i class="fa-solid fa-key"></i>
            Change Password
        </a>

        <a href="logout.php" class="logout-btn">
            <i class="fa-solid fa-right-from-bracket"></i>
            Logout
        </a>

    </div>

</aside>


<!-- MAIN CONTENT -->

<main class="main-content">

    <header class="topbar">

        <div>

            <h1>Change Password</h1>

            <p>
                Update your administrator account password.
            </p>

        </div>

    </header>


    <section class="content">

        <div class="password-page">

            <a href="dashboard.php" class="back-link">
                <i class="fa-solid fa-arrow-left"></i>
                Back to Dashboard
            </a>

            <div class="password-card">

                <h2>Change Your Password</h2>

                <p>
                    Enter your current password and choose a new secure password.
                </p>

                <?php if ($error !== ""): ?>

                    <div class="alert error">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>

                <?php endif; ?>


                <?php if ($success !== ""): ?>

                    <div class="alert success">
                        <i class="fa-solid fa-circle-check"></i>
                        <?= htmlspecialchars($success) ?>
                    </div>

                <?php endif; ?>


                <form method="POST">

                    <div class="form-group">

                        <label for="current_password">
                            Current Password
                        </label>

                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            placeholder="Enter current password"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="new_password">
                            New Password
                        </label>

                        <input
                            type="password"
                            id="new_password"
                            name="new_password"
                            placeholder="Enter new password"
                            minlength="8"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="confirm_password">
                            Confirm New Password
                        </label>

                        <input
                            type="password"
                            id="confirm_password"
                            name="confirm_password"
                            placeholder="Confirm new password"
                            minlength="8"
                            required
                        >

                    </div>


                    <button
                        type="submit"
                        class="password-btn"
                    >
                        <i class="fa-solid fa-key"></i>
                        &nbsp; Change Password
                    </button>

                </form>

            </div>

        </div>

    </section>

</main>
```

</div>

</body>
</html>
