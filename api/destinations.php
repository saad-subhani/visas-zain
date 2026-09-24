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
    | FETCH ACTIVE DESTINATIONS
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
        WHERE status = 'active'
        ORDER BY id ASC
    ");

    $stmt->execute();

    $destinations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    /*
    |--------------------------------------------------------------------------
    | FORMAT IMAGE URL
    |--------------------------------------------------------------------------
    */

    foreach ($destinations as &$destination) {

        if (!empty($destination["image_url"])) {

            $imageUrl = trim($destination["image_url"]);

            /*
            |--------------------------------------------------------------------------
            | Already a complete URL
            |--------------------------------------------------------------------------
            */

            if (
                str_starts_with($imageUrl, "http://") ||
                str_starts_with($imageUrl, "https://")
            ) {

                /*
                | Agar old database value mein ../../uploads aa gaya hai
                | to filename nikal kar correct CMS URL bana do.
                */

                if (str_contains($imageUrl, "../../uploads/")) {

                    $filename = basename($imageUrl);

                    $destination["image_url"] =
                        $baseUrl .
                        "uploads/destinations/" .
                        $filename;

                } else {

                    $destination["image_url"] = $imageUrl;
                }

            } else {

                /*
                |--------------------------------------------------------------------------
                | Relative image path
                |--------------------------------------------------------------------------
                */

                $filename = basename($imageUrl);

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
    }

    unset($destination);

    /*
    |--------------------------------------------------------------------------
    | SUCCESS RESPONSE
    |--------------------------------------------------------------------------
    */

    echo json_encode(
        [
            "success" => true,
            "count" => count($destinations),
            "data" => $destinations
        ],
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES
    );

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode(
        [
            "success" => false,
            "message" => "Failed to fetch destinations."
        ],
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES
    );
}