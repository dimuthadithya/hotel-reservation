<?php
session_start();
require_once 'config/db.php';

// Check if booking ID is provided
$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;

if (!$booking_id) {
  header('Location: index.php');
  exit;
}

// Fetch booking details
$sql = "SELECT b.*, h.hotel_name, h.address, h.contact_phone, h.contact_email,
               h.district, h.province, rt.type_name as room_type,
               r.room_number
        FROM bookings b
        JOIN hotels h ON b.hotel_id = h.hotel_id
        JOIN room_types rt ON b.room_type_id = rt.room_type_id
        JOIN room_bookings rb ON b.booking_id = rb.booking_id
        JOIN rooms r ON rb.room_id = r.room_id
        WHERE b.booking_id = :booking_id";

$stmt = $conn->prepare($sql);
$stmt->execute(['booking_id' => $booking_id]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
  $_SESSION['error'] = "Booking not found";
  header('Location: index.php');
  exit;
}

// Format dates
$check_in_date = new DateTime($booking['check_in_date']);
$check_out_date = new DateTime($booking['check_out_date']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Booking Confirmation - Pearl Stay</title>
  <!-- Favicon -->
  <link
    rel="apple-touch-icon"
    sizes="180x180"
    href="assets/favicon_io/apple-touch-icon.png" />
  <link
    rel="icon"
    type="image/png"
    sizes="32x32"
    href="assets/favicon_io/favicon-32x32.png" />
  <link
    rel="icon"
    type="image/png"
    sizes="16x16"
    href="assets/favicon_io/favicon-16x16.png" />
  <link rel="manifest" href="assets/favicon_io/site.webmanifest" />
  <!-- Bootstrap CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet" />
  <!-- Font Awesome -->
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    rel="stylesheet" />
  <!-- Custom CSS -->
  <link href="assets/css/styles.css" rel="stylesheet" />
  <link href="assets/css/nav.css" rel="stylesheet" />
  <link href="assets/css/footer.css" rel="stylesheet" />
  <link href="assets/css/confirmation.css" rel="stylesheet" />
</head>

<body>
  <?php include 'components/nav.php'; ?>

  <!-- Confirmation Content -->
  <div class="confirmation-container">
    <div class="container">
      <div class="confirmation-card">
        <!-- Success Header -->
        <div class="success-header">
          <div class="success-icon">
            <i class="fas fa-check"></i>
          </div>
          <h1>Booking Request Received!</h1>
          <p class="text-muted">
            Thank you for choosing Pearl Stay. Your booking request has been received and is pending confirmation.
            <?php if ($booking['booking_status'] === 'pending'): ?>
              <br>Our team will review your booking and contact you shortly.
            <?php endif; ?>
          </p>
        </div>

        <!-- Booking Reference -->
        <div class="booking-reference">
          <p class="mb-1">Your Booking Reference</p>
          <div class="reference-number"><?= htmlspecialchars($booking['booking_reference']) ?></div>
        </div>

        <!-- Status Alert -->
        <div class="alert alert-info mb-4">
          <i class="fas fa-info-circle me-2"></i>
          Your booking is currently <strong>pending confirmation</strong>. We will contact you shortly to confirm your reservation.
          Please note that room availability will be confirmed during this process.
        </div>

        <!-- Reservation Summary -->
        <div class="confirmation-section">
          <h2 class="section-title">
            <i class="fas fa-file-alt"></i>
            Reservation Summary
          </h2>
          <div class="detail-grid">
            <div class="detail-item" id="hotelName">
              <span class="detail-label">Hotel</span>
              <span class="detail-value"><?= htmlspecialchars($booking['hotel_name']) ?></span>
            </div>
            <div class="detail-item" id="roomType">
              <span class="detail-label">Room Type</span>
              <span class="detail-value"><?= htmlspecialchars($booking['room_type']) ?> (Room <?= htmlspecialchars($booking['room_number']) ?>)</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Total Amount</span>
              <span class="detail-value">LKR <?= number_format($booking['total_amount'], 2) ?></span>
            </div>
          </div>
        </div>

        <!-- Next Steps -->
        <div class="confirmation-section">
          <h2 class="section-title">
            <i class="fas fa-tasks"></i>
            Next Steps
          </h2>
          <div class="next-steps">
            <ol>
              <li>Wait for our confirmation call or email</li>
              <li>Upon confirmation, complete the payment within 12 hours</li>
              <li>Receive your final booking confirmation</li>
            </ol>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons text-center mt-4">
          <a href="dashboard.php#bookings" class="btn btn-primary">
            <i class="fas fa-history me-2"></i>Check Booking Status
          </a>
          <a href="index.php" class="btn btn-outline-secondary">
            <i class="fas fa-home me-2"></i>Return to Home
          </a>
        </div>
      </div>
    </div>
  </div>

  <?php include 'components/footer.php'; ?>

  <!-- Bootstrap & jQuery JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Custom JS -->
  <script src="assets/js/confirmation.js"></script>
</body>

</html>