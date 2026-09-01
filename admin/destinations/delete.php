<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . "/../../config/db.php";

$id = (int) ($_GET["id"] ?? 0);

if ($id <= 0) {
    $_SESSION["error"] = "Invalid destination.";
    header("Location: index.php");
    exit;
}

try {

    // Get destination image before deleting the record
    $stmt = $pdo->prepare("
        SELECT image_url
        FROM destinations
        WHERE id = :id
    ");

    $stmt->execute([
        ":id" => $id
    ]);

    $destination = $stmt->fetch();

    if (!$destination) {
        $_SESSION["error"] = "Destination not found.";
        header("Location: index.php");
        exit;
    }

    // Delete destination from database
    $delete = $pdo->prepare("
        DELETE FROM destinations
        WHERE id = :id
    ");

    $delete->execute([
        ":id" => $id
    ]);

    // Delete uploaded image from server
    if (!empty($destination["image_url"])) {

        $imagePath = __DIR__ . "/../../" . ltrim(
            $destination["image_url"],
            "/"
        );

        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    $_SESSION["success"] = "Destination deleted successfully.";

} catch (PDOException $e) {

    $_SESSION["error"] = "Unable to delete destination. Please try again.";

}

header("Location: index.php");
exit;