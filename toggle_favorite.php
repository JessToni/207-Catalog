<?php
session_start();
require_once 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Login required"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = (int)($_POST['product_id'] ?? 0);

try {
    // Check if already marked as favorite
    $check = $pdo->prepare("SELECT * FROM favorites WHERE user_id = ? AND product_id = ?");
    $check->execute([$user_id, $product_id]);
    
    if ($check->fetch()) {
        // Remove favorite
        $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
        echo json_encode(["status" => "removed"]);
    } else {
        // Add favorite
        $stmt = $pdo->prepare("INSERT INTO favorites (user_id, product_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $product_id]);
        echo json_encode(["status" => "added"]);
    }
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}