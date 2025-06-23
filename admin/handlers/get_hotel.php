<?php
require_once '../../config/db.php';

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Hotel ID is required']);
    exit;
}

try {
    $hotel_id = intval($_GET['id']);

    $sql = "SELECT * FROM hotels WHERE hotel_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$hotel_id]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Hotel not found']);
        exit;
    }

    $hotel = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 'success', 'data' => $hotel]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error occurred']);
}
