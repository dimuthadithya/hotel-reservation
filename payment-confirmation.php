<?php
session_start();
include_once 'config/db.php';

// Check if payment was just made (via session)
if (!isset($_SESSION['payment_id']) || !isset($_SESSION['payment_method'])) {
    header('Location: dashboard.php');
    exit;
}

$payment_id = $_SESSION['payment_id'];
$payment_method = $_SESSION['payment_method'];

// Clear the session variables
unset($_SESSION['payment_id']);
unset($_SESSION['payment_method']);

// Get payment details
$sql = "SELECT p.*, b.booking_reference, b.guest_name, b.total_amount, h.hotel_name 
        FROM payments p
        JOIN bookings b ON p.booking_id = b.booking_id
        JOIN hotels h ON b.hotel_id = h.hotel_id
        WHERE p.payment_id = ?";

$stmt = $conn->prepare($sql);
$stmt->execute([$payment_id]);
$payment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$payment) {
    header('Location: dashboard.php');
    exit;
}

include_once 'components/nav.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation - Pearl Stay</title>
    <link rel="stylesheet" href="./assets/css/styles.css">
    <link href="./assets/css/nav.css" rel="stylesheet" />
    <link href="./assets/css/footer.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        .confirmation-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .payment-details {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin: 1.5rem 0;
        }

        .next-steps {
            background: #e7f5ff;
            padding: 1.5rem;
            border-radius: 8px;
            margin: 1.5rem 0;
        }

        .next-steps ol {
            margin: 0;
            padding-left: 1.2rem;
        }

        .success-icon {
            color: #198754;
            font-size: 4rem;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <div class="confirmation-container">
        <div class="text-center">
            <i class="fas fa-check-circle success-icon"></i>
            <h2 class="mb-4">Payment Details Submitted!</h2>
            <p class="text-muted">Thank you for choosing Pearl Stay Hotels</p>
        </div>

        <div class="payment-details">
            <h4>Payment Information</h4>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Booking Reference:</strong> <?= htmlspecialchars($payment['booking_reference']) ?></p>
                    <p><strong>Hotel:</strong> <?= htmlspecialchars($payment['hotel_name']) ?></p>
                    <p><strong>Guest Name:</strong> <?= htmlspecialchars($payment['guest_name']) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Amount:</strong> LKR <?= number_format($payment['amount'], 2) ?></p>
                    <p><strong>Payment Method:</strong> <?= ucwords(str_replace('_', ' ', $payment['payment_method'])) ?></p>
                    <p><strong>Status:</strong> <span class="badge bg-warning">Pending Verification</span></p>
                </div>
            </div>
        </div>

        <div class="next-steps">
            <h4>Next Steps</h4>
            <?php if ($payment['payment_method'] === 'bank_transfer'): ?>
                <ol>
                    <li>Your bank transfer details have been received</li>
                    <li>Our team will verify the payment within 12 hours</li>
                    <li>You will receive a confirmation email once verified</li>
                    <li>Check your booking status in the dashboard</li>
                </ol>
            <?php else: ?>
                <ol>
                    <li>Visit our office with your booking reference</li>
                    <li>Make the cash payment at our counter</li>
                    <li>Receive your payment receipt</li>
                    <li>Your booking will be updated immediately</li>
                </ol>
            <?php endif; ?>
        </div>

        <div class="d-grid gap-2">
            <a href="dashboard.php#bookings" class="btn btn-primary">View My Bookings</a>
            <a href="index.php" class="btn btn-outline-secondary">Return to Home</a>
        </div>
    </div>

    <?php include_once 'components/footer.php'; ?>
</body>

</html>