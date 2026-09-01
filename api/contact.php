<?php

header("Content-Type: application/json");

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

require_once __DIR__ . "/../config/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Only POST requests are allowed."
    ]);

    exit;
}

$data = json_decode(
    file_get_contents("php://input"),
    true
);

$name = trim($data["name"] ?? "");
$email = trim($data["email"] ?? "");
$phone = trim($data["phone"] ?? "");
$subject = trim($data["subject"] ?? "");
$message = trim($data["message"] ?? "");


/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/

if ($name === "" || $email === "" || $message === "") {

    http_response_code(422);

    echo json_encode([
        "success" => false,
        "message" => "Please fill in all required fields."
    ]);

    exit;
}


if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    http_response_code(422);

    echo json_encode([
        "success" => false,
        "message" => "Please enter a valid email address."
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| INSERT MESSAGE
|--------------------------------------------------------------------------
*/

try {

    $stmt = $pdo->prepare("
        INSERT INTO contact_messages
        (
            name,
            email,
            phone,
            subject,
            message,
            status
        )
        VALUES
        (
            :name,
            :email,
            :phone,
            :subject,
            :message,
            'unread'
        )
    ");

    $stmt->execute([

        ":name" => $name,

        ":email" => $email,

        ":phone" => $phone !== ""
            ? $phone
            : null,

        ":subject" => $subject !== ""
            ? $subject
            : null,

        ":message" => $message

    ]);


    echo json_encode([

        "success" => true,

        "message" => "Your enquiry has been submitted successfully."

    ]);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([

        "success" => false,

        "message" => "Unable to submit your enquiry."

    ]);

}