<?php
session_start();
require_once 'config/db.php';
require_once 'includes/utility_functions.php';

// Check if room type ID is provided
$room_type_id = isset($_GET['room_type']) ? (int)$_GET['room_type'] : 0;

if (!$room_type_id) {
    header('Location: index.php');
    exit;
}

// Get room type details
$sql = "SELECT rt.*, h.hotel_name, h.hotel_id, h.main_image as hotel_image, 
               h.address, h.district, h.province
        FROM room_types rt
        JOIN hotels h ON rt.hotel_id = h.hotel_id
        WHERE rt.room_type_id = :room_type_id AND rt.status = 'active'";
$stmt = $conn->prepare($sql);
$stmt->execute(['room_type_id' => $room_type_id]);
$roomType = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$roomType) {
    header('Location: index.php');
    exit;
}

// Calculate total price (you can add tax calculation here)
$basePrice = $roomType['base_price'];
$tax = $basePrice * 0.1; // 10% tax
$totalPrice = $basePrice + $tax;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Pearl Stay</title>
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon_io/favicon-16x16.png">
    <link rel="manifest" href="assets/favicon_io/site.webmanifest">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/styles.css" rel="stylesheet">
    <link href="assets/css/nav.css" rel="stylesheet">
    <link href="assets/css/footer.css" rel="stylesheet">
    <link href="assets/css/checkout.css" rel="stylesheet">
</head>

<body>
    <?php include 'components/nav.php'; ?>

    <div class="checkout-container">
        <div class="container">
            <h1 class="mb-4">Complete Your Booking</h1>

            <div class="row">
                <!-- Booking Form -->
                <div class="col-lg-8">
                    <form id="bookingForm" action="handlers/process_booking.php" method="POST">
                        <input type="hidden" name="room_type_id" value="<?= $roomType['room_type_id'] ?>">

                        <div class="guest-form">
                            <!-- Guest Information -->
                            <div class="form-section">
                                <h3>Guest Information</h3>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">First Name</label>
                                        <input type="text" class="form-control" name="first_name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" name="last_name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Phone</label>
                                        <input type="tel" class="form-control" name="phone" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Stay Details -->
                            <div class="form-section">
                                <h3>Stay Details</h3>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Check-in Date</label>
                                        <input type="date" class="form-control" name="check_in" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Check-out Date</label>
                                        <input type="date" class="form-control" name="check_out" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Number of Adults</label>
                                        <select class="form-select" name="adults" required>
                                            <?php for ($i = 1; $i <= $roomType['max_occupancy']; $i++): ?>
                                                <option value="<?= $i ?>"><?= $i ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Number of Children</label>
                                        <select class="form-select" name="children">
                                            <?php for ($i = 0; $i <= 2; $i++): ?>
                                                <option value="<?= $i ?>"><?= $i ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Special Requests -->
                            <div class="special-requests">
                                <label class="form-label">Special Requests</label>
                                <textarea class="form-control" name="special_requests" rows="3"
                                    placeholder="Let us know if you have any special requests..."></textarea>
                                <small class="text-muted">Special requests cannot be guaranteed but we will try our best to meet your needs.</small>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Booking Summary -->
                <div class="col-lg-4">
                    <div class="booking-summary">
                        <h3>Booking Summary</h3>

                        <div class="hotel-info-summary">
                            <img src="<?= $roomType['hotel_image'] ? 'uploads/img/hotels/' . $roomType['hotel_id'] . '/' . $roomType['hotel_image'] : 'assets/img/luxury-suite.jpg' ?>"
                                alt="<?= htmlspecialchars($roomType['hotel_name']) ?>">
                            <h4><?= htmlspecialchars($roomType['hotel_name']) ?></h4>
                            <p class="text-muted">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                <?= htmlspecialchars($roomType['address']) ?>
                            </p>
                        </div>

                        <div class="room-details">
                            <h5><?= htmlspecialchars($roomType['type_name']) ?></h5>
                            <div class="room-features">
                                <p><i class="fas fa-bed me-2"></i><?= htmlspecialchars($roomType['bed_type']) ?></p>
                                <p><i class="fas fa-user-friends me-2"></i>Max <?= htmlspecialchars($roomType['max_occupancy']) ?> guests</p>
                            </div>
                        </div>

                        <div class="price-breakdown">
                            <div class="price-item">
                                <span>Room Rate</span>
                                <span>LKR <?= number_format($basePrice, 2) ?></span>
                            </div>
                            <div class="price-item">
                                <span>Tax (10%)</span>
                                <span>LKR <?= number_format($tax, 2) ?></span>
                            </div>
                            <div class="price-item total-price">
                                <span>Total</span>
                                <span>LKR <?= number_format($totalPrice, 2) ?></span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary checkout-btn" form="bookingForm">
                            Confirm Booking
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'components/footer.php'; ?>

    <!-- Bootstrap & jQuery JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> <!-- Display Messages -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get today's date in YYYY-MM-DD format
            const today = new Date().toISOString().split('T')[0];

            // Set min date for check-in and check-out
            document.querySelector('input[name="check_in"]').min = today;
            document.querySelector('input[name="check_out"]').min = today;

            // Update check-out min date when check-in is selected
            document.querySelector('input[name="check_in"]').addEventListener('change', function() {
                document.querySelector('input[name="check_out"]').min = this.value;
            });

            // Form validation
            document.getElementById('bookingForm').addEventListener('submit', function(e) {
                const checkIn = new Date(document.querySelector('input[name="check_in"]').value);
                const checkOut = new Date(document.querySelector('input[name="check_out"]').value);

                if (checkIn >= checkOut) {
                    e.preventDefault();
                    alert('Check-out date must be after check-in date');
                }
            });
        });
    </script>
</body>

</html>