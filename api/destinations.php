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

try {

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

    $destinations = $stmt->fetchAll();

    echo json_encode([
        "success" => true,
        "data" => $destinations
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to fetch destinations.",
        "error" => $e->getMessage()
    ]);
}
?>
