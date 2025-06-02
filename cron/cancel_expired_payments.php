<?php
require_once '../config/db.php';

try {
    $conn->beginTransaction();

    // Get expired payments
    $stmt = $conn->prepare("
        SELECT p.payment_id, p.booking_id
        FROM payments p
        WHERE p.status = 'pending'
        AND p.payment_deadline < NOW()
    ");

    $stmt->execute();
    $expired_payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($expired_payments as $payment) {
        // Update payment status to expired
        $update_payment = $conn->prepare("
            UPDATE payments 
            SET status = 'expired' 
            WHERE payment_id = ?
        ");
        $update_payment->execute([$payment['payment_id']]);

        // Update booking status to cancelled
        $update_booking = $conn->prepare("
            UPDATE bookings 
            SET booking_status = 'cancelled',
                payment_status = 'cancelled'
            WHERE booking_id = ?
        ");
        $update_booking->execute([$payment['booking_id']]);

        // Optional: Send email notification to user about cancelled booking
        // TODO: Implement email notification
    }

    $conn->commit();
    echo "Successfully processed " . count($expired_payments) . " expired payments\n";
} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    error_log("Error in cancel_expired_payments.php: " . $e->getMessage());
    echo "Error: " . $e->getMessage() . "\n";
}
