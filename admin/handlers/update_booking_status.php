<?php
require_once '../../config/db.php';
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = 'Unauthorized access';
    header('Location: ../bookings.php');
    exit;
}

// Check if required parameters are provided
if (!isset($_POST['booking_id']) || !isset($_POST['status'])) {
    $_SESSION['error'] = 'Missing required parameters';
    header('Location: ../bookings.php');
    exit;
}

$booking_id = filter_var($_POST['booking_id'], FILTER_SANITIZE_NUMBER_INT);
$new_status = filter_var($_POST['status'], FILTER_SANITIZE_STRING);

// Validate status
$allowed_statuses = ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'];
if (!in_array($new_status, $allowed_statuses)) {
    $_SESSION['error'] = 'Invalid status';
    header('Location: ../bookings.php');
    exit;
}

try {
    $conn->beginTransaction();

    // Get current booking status and room_id
    $stmt = $conn->prepare("
        SELECT b.booking_status, rb.room_id
        FROM bookings b
        JOIN room_bookings rb ON b.booking_id = rb.booking_id
        WHERE b.booking_id = ?
    ");
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        throw new Exception('Booking not found');
    }

    // Validate status transition
    $current_status = $booking['booking_status'];
    $valid_transitions = [
        'pending' => ['confirmed', 'cancelled'],
        'confirmed' => ['checked_in', 'cancelled'],
        'checked_in' => ['checked_out'],
        'checked_out' => [],
        'cancelled' => []
    ];

    if (!in_array($new_status, $valid_transitions[$current_status])) {
        throw new Exception("Invalid status transition from {$current_status} to {$new_status}");
    }

    // Update booking status
    $stmt = $conn->prepare("UPDATE bookings SET booking_status = ? WHERE booking_id = ?");
    if (!$stmt->execute([$new_status, $booking_id])) {
        throw new Exception("Failed to update booking status");
    }

    // Update room status if necessary
    $room_status = match ($new_status) {
        'checked_in' => 'occupied',
        'checked_out' => 'available',
        'cancelled' => 'available',
        default => null
    };

    if ($room_status) {
        $stmt = $conn->prepare("UPDATE rooms SET status = ? WHERE room_id = ?");
        if (!$stmt->execute([$room_status, $booking['room_id']])) {
            throw new Exception("Failed to update room status");
        }
    }

    $conn->commit();
    $_SESSION['success'] = 'Booking status updated successfully';
} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    error_log("Error in update_booking_status.php: " . $e->getMessage());
    $_SESSION['error'] = $e->getMessage();
}

header('Location: ../bookings.php');
exit;
