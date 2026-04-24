<?php
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Only POST requests are accepted."]);
    exit;
}

// Capture and sanitize input
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$password = $_POST['password'] ?? '';

// Strict Validation
if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(["error" => "Email and password are required."]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["error" => "Please provide a valid email address."]);
    exit;
}

if (strlen($password) < 8) {
    http_response_code(400);
    echo json_encode(["error" => "Password must be at least 8 characters long."]);
    exit;
}

try {
    // Secure Hashing
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Atomic Insertion
    $stmt = $pdo->prepare("INSERT INTO users (email, password_hash) VALUES (?, ?)");
    $stmt->execute([$email, $hashedPassword]);

    http_response_code(201); // Created
    echo json_encode(["status" => "success", "message" => "User registered successfully."]);

} catch (\PDOException $e) {
    if ($e->getCode() == 23000) { 
        http_response_code(409); 
        echo json_encode(["error" => "This email is already registered."]);
    } else {
        // Log the error internally
        error_log($e->getMessage()); 
        http_response_code(500);
        echo json_encode(["error" => "An internal server error occurred."]);
    }
}