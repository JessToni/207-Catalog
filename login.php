<?php
require_once 'db.php';
session_start();

// Start session and track user
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    try {
        // Find the user by email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if user exists and password is correct
        if ($user && password_verify($password, $user['password_hash'])) {
            // Stores user data in the Session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];

            echo json_encode([
                "status" => "success",
                "message" => "Login successful!",
                "user" => [
                    "id" => $user['id'],
                    "email" => $user['email']
                ]
            ]);
        } else {
            // Error response
            http_response_code(401);
            echo json_encode(["status" => "error", "message" => "Invalid email or password."]);
        }

    } catch (\PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }

} else {
    echo json_encode(["message" => "Please send a POST request."]);
}