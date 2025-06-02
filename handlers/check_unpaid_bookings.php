<?php
require_once '../config/db.php';

try {
    $conn->beginTransaction();

    // Get all confirmed bookings that are unpaid and past the deadline
    $sql = "SELECT b.booking_id, rb.room_id 
            FROM bookings b
            JOIN room_bookings rb ON b.booking_id = rb.booking_id
            WHERE b.booking_status = 'confirmed' 
            AND b.payment_status = 'pending'
            AND b.payment_deadline < NOW()";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $unpaid_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($unpaid_bookings as $booking) {
        // Update booking status to cancelled
        $update_booking = $conn->prepare("
            UPDATE bookings 
            SET booking_status = 'cancelled',
                payment_status = 'cancelled'
            WHERE booking_id = ?
        ");
        $update_booking->execute([$booking['booking_id']]);

        // Update room status to available
        $update_room = $conn->prepare("
            UPDATE rooms 
            SET status = 'available'
            WHERE room_id = ?
        ");
        $update_room->execute([$booking['room_id']]);
    }

    $conn->commit();
    echo "Successfully processed " . count($unpaid_bookings) . " unpaid bookings.";
} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    error_log("Error in check_unpaid_bookings.php: " . $e->getMessage());
    echo "Error processing unpaid bookings: " . $e->getMessage();
}
