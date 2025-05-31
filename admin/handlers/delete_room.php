<?php
require_once '../../config/db.php';
session_start();

header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

// Check if room_id is provided
if (!isset($_POST['room_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Room ID is required']);
    exit;
}

$room_id = filter_var($_POST['room_id'], FILTER_SANITIZE_NUMBER_INT);

try {
    // Check if room has any active bookings
    $stmt = $conn->prepare("
        SELECT rb.room_id 
        FROM room_bookings rb 
        JOIN bookings b ON rb.booking_id = b.booking_id 
        WHERE rb.room_id = ? 
        AND b.booking_status NOT IN ('cancelled', 'checked_out')
    ");
    $stmt->execute([$room_id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Cannot delete room: It has existing active bookings']);
        exit;
    }

    // Delete room
    $stmt = $conn->prepare("DELETE FROM rooms WHERE room_id = ?");
    if ($stmt->execute([$room_id])) {
        echo json_encode(['status' => 'success', 'message' => 'Room deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting room']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    exit;
}
