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

    $bank_slip_path = null;

    // Handle bank transfer
    if ($payment_method === 'bank_transfer') {
        // Validate required fields
        if (empty($_POST['bank_name']) || empty($_POST['bank_reference']) || empty($_POST['transfer_date'])) {
            throw new Exception('Please fill in all required fields');
        }

        // Handle file upload
        if (isset($_FILES['bank_slip']) && $_FILES['bank_slip']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
            $filename = $_FILES['bank_slip']['name'];
            $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (!in_array($filetype, $allowed)) {
                throw new Exception('Invalid file type. Only JPG, PNG and PDF files are allowed');
            }

            // Generate unique filename
            $new_filename = 'slip_' . $booking['booking_reference'] . '_' . uniqid() . '.' . $filetype;
            $upload_path = '../uploads/slips/' . $new_filename;

            // Create directory if it doesn't exist
            if (!file_exists('../uploads/slips')) {
                mkdir('../uploads/slips', 0777, true);
            }

            if (!move_uploaded_file($_FILES['bank_slip']['tmp_name'], $upload_path)) {
                throw new Exception('Failed to upload bank slip');
            }

            $bank_slip_path = 'uploads/slips/' . $new_filename;
        } else {
            throw new Exception('Bank slip is required');
        }
    }

    // Record the payment with deadline
    $payment_sql = "INSERT INTO payments (
        booking_id, amount, payment_method, bank_slip, 
        bank_reference, transfer_date, bank_name, notes, status,
        payment_deadline
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL 12 HOUR))";

    $payment_stmt = $conn->prepare($payment_sql);
    if (!$payment_stmt->execute([
        $booking_id,
        $booking['total_amount'],
        $payment_method,
        $bank_slip_path,
        $_POST['bank_reference'] ?? null,
        $_POST['transfer_date'] ?? null,
        $_POST['bank_name'] ?? null,
        $_POST['notes'] ?? null,
        'pending'
    ])) {
        throw new Exception('Failed to record payment');
    }

    // Update booking payment status
    $update_booking = $conn->prepare("
        UPDATE bookings 
        SET payment_status = 'pending'
        WHERE booking_id = ?
    ");

    if (!$update_booking->execute([$booking_id])) {
        throw new Exception('Failed to update booking status');
    }

    $conn->commit();

    // Set success message based on payment method
    if ($payment_method === 'bank_transfer') {
        $_SESSION['success'] = 'Bank transfer details submitted successfully. Your payment is being verified.';
    } else { // cash payment
        $_SESSION['success'] = 'Cash payment option confirmed. Please visit our office to complete the payment.';
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
