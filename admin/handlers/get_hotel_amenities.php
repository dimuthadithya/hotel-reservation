<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

try {
    $hotel_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if (!$hotel_id) {
        throw new Exception('Invalid hotel ID');
    }

    // Get all amenities for this hotel
    $sql = "SELECT a.amenity_id 
            FROM hotel_amenities ha 
            JOIN amenities a ON ha.amenity_id = a.amenity_id 
            WHERE ha.hotel_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$hotel_id]);
    $amenities = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode(['status' => 'success', 'data' => $amenities]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
