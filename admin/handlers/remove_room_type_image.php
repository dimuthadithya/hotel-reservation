<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['room_type_id']) || !isset($data['image_index'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
    exit;
}

$room_type_id = intval($data['room_type_id']);
$image_index = intval($data['image_index']);

try {
    // Get current images
    $stmt = $conn->prepare("SELECT images FROM room_types WHERE room_type_id = ?");
    $stmt->execute([$room_type_id]);
    $row = $stmt->fetch();

    if (!$row) {
        echo json_encode(['status' => 'error', 'message' => 'Room type not found']);
        exit;
    }

    $images = json_decode($row['images'] ?? '[]', true);

    if (!isset($images[$image_index])) {
        echo json_encode(['status' => 'error', 'message' => 'Image not found']);
        exit;
    }

    // Get the image path to delete
    $image_to_delete = $images[$image_index];
    $full_path = '../../' . $image_to_delete;

    // Remove the file if it exists
    if (file_exists($full_path)) {
        unlink($full_path);
    }

    // Remove the image from the array
    array_splice($images, $image_index, 1);

    // Update the database
    $stmt = $conn->prepare("UPDATE room_types SET images = ? WHERE room_type_id = ?");
    $stmt->execute([json_encode($images), $room_type_id]);

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
}
