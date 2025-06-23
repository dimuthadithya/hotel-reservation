<?php
require_once '../../config/db.php';

if (!isset($_GET['hotel_id'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Hotel ID is required']);
    exit;
}

try {
    $hotel_id = intval($_GET['hotel_id']);

    $sql = "SELECT room_type_id, type_name FROM room_types WHERE hotel_id = ? AND status = 'active' ORDER BY type_name";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$hotel_id]);

    $room_types = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 'success', 'data' => $room_types]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error occurred']);
}
