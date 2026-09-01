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

try {

    $stmt = $pdo->prepare("
        SELECT id
        FROM scholarships
        WHERE id = :id
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


    $stmt = $pdo->prepare("
        DELETE FROM scholarships
        WHERE id = :id
    ");

    $stmt->execute([
        ":id" => $id
    ]);


    $_SESSION["success"] = "Scholarship deleted successfully.";


} catch (PDOException $e) {

    $_SESSION["error"] = "Unable to delete scholarship. Please try again.";

}


header("Location: index.php");
exit;