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

    /*
    |--------------------------------------------------------------------------
    | SINGLE COURSE BY SLUG
    |--------------------------------------------------------------------------
    */

    if (
        isset($_GET["slug"]) &&
        trim($_GET["slug"]) !== ""
    ) {

        $slug = trim($_GET["slug"]);

        $stmt = $pdo->prepare("
            SELECT
                id,
                title,
                slug,
                description,
                level,
                field,
                country,
                language,
                intake,
                tuition_fee,
                duration,
                requirements,
                status
            FROM courses
            WHERE slug = :slug
            AND status = 'active'
            LIMIT 1
        ");

        $stmt->execute([
            ":slug" => $slug
        ]);

        $course = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$course) {

            http_response_code(404);

            echo json_encode([
                "success" => false,
                "message" => "Course not found."
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            exit;
        }

        echo json_encode([
            "success" => true,
            "data" => $course
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | ALL ACTIVE COURSES
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->query("
        SELECT
            id,
            title,
            slug,
            description,
            level,
            field,
            country,
            language,
            intake,
            tuition_fee,
            duration,
            requirements,
            status
        FROM courses
        WHERE status = 'active'
        ORDER BY id DESC
    ");

    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "count" => count($courses),
        "data" => $courses
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to fetch courses."
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}