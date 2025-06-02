<?php
require_once '../../config/db.php';
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

// Check if required parameters are provided
if (!isset($_POST['payment_id']) || !isset($_POST['status'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
    exit;
}

$payment_id = filter_var($_POST['payment_id'], FILTER_SANITIZE_NUMBER_INT);
$status = filter_var($_POST['status'], FILTER_SANITIZE_STRING);
$notes = isset($_POST['notes']) ? filter_var($_POST['notes'], FILTER_SANITIZE_STRING) : null;

try {
    $conn->beginTransaction();

    // Get payment details
    $stmt = $conn->prepare("
        SELECT p.*, b.booking_id, b.booking_status, b.payment_status
        FROM payments p
        JOIN bookings b ON p.booking_id = b.booking_id
        WHERE p.payment_id = ? AND p.status = 'pending'
    ");

    $stmt->execute([$payment_id]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$payment) {
        throw new Exception('Invalid payment or payment already processed');
    }

    // Update payment status
    $updatePayment = $conn->prepare("
        UPDATE payments 
        SET status = ?,
            verified_by = ?,
            verified_at = NOW(),
            notes = CONCAT(COALESCE(notes, ''), '\nAdmin Notes: ', ?)
        WHERE payment_id = ?
    ");

    if (!$updatePayment->execute([$status, $_SESSION['user_id'], $notes, $payment_id])) {
        throw new Exception('Failed to update payment status');
    }

    // If payment is completed, update booking payment status
    if ($status === 'completed') {
        $updateBooking = $conn->prepare("
            UPDATE bookings 
            SET payment_status = 'paid',
                payment_date = NOW()
            WHERE booking_id = ?
        ");

        if (!$updateBooking->execute([$payment['booking_id']])) {
            throw new Exception('Failed to update booking status');
        }
    } else if ($status === 'failed') {
        // If payment verification failed, cancel the booking
        $updateBooking = $conn->prepare("
            UPDATE bookings 
            SET booking_status = 'cancelled',
                payment_status = 'cancelled'
            WHERE booking_id = ?
        ");

        if (!$updateBooking->execute([$payment['booking_id']])) {
            throw new Exception('Failed to cancel booking');
        }

        // Update room status to available
        $updateRoom = $conn->prepare("
            UPDATE rooms r
            JOIN room_bookings rb ON r.room_id = rb.room_id
            SET r.status = 'available'
            WHERE rb.booking_id = ?
        ");

        if (!$updateRoom->execute([$payment['booking_id']])) {
            throw new Exception('Failed to update room status');
        }
    }

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Payment status updated successfully']);
} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    error_log("Error in verify_payment.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
