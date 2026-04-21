<?php
require_once 'db.php';
require_once 'helpers.php';

// Set header for JSON response
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data from request (POST or JSON input)
    $name = $_POST['name'] ?? 'Unknown Product';
    $pkg = (int)($_POST['packaging'] ?? 0);
    $src = (int)($_POST['sourcing'] ?? 0);
    $lng = (int)($_POST['longevity'] ?? 0);

    // Use the helper function to calculate the total
    $total_score = calculateSustainabilityScore($pkg, $src, $lng);

    try {
        // Prepare and execute the SQL
        $sql = "INSERT INTO products (name, packaging_score, sourcing_score, longevity_score, total_score) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $pkg, $src, $lng, $total_score]);

        echo json_encode(["message" => "Product added successfully!", "total_score" => $total_score]);
    } catch (\PDOException $e){
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
} else {
    echo json_encode(["message" => "Please send a POST request."]);
}