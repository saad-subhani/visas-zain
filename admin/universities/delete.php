<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . "/../../config/db.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    $_SESSION["error"] = "Invalid university ID.";
    header("Location: index.php");
    exit;
}

try {

    $stmt = $pdo->prepare("
        SELECT id
        FROM universities
        WHERE id = :id
    ");

    $stmt->execute([
        ":id" => $id
    ]);

    $university = $stmt->fetch();

    if (!$university) {
        $_SESSION["error"] = "University not found.";
        header("Location: index.php");
        exit;
    }

    $stmt = $pdo->prepare("
        DELETE FROM universities
        WHERE id = :id
    ");

    $stmt->execute([
        ":id" => $id
    ]);

    $_SESSION["success"] = "University deleted successfully.";

} catch (PDOException $e) {

    $_SESSION["error"] = "Unable to delete university. Please try again.";
}

header("Location: index.php");
exit;