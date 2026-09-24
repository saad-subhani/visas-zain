<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

require_once __DIR__ . "/../config/db.php";

/*
|--------------------------------------------------------------------------
| CMS BASE URL
|--------------------------------------------------------------------------
*/

$baseUrl = "http://localhost/realCMS/";

try {

    /*
    |--------------------------------------------------------------------------
    | CHECK SLUG
    |--------------------------------------------------------------------------
    */

    if (
        !isset($_GET["slug"]) ||
        empty(trim($_GET["slug"]))
    ) {

        http_response_code(400);

        echo json_encode([
            "success" => false,
            "message" => "Destination slug is required."
        ]);

        exit;
    }

    $slug = trim($_GET["slug"]);

    /*
    |--------------------------------------------------------------------------
    | FETCH DESTINATION
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        SELECT
            id,
            country_name,
            slug,
            description,
            why_study,
            tuition_range,
            living_cost,
            visa_info,
            image_url,
            status,
            created_at
        FROM destinations
        WHERE slug = :slug
        LIMIT 1
    ");

    $stmt->execute([
        ":slug" => $slug
    ]);

    $destination = $stmt->fetch(PDO::FETCH_ASSOC);

    /*
    |--------------------------------------------------------------------------
    | DESTINATION NOT FOUND
    |--------------------------------------------------------------------------
    */

    if (!$destination) {

        http_response_code(404);

        echo json_encode([
            "success" => false,
            "message" => "Destination not found."
        ]);

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | CHECK ACTIVE STATUS
    |--------------------------------------------------------------------------
    */

    if (
        strtolower(
            trim($destination["status"])
        ) !== "active"
    ) {

        http_response_code(404);

        echo json_encode([
            "success" => false,
            "message" => "Destination is not active."
        ]);

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT IMAGE URL
    |--------------------------------------------------------------------------
    */

    if (!empty($destination["image_url"])) {

        $imageUrl = trim(
            $destination["image_url"]
        );

        /*
        |--------------------------------------------------------------------------
        | OLD WRONG URL
        |--------------------------------------------------------------------------
        |
        | Example:
        | http://localhost/realCMS/../../uploads/destinations/file.jpg
        |
        */

        if (
            str_contains(
                $imageUrl,
                "../../uploads/"
            )
        ) {

            $filename = basename($imageUrl);

            $destination["image_url"] =
                $baseUrl .
                "uploads/destinations/" .
                $filename;

        /*
        |--------------------------------------------------------------------------
        | Already correct full URL
        |--------------------------------------------------------------------------
        */

        } elseif (
            str_starts_with(
                $imageUrl,
                "http://"
            ) ||
            str_starts_with(
                $imageUrl,
                "https://"
            )
        ) {

            $destination["image_url"] =
                $imageUrl;

        /*
        |--------------------------------------------------------------------------
        | Relative URL
        |--------------------------------------------------------------------------
        */

        } else {

            $filename = basename(
                $imageUrl
            );

            $destination["image_url"] =
                $baseUrl .
                "uploads/destinations/" .
                $filename;
        }

    } else {

        $destination["image_url"] = null;
    }

    /*
    |--------------------------------------------------------------------------
    | NORMALIZE STATUS
    |--------------------------------------------------------------------------
    */

    $destination["status"] =
        $destination["status"] ?? "active";

    /*
    |--------------------------------------------------------------------------
    | SUCCESS RESPONSE
    |--------------------------------------------------------------------------
    */

    echo json_encode(
        [
            "success" => true,
            "data" => $destination
        ],
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES
    );

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode(
        [
            "success" => false,
            "message" => "Database error."
        ],
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES
    );
}