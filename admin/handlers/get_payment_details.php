<?php
require_once '../../config/db.php';
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Payment ID not provided']);
    exit;
}

$payment_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

try {
    $stmt = $conn->prepare("
        SELECT 
            p.*,
            b.booking_reference,
            b.guest_name,
            b.guest_email,
            b.guest_phone,
            b.check_in_date,
            b.check_out_date,
            h.hotel_name,
            rt.type_name as room_type,
            r.room_number,
            u.first_name as verified_by_name,
            u.last_name as verified_by_lastname
        FROM payments p
        JOIN bookings b ON p.booking_id = b.booking_id
        JOIN hotels h ON b.hotel_id = h.hotel_id
        JOIN room_types rt ON b.room_type_id = rt.room_type_id
        JOIN room_bookings rb ON b.booking_id = rb.booking_id
        JOIN rooms r ON rb.room_id = r.room_id
        LEFT JOIN users u ON p.verified_by = u.user_id
        WHERE p.payment_id = ?
    ");

    $stmt->execute([$payment_id]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$payment) {
        throw new Exception('Payment not found');
    }

    // Generate HTML for payment details
    $html = '
    <div class="payment-details">
        <div class="row mb-4">
            <div class="col-md-6">
                <h6 class="mb-3">Payment Information</h6>
                <p><strong>Payment ID:</strong> ' . htmlspecialchars($payment['payment_id']) . '</p>
                <p><strong>Amount:</strong> LKR ' . number_format($payment['amount'], 2) . '</p>
                <p><strong>Method:</strong> ' . ucfirst(str_replace('_', ' ', $payment['payment_method'])) . '</p>
                <p><strong>Status:</strong> ' . ucfirst($payment['status']) . '</p>
                <p><strong>Date:</strong> ' . date('M d, Y H:i', strtotime($payment['payment_date'])) . '</p>
                ' . ($payment['payment_deadline'] ? '<p><strong>Deadline:</strong> ' . date('M d, Y H:i', strtotime($payment['payment_deadline'])) . '</p>' : '') . '
            </div>
            <div class="col-md-6">
                <h6 class="mb-3">Booking Information</h6>
                <p><strong>Reference:</strong> ' . htmlspecialchars($payment['booking_reference']) . '</p>
                <p><strong>Hotel:</strong> ' . htmlspecialchars($payment['hotel_name']) . '</p>
                <p><strong>Room Type:</strong> ' . htmlspecialchars($payment['room_type']) . '</p>
                <p><strong>Room Number:</strong> ' . htmlspecialchars($payment['room_number']) . '</p>
                <p><strong>Check-in:</strong> ' . date('M d, Y', strtotime($payment['check_in_date'])) . '</p>
                <p><strong>Check-out:</strong> ' . date('M d, Y', strtotime($payment['check_out_date'])) . '</p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <h6 class="mb-3">Guest Information</h6>
                <p><strong>Name:</strong> ' . htmlspecialchars($payment['guest_name']) . '</p>
                <p><strong>Email:</strong> ' . htmlspecialchars($payment['guest_email']) . '</p>
                <p><strong>Phone:</strong> ' . htmlspecialchars($payment['guest_phone']) . '</p>
            </div>';

    if ($payment['payment_method'] === 'bank_transfer') {
        $html .= '
            <div class="col-md-6">
                <h6 class="mb-3">Bank Transfer Details</h6>
                <p><strong>Bank Name:</strong> ' . htmlspecialchars($payment['bank_name']) . '</p>
                <p><strong>Reference:</strong> ' . htmlspecialchars($payment['bank_reference']) . '</p>
                <p><strong>Transfer Date:</strong> ' . date('M d, Y', strtotime($payment['transfer_date'])) . '</p>
                ' . ($payment['bank_slip'] ? '<p><strong>Bank Slip:</strong> <a href="../' . htmlspecialchars($payment['bank_slip']) . '" target="_blank">View Slip</a></p>' : '') . '
            </div>';
    }

    $html .= '</div>';

    if ($payment['verified_by']) {
        $html .= '
        <div class="row mb-4">
            <div class="col-12">
                <h6 class="mb-3">Verification Information</h6>
                <p><strong>Verified By:</strong> ' . htmlspecialchars($payment['verified_by_name'] . ' ' . $payment['verified_by_lastname']) . '</p>
                <p><strong>Verified At:</strong> ' . date('M d, Y H:i', strtotime($payment['verified_at'])) . '</p>
                ' . ($payment['notes'] ? '<p><strong>Notes:</strong> ' . nl2br(htmlspecialchars($payment['notes'])) . '</p>' : '') . '
            </div>
        </div>';
    }

    $html .= '</div>';

    echo json_encode(['status' => 'success', 'html' => $html]);
} catch (Exception $e) {
    error_log("Error in get_payment_details.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
