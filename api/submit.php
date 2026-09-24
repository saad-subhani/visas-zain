<?php

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../../config/db.php";

/*
|--------------------------------------------------------------------------
| Only POST requests are allowed
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Method not allowed."
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Read JSON request body
|--------------------------------------------------------------------------
*/

$input = json_decode(
    file_get_contents("php://input"),
    true
);

if (!is_array($input)) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid request data."
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Get form fields
|--------------------------------------------------------------------------
*/

$name = trim($input["name"] ?? "");
$email = trim($input["email"] ?? "");
$phone = trim($input["phone"] ?? "");
$subject = trim($input["subject"] ?? "");
$message = trim($input["message"] ?? "");


/*
|--------------------------------------------------------------------------
| Validate required fields
|--------------------------------------------------------------------------
*/

if ($name === "") {

    http_response_code(422);

    echo json_encode([
        "success" => false,
        "message" => "Please enter your full name."
    ]);

    exit;
}


if ($email === "") {

    http_response_code(422);

    echo json_encode([
        "success" => false,
        "message" => "Please enter your email address."
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


if ($subject === "") {

    http_response_code(422);

    echo json_encode([
        "success" => false,
        "message" => "Please select a subject."
    ]);

    exit;
}


if ($message === "") {

    http_response_code(422);

    echo json_encode([
        "success" => false,
        "message" => "Please enter your message."
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Insert message into existing contact_messages table
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
            'New'
        )
    ");

    $stmt->execute([
        ":name" => $name,
        ":email" => $email,
        ":phone" => $phone !== "" ? $phone : null,
        ":subject" => $subject,
        ":message" => $message
    ]);


    /*
    |--------------------------------------------------------------------------
    | Success response
    |--------------------------------------------------------------------------
    */

    echo json_encode([
        "success" => true,
        "message" => "Your message has been sent successfully."
    ]);

    exit;


} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to send your message right now. Please try again."
    ]);

    exit;
}