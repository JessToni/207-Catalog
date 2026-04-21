<?php
require_once 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Basic Validation
    if (empty($email) || empty($password)) {
        echo json_encode(["error" => "Email and password are required."]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["error" => "Invalid email format."]);
        exit;
    }

    // Hashes the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    try {
        // Insert into database
        $sql = "INSERT INTO users (email, password_hash) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email, $hashedPassword]);

        echo json_encode(["status" => "success", "message" => "User registered!"]);
    } catch (\PDOException $e) {
        if ($e->getCode() == 23000) { // Error code for duplicate entry
            echo json_encode(["error" => "Email already exists."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
} else {
    echo json_encode(["message" => "Please send a POST request."]);
}