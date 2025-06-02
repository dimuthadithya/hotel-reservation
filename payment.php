<?php
session_start();
include_once 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Please login to make payment';
    header('Location: login.php');
    exit;
}

// Check if booking_id is provided
if (!isset($_GET['booking_id'])) {
    $_SESSION['error'] = 'Invalid booking';
    header('Location: dashboard.php');
    exit;
}

$booking_id = filter_var($_GET['booking_id'], FILTER_SANITIZE_NUMBER_INT);

// Get booking details
$sql = "SELECT b.*, h.hotel_name, rt.type_name as room_type, r.room_number
        FROM bookings b
        JOIN hotels h ON b.hotel_id = h.hotel_id
        JOIN room_types rt ON b.room_type_id = rt.room_type_id
        JOIN room_bookings rb ON b.booking_id = rb.booking_id
        JOIN rooms r ON rb.room_id = r.room_id
        WHERE b.booking_id = ? AND b.user_id = ? 
        AND b.booking_status = 'confirmed' 
        AND b.payment_status = 'pending'
        AND b.payment_deadline > NOW()";

$stmt = $conn->prepare($sql);
$stmt->execute([$booking_id, $_SESSION['user_id']]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    $_SESSION['error'] = 'Invalid booking or payment deadline has passed';
    header('Location: dashboard.php');
    exit;
}

// Calculate time remaining
$deadline = new DateTime($booking['payment_deadline']);
$now = new DateTime();
$timeRemaining = $deadline->diff($now);
$hoursRemaining = ($timeRemaining->invert) ? ($timeRemaining->d * 24 + $timeRemaining->h) : 0;

include_once 'components/nav.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make Payment - Pearl Stay</title>
    <!-- Existing CSS -->
    <link rel="stylesheet" href="./assets/css/styles.css">
    <link rel="stylesheet" href="./assets/css/booking.css">
    <link href="./assets/css/nav.css" rel="stylesheet" />
    <link href="./assets/css/footer.css" rel="stylesheet" />

    <!-- Bootstrap 5 CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet" />
    <style>
        .payment-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .booking-summary {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        .time-remaining {
            color: #dc3545;
            font-weight: bold;
        }

        .payment-methods {
            margin: 2rem 0;
        }

        .payment-method-option {
            border: 2px solid #dee2e6;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .payment-method-option:hover {
            border-color: #0d6efd;
        }

        .payment-method-option.selected {
            border-color: #0d6efd;
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>
    <div class="payment-container">
        <h2 class="mb-4">Complete Your Payment</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error'];
                unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success'];
                unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div class="alert alert-warning">
            <i class="fas fa-clock me-2"></i>
            Time remaining to complete payment: <span class="time-remaining"><?= $hoursRemaining ?> hours</span>
            <br>
            <small>Booking will be automatically cancelled if payment is not received within the time limit.</small>
        </div>

        <div class="booking-summary">
            <h4 class="mb-3">Booking Summary</h4>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Hotel:</strong> <?= htmlspecialchars($booking['hotel_name']) ?></p>
                    <p><strong>Room Type:</strong> <?= htmlspecialchars($booking['room_type']) ?></p>
                    <p><strong>Room Number:</strong> <?= htmlspecialchars($booking['room_number']) ?></p>
                    <p><strong>Check-in:</strong> <?= date('M d, Y', strtotime($booking['check_in_date'])) ?></p>
                    <p><strong>Check-out:</strong> <?= date('M d, Y', strtotime($booking['check_out_date'])) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Booking Reference:</strong> <?= htmlspecialchars($booking['booking_reference']) ?></p>
                    <p><strong>Total Amount:</strong> LKR <?= number_format($booking['total_amount'], 2) ?></p>
                    <p><strong>Guest Name:</strong> <?= htmlspecialchars($booking['guest_name']) ?></p>
                    <p><strong>Guest Email:</strong> <?= htmlspecialchars($booking['guest_email']) ?></p>
                </div>
            </div>
        </div>

        <form action="handlers/process_payment.php" method="POST" enctype="multipart/form-data" id="paymentForm">
            <input type="hidden" name="booking_id" value="<?= $booking_id ?>">

            <div class="payment-methods mb-4">
                <h4 class="mb-3">Select Payment Method</h4>

                <div class="payment-method-option">
                    <div class="form-check">
                        <input class="form-check-input payment-method-radio" type="radio" name="payment_method" id="bankTransfer" value="bank_transfer" required>
                        <label class="form-check-label" for="bankTransfer">
                            <strong>Bank Transfer</strong>
                            <p class="mb-0 text-muted">Transfer the amount to our bank account</p>
                        </label>
                    </div>
                </div>

                <div class="payment-method-option">
                    <div class="form-check">
                        <input class="form-check-input payment-method-radio" type="radio" name="payment_method" id="cash" value="cash" required>
                        <label class="form-check-label" for="cash">
                            <strong>Cash Payment</strong>
                            <p class="mb-0 text-muted">Pay in cash at our office</p>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Bank Transfer Details (initially hidden) -->
            <div id="bankTransferDetails" style="display: none;">
                <div class="alert alert-info">
                    <h5 class="alert-heading">Bank Account Details</h5>
                    <p class="mb-0">Please transfer the amount to the following account:</p>
                    <hr>
                    <p class="mb-1"><strong>Bank Name:</strong> Commercial Bank</p>
                    <p class="mb-1"><strong>Account Name:</strong> Pearl Stay Hotels</p>
                    <p class="mb-1"><strong>Account Number:</strong> 1234567890</p>
                    <p class="mb-1"><strong>Branch:</strong> Main Branch</p>
                    <p class="mb-0"><strong>Amount to Transfer:</strong> LKR <?= number_format($booking['total_amount'], 2) ?></p>
                </div>

                <div class="mb-3">
                    <label for="bankName" class="form-label">Bank Used for Transfer *</label>
                    <input type="text" class="form-control" id="bankName" name="bank_name" required>
                </div>

                <div class="mb-3">
                    <label for="bankReference" class="form-label">Bank Reference Number *</label>
                    <input type="text" class="form-control" id="bankReference" name="bank_reference" required>
                </div>

                <div class="mb-3">
                    <label for="transferDate" class="form-label">Date of Transfer *</label>
                    <input type="date" class="form-control" id="transferDate" name="transfer_date" required>
                </div>

                <div class="mb-3">
                    <label for="bankSlip" class="form-label">Upload Bank Slip (PDF, JPG, PNG) *</label>
                    <input type="file" class="form-control" id="bankSlip" name="bank_slip" accept=".pdf,.jpg,.jpeg,.png" required>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Additional Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>
            </div>

            <!-- Cash Payment Details (initially hidden) -->
            <div id="cashPaymentDetails" style="display: none;">
                <div class="alert alert-info">
                    <h5 class="alert-heading">Cash Payment Instructions</h5>
                    <p class="mb-0">Please visit our office to make the cash payment:</p>
                    <hr>
                    <p class="mb-1"><strong>Office Address:</strong> 123 Main Street, Colombo</p>
                    <p class="mb-1"><strong>Office Hours:</strong> Monday to Friday, 9:00 AM - 5:00 PM</p>
                    <p class="mb-1"><strong>Contact:</strong> +94 11 234 5678</p>
                    <p class="mb-0"><strong>Amount to Pay:</strong> LKR <?= number_format($booking['total_amount'], 2) ?></p>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary" id="submitBtn">Confirm Payment Method</button>
                <a href="dashboard.php" class="btn btn-outline-secondary">Back to Dashboard</a>
            </div>
        </form>
    </div>

    <script>
        // Show/hide payment details based on selected method
        document.querySelectorAll('.payment-method-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('bankTransferDetails').style.display =
                    this.value === 'bank_transfer' ? 'block' : 'none';
                document.getElementById('cashPaymentDetails').style.display =
                    this.value === 'cash' ? 'block' : 'none';

                // Update button text
                document.getElementById('submitBtn').textContent =
                    this.value === 'bank_transfer' ? 'Submit Payment Details' : 'Confirm Cash Payment';

                // Toggle required attributes
                const bankFields = document.querySelectorAll('#bankTransferDetails input[required]');
                bankFields.forEach(field => {
                    field.required = this.value === 'bank_transfer';
                });
            });
        });
    </script>

    <?php include_once 'components/footer.php'; ?>
</body>

</html>