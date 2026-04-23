<?php
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Only POST requests are accepted."]);
    exit;
}

// 1. Capture and Sanitize Input
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$password = $_POST['password'] ?? '';

// 2. Strict Validation
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
    // 3. Secure Hashing
    // PASSWORD_DEFAULT is recommended over explicit BCRYPT as it auto-updates if PHP adds better algos
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // 4. Atomic Insertion
    // Using the DB's UNIQUE constraint is faster and prevents "Race Conditions" 
    // compared to doing a SELECT then an INSERT.
    $stmt = $pdo->prepare("INSERT INTO users (email, password_hash) VALUES (?, ?)");
    $stmt->execute([$email, $hashedPassword]);

    http_response_code(201); // Created
    echo json_encode(["status" => "success", "message" => "User registered successfully."]);

} catch (\PDOException $e) {
    if ($e->getCode() == 23000) { 
        // 23000 is the SQLSTATE for integrity constraint violation (Duplicate Entry)
        http_response_code(409); 
        echo json_encode(["error" => "This email is already registered."]);
    } else {
        // Log the actual error internally, but don't show $e->getMessage() to the user
        error_log($e->getMessage()); 
        http_response_code(500);
        echo json_encode(["error" => "An internal server error occurred."]);
    }
}