<?php
require_once '../../config/db.php';

header('Content-Type: application/json');

if (!isset($_GET['id']) || !isset($_GET['hotel_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Room type ID and hotel ID are required']);
    exit;
}

$id = intval($_GET['id']);
$hotel_id = intval($_GET['hotel_id']);

try {
    // Get room type details
    $sql = "SELECT * FROM room_types WHERE room_type_id = ? AND hotel_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id, $hotel_id]);
    $roomType = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$roomType) {
        echo json_encode(['status' => 'error', 'message' => 'Room type not found']);
        exit;
    }

    // Convert image paths to full URLs
    if (!empty($roomType['images'])) {
        $images = json_decode($roomType['images'], true);
        $images = array_map(function ($path) {
            return '../../' . $path;
        }, $images);
        $roomType['images'] = json_encode($images);
    }

    echo json_encode(['status' => 'success', 'data' => $roomType]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
}
