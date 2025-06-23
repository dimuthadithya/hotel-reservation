<?php
include_once 'includes/header.php';
include_once 'includes/sidebar.php';

// Fetch all payments with related information
$sql = "SELECT 
            p.*, 
            b.booking_reference,
            b.guest_name,
            b.check_in_date,
            b.check_out_date,
            h.hotel_name,
            CONCAT(u.first_name, ' ', u.last_name) as verified_by_name
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
        <h2>Payment Management</h2>
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
    <?php endif; ?> <div class="payments-list">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Booking Ref</th>
                        <th>Guest</th>
                        <th>Hotel</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $payment): ?>
                        <tr class="payment-row"
                            data-status="<?= $payment['status'] ?>"
                            data-method="<?= $payment['payment_method'] ?>"
                            data-reference="<?= $payment['booking_reference'] ?>">
                            <td><?= htmlspecialchars($payment['booking_reference']) ?></td>
                            <td><?= htmlspecialchars($payment['guest_name']) ?></td>
                            <td><?= htmlspecialchars($payment['hotel_name']) ?></td>
                            <td>LKR <?= number_format($payment['amount'], 2) ?></td>
                            <td>
                                <span class="badge bg-<?= $payment['payment_method'] === 'bank_transfer' ? 'info' : 'warning' ?>">
                                    <?= ucfirst(str_replace('_', ' ', $payment['payment_method'])) ?>
                                </span>
                            </td>
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
                            <td><?= date('M d, Y H:i', strtotime($payment['payment_date'])) ?></td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="viewPaymentDetails(<?= $payment['payment_id'] ?>)"
                                        title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <?php if ($payment['status'] === 'pending'): ?>
                                        <button type="button" class="btn btn-sm btn-outline-success"
                                            onclick="verifyPayment(<?= $payment['payment_id'] ?>, 'completed')"
                                            title="Verify Payment">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="verifyPayment(<?= $payment['payment_id'] ?>, 'failed')"
                                            title="Mark as Failed">
                                            <i class="fas fa-times"></i>
                                        </button>
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

<!-- Payment Details Modal -->
<div class="modal fade" id="paymentDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="paymentDetailsContent">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>
</div>

<!-- Verify Payment Modal -->
<div class="modal fade" id="verifyPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Verify Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="verifyPaymentForm">
                    <input type="hidden" id="verifyPaymentId">
                    <input type="hidden" id="verifyPaymentStatus">
                    <div class="mb-3">
                        <label for="verificationNotes" class="form-label">Verification Notes</label>
                        <textarea class="form-control" id="verificationNotes" rows="3"
                            placeholder="Add any notes about the verification (optional)"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitVerification()">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize any needed functionality
    });

    async function viewPaymentDetails(paymentId) {
        try {
            const response = await fetch(`handlers/get_payment_details.php?id=${paymentId}`);
            const data = await response.json();

            if (data.status === 'success') {
                document.getElementById('paymentDetailsContent').innerHTML = data.html;
                new bootstrap.Modal(document.getElementById('paymentDetailsModal')).show();
            } else {
                alert('Failed to load payment details: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to load payment details');
        }
    }

    function verifyPayment(paymentId, status) {
        document.getElementById('verifyPaymentId').value = paymentId;
        document.getElementById('verifyPaymentStatus').value = status;
        document.getElementById('verificationNotes').value = '';

        const modal = new bootstrap.Modal(document.getElementById('verifyPaymentModal'));
        modal.show();
    }

    async function submitVerification() {
        const paymentId = document.getElementById('verifyPaymentId').value;
        const status = document.getElementById('verifyPaymentStatus').value;
        const notes = document.getElementById('verificationNotes').value;

        try {
            const response = await fetch('handlers/verify_payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `payment_id=${paymentId}&status=${status}&notes=${encodeURIComponent(notes)}`
            });

            const data = await response.json();

            if (data.status === 'success') {
                location.reload(); // Reload to show updated status
            } else {
                alert(data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to verify payment');
        }
    }
</script>

<?php include_once 'includes/footer.php'; ?>