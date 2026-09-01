<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

require_once __DIR__ . "/../config/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "success" => false,
        "message" => "Only POST requests are allowed."
    ]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request data."
    ]);
    exit;
}

$name = trim($input["name"] ?? "");
$whatsapp = trim($input["whatsapp"] ?? "");
$email = trim($input["email"] ?? "");
$country = trim($input["country"] ?? "");

$qualification = trim($input["qualification"] ?? "");
$field = trim($input["field"] ?? "");
$percentage = trim($input["percentage"] ?? "");
$graduationYear = trim($input["graduationYear"] ?? "");
$englishTest = trim($input["englishTest"] ?? "");

$destination = trim($input["destination"] ?? "");
$studyLevel = trim($input["studyLevel"] ?? "");
$intake = trim($input["intake"] ?? "");
$budget = trim($input["budget"] ?? "");
$interestedField = trim($input["interestedField"] ?? "");


if (
    !$name ||
    !$whatsapp ||
    !$email ||
    !$country ||
    !$qualification ||
    !$field ||
    !$percentage ||
    !$graduationYear ||
    !$englishTest ||
    !$destination ||
    !$studyLevel ||
    !$intake ||
    !$budget ||
    !$interestedField
) {
    echo json_encode([
        "success" => false,
        "message" => "Please complete all required fields."
    ]);
    exit;
}


if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "success" => false,
        "message" => "Please enter a valid email address."
    ]);
    exit;
}


try {

    $sql = "
        INSERT INTO assessments (
            name,
            whatsapp,
            email,
            country,
            qualification,
            field,
            percentage,
            graduation_year,
            english_test,
            destination,
            study_level,
            intake,
            budget,
            interested_field
        )

        VALUES (
            :name,
            :whatsapp,
            :email,
            :country,
            :qualification,
            :field,
            :percentage,
            :graduation_year,
            :english_test,
            :destination,
            :study_level,
            :intake,
            :budget,
            :interested_field
        )
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":name" => $name,
        ":whatsapp" => $whatsapp,
        ":email" => $email,
        ":country" => $country,

        ":qualification" => $qualification,
        ":field" => $field,
        ":percentage" => $percentage,
        ":graduation_year" => $graduationYear,
        ":english_test" => $englishTest,

        ":destination" => $destination,
        ":study_level" => $studyLevel,
        ":intake" => $intake,
        ":budget" => $budget,
        ":interested_field" => $interestedField
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Assessment submitted successfully.",
        "id" => $pdo->lastInsertId()
    ]);

} catch (PDOException $e) {

    echo json_encode([
        "success" => false,
        "message" => "Unable to save assessment."
    ]);
}