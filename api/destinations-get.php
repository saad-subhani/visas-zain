<?php

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../../config/db.php";

try {

    if (!isset($_GET["slug"]) || empty(trim($_GET["slug"]))) {
        http_response_code(400);

        echo json_encode([
            "success" => false,
            "message" => "Destination slug is required."
        ]);

        exit;
    }

    $slug = trim($_GET["slug"]);

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
            status
        FROM destinations
        WHERE slug = :slug
        LIMIT 1
    ");

    $stmt->execute([
        ":slug" => $slug
    ]);

    $destination = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$destination) {
        http_response_code(404);

        echo json_encode([
            "success" => false,
            "message" => "Destination not found."
        ]);

        exit;
    }

    if ($destination["status"] !== "active") {
        http_response_code(404);

        echo json_encode([
            "success" => false,
            "message" => "Destination is not active."
        ]);

        exit;
    }

    echo json_encode([
        "success" => true,
        "data" => $destination
    ]);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Database error."
    ]);
}