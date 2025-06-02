<?php
include_once 'includes/header.php';
include_once 'includes/sidebar.php';

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
        <div class="header-actions">
            <button class="btn btn-outline-primary" id="exportPayments">
                <i class="fas fa-download"></i> Export Report
            </button>
        </div>
    </div>

    <div class="payment-filters mb-3">
        <div class="row g-3">
            <div class="col-md-3">
                <select class="form-select" id="paymentStatusFilter">
                    <option value="">All Payment Status</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                    <option value="failed">Failed</option>
                    <option value="expired">Expired</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="paymentMethodFilter">
                    <option value="">All Payment Methods</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="cash">Cash</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" class="form-control" id="paymentDateFilter">
            </div>
        </div>
    </div>

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
                                    <button type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        onclick="viewPayment(<?= $payment['payment_id'] ?>)"
                                        title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <?php if ($payment['status'] === 'pending'): ?>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-success"
                                            onclick="verifyPayment(<?= $payment['payment_id'] ?>, 'completed')"
                                            title="Approve Payment">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="verifyPayment(<?= $payment['payment_id'] ?>, 'failed')"
                                            title="Reject Payment">
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
                        <label for="verificationNotes" class="form-label">Notes</label>
                        <textarea class="form-control" id="verificationNotes" rows="3"
                            placeholder="Add any notes about the verification"></textarea>
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
    // Function to filter payments
    function filterPayments() {
        const status = document.getElementById('paymentStatusFilter').value;
        const method = document.getElementById('paymentMethodFilter').value;
        const date = document.getElementById('paymentDateFilter').value;

        // Implement filtering logic
        const rows = document.querySelectorAll('.payments-list tbody tr');
        rows.forEach(row => {
            // Add filtering logic here
        });
    }

    // Function to view payment details
    async function viewPayment(paymentId) {
        try {
            const response = await fetch(`handlers/get_payment_details.php?id=${paymentId}`);
            const data = await response.json();

            if (data.status === 'success') {
                document.getElementById('paymentDetailsContent').innerHTML = data.html;
                new bootstrap.Modal(document.getElementById('paymentDetailsModal')).show();
            } else {
                alert(data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to load payment details');
        }
    }

    // Function to verify payment
    function verifyPayment(paymentId, status) {
        document.getElementById('verifyPaymentId').value = paymentId;
        document.getElementById('verifyPaymentStatus').value = status;
        document.getElementById('verificationNotes').value = '';

        const modal = new bootstrap.Modal(document.getElementById('verifyPaymentModal'));
        modal.show();
    }

    // Function to submit verification
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

    // Add event listeners for filters
    document.getElementById('paymentStatusFilter').addEventListener('change', filterPayments);
    document.getElementById('paymentMethodFilter').addEventListener('change', filterPayments);
    document.getElementById('paymentDateFilter').addEventListener('change', filterPayments);
</script>

<?php include_once 'includes/footer.php'; ?>