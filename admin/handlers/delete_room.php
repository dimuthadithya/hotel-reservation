<?php
require_once '../../config/db.php';
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = 'Unauthorized access';
    header('Location: ../rooms.php');
    exit;
}

// Check if room_id is provided
if (!isset($_POST['room_id'])) {
    $_SESSION['error'] = 'Room ID is required';
    header('Location: ../rooms.php');
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
        $_SESSION['error'] = 'Cannot delete room: It has existing active bookings';
        header('Location: ../rooms.php');
        exit;
    }

    // Delete room
    $stmt = $conn->prepare("DELETE FROM rooms WHERE room_id = ?");
    if ($stmt->execute([$room_id])) {
        $_SESSION['success'] = 'Room deleted successfully';
    } else {
        $_SESSION['error'] = 'Error deleting room';
    }
} catch (Exception $e) {
    $_SESSION['error'] = 'Database error: ' . $e->getMessage();
}

header('Location: ../rooms.php');
exit;
