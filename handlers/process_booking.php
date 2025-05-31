<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

// Validate required fields
$required_fields = [
    'room_type_id',
    'first_name',
    'last_name',
    'email',
    'phone',
    'check_in',
    'check_out',
    'adults'
];

foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty($_POST[$field])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => "Missing required field: $field"]);
        exit;
    }
}

try {
    $conn->beginTransaction();

    // Sanitize and validate input
    $room_type_id = filter_var($_POST['room_type_id'], FILTER_SANITIZE_NUMBER_INT);
    $first_name = htmlspecialchars($_POST['first_name'], ENT_QUOTES, 'UTF-8');
    $last_name = htmlspecialchars($_POST['last_name'], ENT_QUOTES, 'UTF-8');
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars($_POST['phone'], ENT_QUOTES, 'UTF-8');
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $adults = filter_var($_POST['adults'], FILTER_SANITIZE_NUMBER_INT);
    $children = isset($_POST['children']) ? filter_var($_POST['children'], FILTER_SANITIZE_NUMBER_INT) : 0;
    $special_requests = isset($_POST['special_requests']) ?
        htmlspecialchars($_POST['special_requests'], ENT_QUOTES, 'UTF-8') : '';
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    // Validate dates
    $check_in_date = new DateTime($check_in);
    $check_out_date = new DateTime($check_out);
    $today = new DateTime();

    if ($check_in_date < $today) {
        throw new Exception("Check-in date cannot be in the past");
    }
    if ($check_out_date <= $check_in_date) {
        throw new Exception("Check-out date must be after check-in date");
    }

    // Get available room
    $room_sql = "SELECT r.room_id, rt.base_price 
                 FROM rooms r
                 JOIN room_types rt ON r.room_type_id = rt.room_type_id
                 WHERE r.room_type_id = ? 
                 AND r.status = 'available'
                 AND NOT EXISTS (                     SELECT 1 FROM room_bookings rb
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
        $room_type_id,
        $check_in,
        $check_in,
        $check_out,
        $check_out,
        $check_in,
        $check_out
    ]);
    $room = $room_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$room) {
        throw new Exception("No rooms available for the selected dates");
    }

    // Calculate total amount
    $nights = $check_in_date->diff($check_out_date)->days;
    $base_amount = $room['base_price'] * $nights;
    $tax_amount = $base_amount * 0.1; // 10% tax
    $total_amount = $base_amount + $tax_amount;    // Create booking
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
    )"; // Generate unique booking reference
    $booking_reference = 'BK' . date('y') . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);

    // Get hotel_id from room_type
    $hotel_sql = "SELECT hotel_id FROM room_types WHERE room_type_id = ?";
    $hotel_stmt = $conn->prepare($hotel_sql);
    $hotel_stmt->execute([$room_type_id]);
    $hotel_id = $hotel_stmt->fetchColumn();

    $booking_stmt = $conn->prepare($booking_sql);
    $booking_stmt->execute([
        $booking_reference,          // booking_reference
        $user_id,                    // user_id
        $hotel_id,                   // hotel_id
        $room_type_id,               // room_type_id
        $check_in,                   // check_in_date
        $check_out,                  // check_out_date
        $adults,                     // adults
        $children,                   // children
        $nights,                     // total_nights
        $base_amount / $nights,      // room_rate (per night)
        $tax_amount,                 // taxes
        $total_amount,               // total_amount
        $special_requests,           // special_requests
        $first_name . ' ' . $last_name,  // guest_name
        $email,                      // guest_email
        $phone                       // guest_phone
    ]);

    $booking_id = $conn->lastInsertId();    // Create room booking
    $room_booking_sql = "INSERT INTO room_bookings (booking_id, room_id)
                        VALUES (?, ?)";
    $room_booking_stmt = $conn->prepare($room_booking_sql);
    $room_booking_stmt->execute([$booking_id, $room['room_id']]);

    // Update room status
    $update_room_sql = "UPDATE rooms SET status = 'occupied' WHERE room_id = ?";
    $update_room_stmt = $conn->prepare($update_room_sql);
    $update_room_stmt->execute([$room['room_id']]);
    $conn->commit();

    // Set success message in session
    $_SESSION['success'] = "Booking completed successfully!";

    // Redirect to confirmation page
    header("Location: ../confirmation.php?booking_id=" . $booking_id);
    exit;
} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    // Store error in session
    $_SESSION['error'] = $e->getMessage();
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}
