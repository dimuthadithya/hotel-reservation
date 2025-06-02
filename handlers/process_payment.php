<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Please login to make a payment';
    header('Location: ../login.php');
    exit;
}

if (!isset($_POST['booking_id'])) {
    $_SESSION['error'] = 'Invalid payment request';
    header('Location: ../dashboard.php');
    exit;
}

$booking_id = filter_var($_POST['booking_id'], FILTER_SANITIZE_NUMBER_INT);

try {
    $conn->beginTransaction();

    // Get booking details and verify payment eligibility
    $stmt = $conn->prepare("
        SELECT * FROM bookings 
        WHERE booking_id = ? 
        AND user_id = ? 
        AND booking_status = 'confirmed'
        AND payment_status = 'pending'
        AND payment_deadline > NOW()
    ");

    $stmt->execute([$booking_id, $_SESSION['user_id']]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        throw new Exception('Invalid booking or payment deadline has passed');
    }

    // Process payment (this is where you would integrate with a payment gateway)
    // For now, we'll just mark it as paid
    $update_booking = $conn->prepare("
        UPDATE bookings 
        SET payment_status = 'paid',
            payment_date = NOW()
        WHERE booking_id = ?
    ");

    if (!$update_booking->execute([$booking_id])) {
        throw new Exception('Failed to update payment status');
    }

    // Record the payment
    $payment_sql = "INSERT INTO payments (booking_id, amount, payment_method, status) 
                   VALUES (?, ?, 'direct', 'completed')";
    $payment_stmt = $conn->prepare($payment_sql);
    if (!$payment_stmt->execute([$booking_id, $booking['total_amount']])) {
        throw new Exception('Failed to record payment');
    }

    $conn->commit();
    $_SESSION['success'] = 'Payment processed successfully';
} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    error_log("Error in process_payment.php: " . $e->getMessage());
    $_SESSION['error'] = $e->getMessage();
}

header('Location: ../dashboard.php');
exit;
