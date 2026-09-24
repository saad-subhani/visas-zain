<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . "/../../config/db.php";


/*
|--------------------------------------------------------------------------
| GET UNIVERSITY ID
|--------------------------------------------------------------------------
*/

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    $_SESSION["error"] = "Invalid university ID.";
    header("Location: index.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| FETCH UNIVERSITY
|--------------------------------------------------------------------------
*/

try {

    $stmt = $pdo->prepare("
        SELECT id, image_url
        FROM universities
        WHERE id = :id
        LIMIT 1
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


    /*
    |--------------------------------------------------------------------------
    | DELETE DATABASE RECORD
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        DELETE FROM universities
        WHERE id = :id
    ");

    $stmt->execute([
        ":id" => $id
    ]);


    /*
    |--------------------------------------------------------------------------
    | DELETE ASSOCIATED IMAGE
    |--------------------------------------------------------------------------
    */

    if (!empty($university["image_url"])) {

        $imagePath = __DIR__ . "/../../" . ltrim(
            $university["image_url"],
            "/"
        );

        if (is_file($imagePath)) {
            @unlink($imagePath);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SUCCESS MESSAGE
    |--------------------------------------------------------------------------
    */

    $_SESSION["success"] = "University deleted successfully.";


} catch (PDOException $e) {

    $_SESSION["error"] = "Unable to delete university. Please try again.";
}


/*
|--------------------------------------------------------------------------
| REDIRECT
|--------------------------------------------------------------------------
*/

header("Location: index.php");
exit;