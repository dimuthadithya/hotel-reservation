<?php
session_start();
require_once '../config/db.php';
require_once '../includes/booking_validation.php';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Invalid request method";
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

// Verify CSRF token
if (
    !isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    $_SESSION['error'] = "Invalid form submission";
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

// Clear the CSRF token
unset($_SESSION['csrf_token']);

try {
    // Validate and sanitize input data
    $validation = validateBookingData($_POST);

    if (!$validation['valid']) {
        throw new Exception(implode("<br>", $validation['errors']));
    }

    $data = $validation['sanitized_data'];
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    $conn->beginTransaction();

    // Get available room
    $room_sql = "SELECT r.room_id, rt.base_price 
                 FROM rooms r
                 JOIN room_types rt ON r.room_type_id = rt.room_type_id
                 WHERE r.room_type_id = ? 
                 AND r.status = 'available'
                 AND NOT EXISTS (
                     SELECT 1 FROM room_bookings rb
                     JOIN bookings b ON rb.booking_id = b.booking_id
                     WHERE rb.room_id = r.room_id
                     AND b.booking_status NOT IN ('cancelled', 'checked_out')
                     AND (
                         (b.check_in_date <= ? AND b.check_out_date >= ?)
                         OR (b.check_in_date <= ? AND b.check_out_date >= ?)
                         OR (b.check_in_date >= ? AND b.check_out_date <= ?)
                     )
                 )
                 LIMIT 1";

    $room_stmt = $conn->prepare($room_sql);
    $room_stmt->execute([
        $data['room_type_id'],
        $data['check_in'],
        $data['check_in'],
        $data['check_out'],
        $data['check_out'],
        $data['check_in'],
        $data['check_out']
    ]);

    $room = $room_stmt->fetch(PDO::FETCH_ASSOC);
    if (!$room) {
        throw new Exception("No rooms available for the selected dates");
    }

    // Calculate total amount
    $check_in_date = new DateTime($data['check_in']);
    $check_out_date = new DateTime($data['check_out']);
    $nights = $check_in_date->diff($check_out_date)->days;
    $base_amount = $room['base_price'] * $nights;
    $tax_amount = $base_amount * 0.1; // 10% tax
    $total_amount = $base_amount + $tax_amount;

    // Generate unique booking reference
    $booking_reference = 'BK' . date('y') . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);

    // Get hotel_id from room_type
    $hotel_sql = "SELECT hotel_id FROM room_types WHERE room_type_id = ?";
    $hotel_stmt = $conn->prepare($hotel_sql);
    $hotel_stmt->execute([$data['room_type_id']]);
    $hotel_id = $hotel_stmt->fetchColumn();

    if (!$hotel_id) {
        throw new Exception("Invalid room type selected");
    }

    // Create booking
    $booking_sql = "INSERT INTO bookings (
        booking_reference, user_id, hotel_id, room_type_id,
        check_in_date, check_out_date, adults, children,
        total_nights, rooms_booked, room_rate, taxes,
        service_charges, total_amount, currency,
        booking_status, payment_status, special_requests,
        guest_name, guest_email, guest_phone, booking_source
    ) VALUES (
        ?, ?, ?, ?,
        ?, ?, ?, ?,
        ?, 1, ?, ?,
        0.00, ?, 'LKR',
        'pending', 'pending', ?,
        ?, ?, ?, 'website'
    )";

    $booking_stmt = $conn->prepare($booking_sql);
    $booking_success = $booking_stmt->execute([
        $booking_reference,          // booking_reference
        $user_id,                    // user_id
        $hotel_id,                   // hotel_id
        $data['room_type_id'],       // room_type_id
        $data['check_in'],           // check_in_date
        $data['check_out'],          // check_out_date
        $data['adults'],             // adults
        $data['children'],           // children
        $nights,                     // total_nights
        $base_amount / $nights,      // room_rate (per night)
        $tax_amount,                 // taxes
        $total_amount,               // total_amount
        $data['special_requests'],   // special_requests
        $data['first_name'] . ' ' . $data['last_name'],  // guest_name
        $data['email'],              // guest_email
        $data['phone']               // guest_phone
    ]);

    if (!$booking_success) {
        throw new Exception("Failed to create booking");
    }

    $booking_id = $conn->lastInsertId();

    // Create room booking
    $room_booking_sql = "INSERT INTO room_bookings (booking_id, room_id) VALUES (?, ?)";
    $room_booking_stmt = $conn->prepare($room_booking_sql);
    if (!$room_booking_stmt->execute([$booking_id, $room['room_id']])) {
        throw new Exception("Failed to create room booking");
    }

    $conn->commit();

    // Set success message
    $_SESSION['success'] = "Booking request received successfully!";

    // Check if it's an AJAX request
    if (
        !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
    ) {
        // Send JSON response for AJAX requests
        echo json_encode([
            'success' => true,
            'booking_id' => $booking_id,
            'message' => 'Booking request received successfully!'
        ]);
        exit;
    }

    // Regular form submission - redirect
    header("Location: ../confirmation.php?booking_id=" . $booking_id);
    exit;
} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }

    $error_message = $e->getMessage();
    $_SESSION['error'] = $error_message;

    // Check if it's an AJAX request
    if (
        !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
    ) {
        // Send JSON response for AJAX requests
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $error_message
        ]);
        exit;
    }

    // Regular form submission - redirect back
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}
