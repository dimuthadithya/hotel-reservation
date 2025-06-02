<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Please login to make a payment';
    header('Location: ../login.php');
    exit;
}

if (!isset($_POST['booking_id']) || !isset($_POST['payment_method'])) {
    $_SESSION['error'] = 'Invalid payment request';
    header('Location: ../dashboard.php');
    exit;
}

$booking_id = filter_var($_POST['booking_id'], FILTER_SANITIZE_NUMBER_INT);
$payment_method = filter_var($_POST['payment_method'], FILTER_SANITIZE_STRING);

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

    // Record the payment method selection
    $payment_sql = "INSERT INTO payments (booking_id, amount, payment_method, status) 
                   VALUES (?, ?, ?, 'pending')";
    $payment_stmt = $conn->prepare($payment_sql);
    if (!$payment_stmt->execute([$booking_id, $booking['total_amount'], $payment_method])) {
        throw new Exception('Failed to record payment method');
    }

    $conn->commit();

    // Prepare instructions based on payment method
    if ($payment_method === 'bank_transfer') {
        $_SESSION['success'] = 'Please transfer the amount to:<br>
            Bank: Sample Bank<br>
            Account Name: Pearl Stay<br>
            Account Number: 1234567890<br>
            Branch: Sample Branch<br>
            Reference: ' . $booking['booking_reference'];
    } else { // cash payment
        $_SESSION['success'] = 'Please visit our office to make the cash payment.<br>
            Address: Sample Address<br>
            Office Hours: 9 AM - 5 PM<br>
            Reference: ' . $booking['booking_reference'];
    }
} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    error_log("Error in process_payment.php: " . $e->getMessage());
    $_SESSION['error'] = $e->getMessage();
}

header('Location: ../dashboard.php#bookings');
exit;
