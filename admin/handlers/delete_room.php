<?php
require_once '../../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../rooms.php');
    exit;
}

if (!isset($_POST['room_id'])) {
    $_SESSION['error'] = 'Invalid request.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

try {
    $room_id = intval($_POST['room_id']);

    // Start transaction
    $conn->beginTransaction();

    // First check if the room exists and get its hotel_id
    $check_sql = "SELECT r.*, h.hotel_name FROM rooms r 
                  JOIN hotels h ON r.hotel_id = h.hotel_id 
                  WHERE r.room_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->execute([$room_id]);
    $room = $check_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$room) {
        throw new Exception("Room not found");
    }

    // Check for existing bookings
    $booking_sql = "SELECT COUNT(*) FROM room_bookings WHERE room_id = ? AND status != 'cancelled'";
    $booking_stmt = $conn->prepare($booking_sql);
    $booking_stmt->execute([$room_id]);
    $has_bookings = $booking_stmt->fetchColumn() > 0;

    if ($has_bookings) {
        throw new Exception("Cannot delete room: There are existing bookings for this room");
    }

    // Delete the room
    $delete_sql = "DELETE FROM rooms WHERE room_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->execute([$room_id]);

    // Commit transaction
    $conn->commit();
    $_SESSION['success'] = "Room {$room['room_number']} deleted successfully";
} catch (Exception $e) {
    // Rollback transaction on error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    $_SESSION['error'] = $e->getMessage();
}

// Redirect back
$hotel_id = isset($_POST['hotel_id']) ? intval($_POST['hotel_id']) : null;
if ($hotel_id) {
    $redirect_url = "../rooms.php?hotel_id=$hotel_id";
} else {
    $redirect_url = "../rooms.php";
}
header("Location: $redirect_url");
exit;
