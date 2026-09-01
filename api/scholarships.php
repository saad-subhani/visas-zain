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
            title,
            description,
            amount,
            deadline,
            eligible_countries,
            study_level,
            requirements,
            official_source,
            how_to_apply,
            status,
            created_at
        FROM scholarships
        WHERE status = 'active'
        ORDER BY deadline ASC
    ");

    $stmt->execute();

    $scholarships = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "data" => $scholarships
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to fetch scholarships."
    ]);
}
?>