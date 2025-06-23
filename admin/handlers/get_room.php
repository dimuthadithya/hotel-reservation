<?php
require_once '../../config/db.php';
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

// Check if room_id is provided
if (!isset($_GET['room_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Room ID is required']);
    exit;
}

$room_id = $_GET['room_id'];

try {
    // Get room details
    $stmt = $conn->prepare("
        SELECT r.*, h.hotel_id, rt.type_name 
        FROM rooms r 
        JOIN hotels h ON r.hotel_id = h.hotel_id 
        JOIN room_types rt ON r.room_type_id = rt.room_type_id 
        WHERE r.room_id = ?
    ");
    $stmt->execute([$room_id]);

    if ($room = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Get room types for this hotel
        $types_stmt = $conn->prepare("
            SELECT room_type_id, type_name 
            FROM room_types 
            WHERE hotel_id = ? 
            ORDER BY type_name
        ");
        $types_stmt->execute([$room['hotel_id']]);
        $room_types = $types_stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'status' => 'success',
            'room' => $room,
            'room_types' => $room_types
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Room not found']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
