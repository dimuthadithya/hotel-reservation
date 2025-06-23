<?php
require_once '../../config/db.php';
session_start();

header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit;
}

// Check if required fields are provided
if (
    !isset($_POST['room_id']) || !isset($_POST['room_type_id']) || !isset($_POST['room_number']) ||
    !isset($_POST['floor_number']) || !isset($_POST['status'])
) {
    http_response_code(400);
    exit;
}

// Sanitize and validate input
$room_id = filter_var($_POST['room_id'], FILTER_SANITIZE_NUMBER_INT);
$room_type_id = filter_var($_POST['room_type_id'], FILTER_SANITIZE_NUMBER_INT);
$room_number = htmlspecialchars($_POST['room_number'], ENT_QUOTES, 'UTF-8');
$floor_number = filter_var($_POST['floor_number'], FILTER_SANITIZE_NUMBER_INT);
$status = htmlspecialchars($_POST['status'], ENT_QUOTES, 'UTF-8');

try {
    // Validate inputs
    if (empty($room_number) || strlen($room_number) > 10) {
        throw new Exception('Invalid room number format');
    }

    if ($floor_number < 0) {
        throw new Exception('Invalid floor number');
    }

    if (!in_array($status, ['available', 'occupied', 'maintenance', 'out_of_order'])) {
        throw new Exception('Invalid status');
    }

    // Start transaction
    $conn->beginTransaction();

    // Get current room details
    $current_sql = "SELECT r.*, rt.hotel_id FROM rooms r 
                   JOIN room_types rt ON r.room_type_id = rt.room_type_id 
                   WHERE r.room_id = ?";
    $current_stmt = $conn->prepare($current_sql);
    $current_stmt->execute([$room_id]);
    $current_room = $current_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$current_room) {
        throw new Exception("Room not found");
    }

    // Check if room number already exists in this hotel (excluding current room)
    $check_sql = "SELECT COUNT(*) FROM rooms r 
                 JOIN room_types rt ON r.room_type_id = rt.room_type_id 
                 WHERE rt.hotel_id = ? AND r.room_number = ? AND r.room_id != ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->execute([$current_room['hotel_id'], $room_number, $room_id]);
    if ($check_stmt->fetchColumn() > 0) {
        throw new Exception("Room number already exists in this hotel");
    }

    // Validate that the new room type belongs to the same hotel
    $type_check_sql = "SELECT hotel_id FROM room_types WHERE room_type_id = ?";
    $type_check_stmt = $conn->prepare($type_check_sql);
    $type_check_stmt->execute([$room_type_id]);
    $type_hotel = $type_check_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$type_hotel || $type_hotel['hotel_id'] !== $current_room['hotel_id']) {
        throw new Exception("Invalid room type selected");
    }

    // Check if room has active bookings when trying to change status
    if ($current_room['status'] === 'occupied' && $status !== 'occupied') {
        $booking_sql = "SELECT COUNT(*) FROM room_bookings rb 
                      JOIN bookings b ON rb.booking_id = b.booking_id 
                      WHERE rb.room_id = ? AND b.booking_status IN ('confirmed', 'checked_in')";
        $booking_stmt = $conn->prepare($booking_sql);
        $booking_stmt->execute([$room_id]);
        if ($booking_stmt->fetchColumn() > 0) {
            throw new Exception("Cannot change status: Room has active bookings");
        }
    }

    // Update room details
    $sql = "UPDATE rooms SET 
            room_type_id = ?, 
            room_number = ?, 
            floor_number = ?, 
            status = ?
            WHERE room_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $room_type_id,
        $room_number,
        $floor_number,
        $status,
        $room_id
    ]);    // Commit transaction
    $conn->commit();
    header('Location: ../rooms.php');
    exit;
} catch (Exception $e) {
    // Rollback transaction on error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    $_SESSION['error'] = $e->getMessage();
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}
