<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . "/../../config/db.php";

/*
|--------------------------------------------------------------------------
| FETCH ACTIVE UNIVERSITIES
|--------------------------------------------------------------------------
*/

try {

    $stmt = $pdo->prepare("
        SELECT
            id,
            name,
            slug,
            country,
            city,
            description,
            programmes,
            tuition_fee,
            intake_dates,
            requirements,
            english_requirements,
            scholarships_available,
            official_url,
            image_url,
            status,
            created_at,
            updated_at
        FROM universities
        WHERE status = 'active'
        ORDER BY created_at DESC
    ");

    $stmt->execute();

    $universities = $stmt->fetchAll(PDO::FETCH_ASSOC);

    /*
    |--------------------------------------------------------------------------
    | REAL CMS BASE URL
    |--------------------------------------------------------------------------
    */

    $baseUrl = "http://localhost/realCMS/";

    /*
    |--------------------------------------------------------------------------
    | FORMAT IMAGE URL
    |--------------------------------------------------------------------------
    */

    foreach ($universities as &$university) {

        if (!empty($university["image_url"])) {

            $imagePath = ltrim(
                $university["image_url"],
                "/"
            );

            $university["image_url"] = $baseUrl . $imagePath;

        } else {

            $university["image_url"] = null;
        }

        /*
        |--------------------------------------------------------------------------
        | NORMALIZE SCHOLARSHIP VALUE
        |--------------------------------------------------------------------------
        */

        $university["scholarships_available"] =
            $university["scholarships_available"] ?? "no";

        /*
        |--------------------------------------------------------------------------
        | NORMALIZE STATUS
        |--------------------------------------------------------------------------
        */

        $university["status"] =
            $university["status"] ?? "active";
    }

    unset($university);

    /*
    |--------------------------------------------------------------------------
    | SUCCESS RESPONSE
    |--------------------------------------------------------------------------
    */

    echo json_encode(
        [
            "success" => true,
            "count" => count($universities),
            "data" => $universities
        ],
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
    );

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode(
        [
            "success" => false,
            "message" => "Unable to fetch universities."
        ],
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
    );
}