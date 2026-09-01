<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . "/../../config/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$id = isset($_POST["id"])
    ? (int) $_POST["id"]
    : 0;

if ($id <= 0) {
    header("Location: index.php?error=invalid");
    exit;
}

try {

    $stmt = $pdo->prepare("
        DELETE FROM contact_messages
        WHERE id = :id
    ");

    $stmt->execute([
        ":id" => $id
    ]);

    header("Location: index.php?deleted=1");
    exit;

} catch (PDOException $e) {

    header("Location: index.php?error=delete");
    exit;

}