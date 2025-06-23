<?php
include_once 'includes/header.php';
include_once 'includes/sidebar.php';

// Handle view payment details
$paymentDetails = null;
if (isset($_GET['action']) && $_GET['action'] === 'view' && isset($_GET['id'])) {
    $viewStmt = $conn->prepare("
        SELECT p.*, 
            b.booking_reference, b.guest_name, b.guest_email, b.guest_phone,
            b.check_in_date, b.check_out_date,
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
    $viewStmt->execute([$_GET['id']]);
    $payment = $viewStmt->fetch(PDO::FETCH_ASSOC);

    if ($payment) {
        ob_start();
?>
        <div class="payment-details">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="mb-3">Payment Information</h6>
                    <p><strong>Payment ID:</strong> <?= htmlspecialchars($payment['payment_id']) ?></p>
                    <p><strong>Amount:</strong> LKR <?= number_format($payment['amount'], 2) ?></p>
                    <p><strong>Method:</strong> <?= ucfirst(str_replace('_', ' ', $payment['payment_method'])) ?></p>
                    <p><strong>Status:</strong> <?= ucfirst($payment['status']) ?></p>
                    <p><strong>Date:</strong> <?= date('M d, Y H:i', strtotime($payment['payment_date'])) ?></p>
                </div>
                <div class="col-md-6">
                    <h6 class="mb-3">Booking Information</h6>
                    <p><strong>Reference:</strong> <?= htmlspecialchars($payment['booking_reference']) ?></p>
                    <p><strong>Hotel:</strong> <?= htmlspecialchars($payment['hotel_name']) ?></p>
                    <p><strong>Room Type:</strong> <?= htmlspecialchars($payment['room_type']) ?></p>
                    <p><strong>Room Number:</strong> <?= htmlspecialchars($payment['room_number']) ?></p>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="mb-3">Guest Information</h6>
                    <p><strong>Name:</strong> <?= htmlspecialchars($payment['guest_name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($payment['guest_email']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($payment['guest_phone']) ?></p>
                </div>
                <?php if ($payment['payment_method'] === 'bank_transfer'): ?>
                    <div class="col-md-6">
                        <h6 class="mb-3">Bank Transfer Details</h6>
                        <p><strong>Bank Name:</strong> <?= htmlspecialchars($payment['bank_name']) ?></p>
                        <p><strong>Reference:</strong> <?= htmlspecialchars($payment['bank_reference']) ?></p>
                        <p><strong>Transfer Date:</strong> <?= date('M d, Y', strtotime($payment['transfer_date'])) ?></p>
                        <?php if ($payment['bank_slip']): ?>
                            <p><strong>Bank Slip:</strong> <a href="../<?= htmlspecialchars($payment['bank_slip']) ?>" target="_blank">View Slip</a></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($payment['verified_by']): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="mb-3">Verification Information</h6>
                        <p><strong>Verified By:</strong> <?= htmlspecialchars($payment['verified_by_name'] . ' ' . $payment['verified_by_lastname']) ?></p>
                        <p><strong>Verified At:</strong> <?= date('M d, Y H:i', strtotime($payment['verified_at'])) ?></p>
                        <?php if ($payment['notes']): ?>
                            <p><strong>Notes:</strong> <?= nl2br(htmlspecialchars($payment['notes'])) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
<?php
        $paymentDetails = ob_get_clean();
    }
}

// Fetch payments with booking and user details
$sql = "SELECT 
            p.*, 
            b.booking_reference,
            b.guest_name,
            h.hotel_name,
            u.first_name as verified_by_name,
            u.last_name as verified_by_lastname
        FROM payments p
        JOIN bookings b ON p.booking_id = b.booking_id
        JOIN hotels h ON b.hotel_id = h.hotel_id
        LEFT JOIN users u ON p.verified_by = u.user_id
        ORDER BY p.payment_date DESC";

$stmt = $conn->query($sql);
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Main Content -->
<div class="admin-main">
    <div class="content-header">
        <h2>Payment Verification</h2>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="payments-list">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Guest</th>
                        <th>Hotel</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td><?= htmlspecialchars($payment['booking_reference']) ?></td>
                            <td><?= htmlspecialchars($payment['guest_name']) ?></td>
                            <td><?= htmlspecialchars($payment['hotel_name']) ?></td>
                            <td>LKR <?= number_format($payment['amount'], 2) ?></td>
                            <td>
                                <span class="badge bg-<?= $payment['payment_method'] === 'bank_transfer' ? 'info' : 'warning' ?>">
                                    <?= ucfirst(str_replace('_', ' ', $payment['payment_method'])) ?>
                                </span>
                            </td>
                            <td><?= date('M d, Y H:i', strtotime($payment['payment_date'])) ?></td>
                            <td>
                                <?php
                                $statusClass = match ($payment['status']) {
                                    'completed' => 'success',
                                    'pending' => 'warning',
                                    'failed' => 'danger',
                                    'expired' => 'secondary',
                                    default => 'info'
                                };
                                ?>
                                <span class="badge bg-<?= $statusClass ?>">
                                    <?= ucfirst($payment['status']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="payment-verification.php?action=view&id=<?= $payment['payment_id'] ?>"
                                        class="btn btn-sm btn-outline-primary"
                                        title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <?php if ($payment['status'] === 'pending'): ?>
                                        <?php if ($payment['payment_method'] === 'bank_transfer'): ?>
                                            <a href="handlers/verify_payment.php?payment_id=<?= $payment['payment_id'] ?>&status=completed&notes=<?= urlencode('Bank transfer verified by admin') ?>"
                                                class="btn btn-sm btn-outline-success"
                                                onclick="return confirm('Are you sure you want to verify this payment?')"
                                                title="Verify Bank Transfer">
                                                <i class="fas fa-check"></i> Verify Transfer
                                            </a>
                                            <a href="handlers/verify_payment.php?payment_id=<?= $payment['payment_id'] ?>&status=failed&notes=<?= urlencode('Bank transfer rejected by admin') ?>"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Are you sure you want to reject this payment?')"
                                                title="Reject Transfer">
                                                <i class="fas fa-times"></i> Reject
                                            </a>
                                        <?php else: ?>
                                            <a href="handlers/verify_payment.php?payment_id=<?= $payment['payment_id'] ?>&status=completed&notes=<?= urlencode('Cash payment verified by admin') ?>"
                                                class="btn btn-sm btn-outline-success"
                                                onclick="return confirm('Are you sure you want to mark this payment as received?')"
                                                title="Mark Cash Payment as Received">
                                                <i class="fas fa-money-bill"></i> Mark as Paid
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Payment Details -->
<div class="modal fade" id="paymentDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="paymentDetailsContent">
                <?php if (isset($paymentDetails)): ?>
                    <?= $paymentDetails ?>
                <?php else: ?>
                    <p>Select a payment to view details.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>