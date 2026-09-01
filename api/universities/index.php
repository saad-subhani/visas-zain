<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . "/../../config/db.php";

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
            created_at
        FROM universities
        WHERE status = 'active'
        ORDER BY created_at DESC
    ");

    $stmt->execute();

    $universities = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $baseUrl = "http://localhost/CMS/";

    foreach ($universities as &$university) {

        if (!empty($university["image_url"])) {
            $university["image_url"] = $baseUrl . $university["image_url"];
        } else {
            $university["image_url"] = null;
        }

    }

    unset($university);

    echo json_encode([
        "success" => true,
        "count" => count($universities),
        "data" => $universities
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to fetch universities.",
        "error" => $e->getMessage()
    ]);

}