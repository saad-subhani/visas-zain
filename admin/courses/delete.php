<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . "/../../config/db.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    $_SESSION["error"] = "Invalid course ID.";
    header("Location: index.php");
    exit;
}

try {

    // Check whether course exists
    $stmt = $pdo->prepare("
        SELECT id
        FROM courses
        WHERE id = :id
        LIMIT 1
    ");

    $stmt->execute([
        ":id" => $id
    ]);

    $course = $stmt->fetch();

    if (!$course) {

        $_SESSION["error"] = "Course not found.";

        header("Location: index.php");
        exit;
    }

    // Delete course
    $stmt = $pdo->prepare("
        DELETE FROM courses
        WHERE id = :id
    ");

    $stmt->execute([
        ":id" => $id
    ]);

    $_SESSION["success"] = "Course deleted successfully.";

} catch (PDOException $e) {

    $_SESSION["error"] = "Unable to delete course. Please try again.";
}

header("Location: index.php");
exit;