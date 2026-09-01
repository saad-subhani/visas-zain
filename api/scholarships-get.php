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

    if (!isset($_GET["id"]) || empty(trim($_GET["id"]))) {
        http_response_code(400);

        echo json_encode([
            "success" => false,
            "message" => "Scholarship ID is required."
        ]);

        exit;
    }

    $id = (int) $_GET["id"];

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
        WHERE id = :id
        LIMIT 1
    ");

    $stmt->execute([
        ":id" => $id
    ]);

    $scholarship = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$scholarship) {
        http_response_code(404);

        echo json_encode([
            "success" => false,
            "message" => "Scholarship not found."
        ]);

        exit;
    }

    if ($scholarship["status"] !== "active") {
        http_response_code(404);

        echo json_encode([
            "success" => false,
            "message" => "Scholarship is not active."
        ]);

        exit;
    }

    echo json_encode([
        "success" => true,
        "data" => $scholarship
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Database error."
    ]);
}
?>