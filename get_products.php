<?php
session_start();

// DB connection
require_once 'db.php';

try {
    // Get the current user ID from the session, or 0 if they are a guest
    $user_id = $_SESSION['user_id'] ?? 0;

    $sql = "SELECT p.*, 
            (SELECT COUNT(*) FROM favorites f WHERE f.product_id = p.id AND f.user_id = ?) as is_favorite
            FROM products p 
            ORDER BY p.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');

    // Output results
    echo json_encode($products);

} catch (\PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(["error" => "Could not fetch products: " . $e->getMessage()]);
}
?>