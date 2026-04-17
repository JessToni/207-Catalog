<?php
// DB connection
require_once 'db.php';

try {
    // Prepare the SQL query
    $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Set the header so the browser/Postman knows this is JSON data header
    header('Content-Type: application/json');

    // Output results
    echo json_encode($products);

} catch (\PDOException $e) {
    // Handle errors gracefully
    http_response_code(500);
    echo json_encode(["error" => "Could not fetch products: " . $e->getMessage()]);
}
?>