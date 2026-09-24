<?php

session_start();

if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit;
}

require_once __DIR__ . '/../config/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {
        $error = "Username and password are required.";
    } else {

        $stmt = $pdo->prepare("
            SELECT id, username, password, full_name, email
            FROM admin_users
            WHERE username = ?
            AND status = 'Active'
            LIMIT 1
        ");

        $stmt->execute([$username]);

        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin["password"])) {

            session_regenerate_id(true);

            $_SESSION["admin_id"] = $admin["id"];
            $_SESSION["admin_username"] = $admin["username"];
            $_SESSION["admin_name"] = $admin["full_name"] ?? "";

            header("Location: dashboard.php");
            exit;

        } else {
            $error = "Invalid username or password.";
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

    <title>Admin Login | FSC</title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            min-height: 100vh;
            background:
                radial-gradient(
                    circle at top left,
                    rgba(16, 167, 217, 0.08),
                    transparent 35%
                ),
                #f3f6fa;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-wrapper {
            width: 100%;
            max-width: 430px;
        }

        .login-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.08);
            border: 1px solid #e8edf3;
        }

        .logo {
            width: 62px;
            height: 62px;
            background: #102f52;
            color: #ffffff;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 25px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
            font-size: 25px;
            color: #102f52;
            margin-bottom: 8px;
        }

        .login-header p {
            color: #7b8494;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #273247;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #98a1af;
        }

        .input-wrapper input {
            width: 100%;
            height: 48px;
            border: 1px solid #dce2e9;
            border-radius: 9px;
            padding: 0 15px 0 43px;
            font-size: 14px;
            outline: none;
            transition: 0.2s ease;
        }

        .input-wrapper input:focus {
            border-color: #102f52;
            box-shadow: 0 0 0 3px rgba(16, 47, 82, 0.08);
        }

        .error {
            background: #fff0f0;
            border: 1px solid #ffd1d1;
            color: #c62828;
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .login-btn {
            width: 100%;
            height: 48px;
            border: none;
            border-radius: 9px;
            background: #102f52;
            color: #ffffff;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .login-btn:hover {
            background: #1a456f;
        }

        .footer-text {
            text-align: center;
            margin-top: 22px;
            color: #98a1af;
            font-size: 12px;
        }

        @media (max-width: 480px) {

            .login-card {
                padding: 30px 22px;
            }

            .login-header h1 {
                font-size: 22px;
            }
        }

    </style>

</head>

<body>

<div class="login-wrapper">

    <div class="login-card">

        <div class="logo">
            <i class="fa-solid fa-graduation-cap"></i>
        </div>

        <div class="login-header">

            <h1>FSC Consultancy</h1>

            <p>
                Admin Panel Login
            </p>

        </div>

        <?php if ($error !== ""): ?>

            <div class="error">

                <i class="fa-solid fa-circle-exclamation"></i>

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="form-group">

                <label for="username">
                    Username
                </label>

                <div class="input-wrapper">

                    <i class="fa-solid fa-user"></i>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Enter username"
                        value="<?= htmlspecialchars($_POST["username"] ?? "") ?>"
                        required
                        autofocus
                    >

                </div>

            </div>

            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <div class="input-wrapper">

                    <i class="fa-solid fa-lock"></i>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter password"
                        required
                    >

                </div>

            </div>

            <button
                type="submit"
                class="login-btn"
            >

                <i class="fa-solid fa-right-to-bracket"></i>

                &nbsp; Login

            </button>

        </form>

    </div>

    <div class="footer-text">

        © <?= date("Y") ?> FSC Consultancy.
        All rights reserved.

    </div>

</div>

</body>

</html>