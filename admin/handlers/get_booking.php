<?php
require_once '../../config/db.php';
session_start();

header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

// Check if booking ID is provided
if (!isset($_GET['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Booking ID is required']);
    exit;
}

$booking_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

try {
    // Get booking details with related information
    $sql = "SELECT b.*, h.hotel_name, rt.type_name as room_type, r.room_number 
            FROM bookings b 
            JOIN hotels h ON b.hotel_id = h.hotel_id 
            JOIN room_types rt ON b.room_type_id = rt.room_type_id
            JOIN room_bookings rb ON b.booking_id = rb.booking_id
            JOIN rooms r ON rb.room_id = r.room_id
            WHERE b.booking_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$booking_id]);

    if ($booking = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo json_encode([
            'status' => 'success',
            'booking' => $booking
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Booking not found']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
