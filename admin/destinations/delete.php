<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . "/../../config/db.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {

    $_SESSION["error"] = "Invalid destination ID.";

    header("Location: index.php");
    exit;
}

try {

    /* =========================
       GET DESTINATION
    ========================= */

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

    /* =========================
       DELETE DATABASE RECORD
    ========================= */

    $delete = $pdo->prepare("
        DELETE FROM destinations
        WHERE id = :id
    ");

    $delete->execute([
        ":id" => $id
    ]);

    /* =========================
       DELETE IMAGE
    ========================= */

    if (!empty($destination["image_url"])) {

        /*
         * Database example:
         * ../../uploads/destinations/file.jpg
         *
         * Convert it to:
         * uploads/destinations/file.jpg
         */

        $relativeImagePath = str_replace(
            "../../",
            "",
            $destination["image_url"]
        );

        $imagePath =
            __DIR__ . "/../../" . $relativeImagePath;

        if (
            file_exists($imagePath) &&
            is_file($imagePath)
        ) {

            unlink($imagePath);
        }
    }

    $_SESSION["success"] =
        "Destination deleted successfully.";

} catch (PDOException $e) {

    $_SESSION["error"] =
        "Unable to delete destination. Please try again.";
}

header("Location: index.php");
exit;