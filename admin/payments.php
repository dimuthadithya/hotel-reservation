<?php include_once 'includes/header.php'; ?>
<?php include_once 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="admin-main">
    <div class="content-header">
        <h2>Payment Management</h2>
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
                    <option value="refunded">Refunded</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="paymentMethodFilter">
                    <option value="">All Payment Methods</option>
                    <option value="credit_card">Credit Card</option>
                    <option value="debit_card">Debit Card</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="cash">Cash</option>
                </select>
            </div>
            <div class="col-md-3">
                <input
                    type="date"
                    class="form-control"
                    id="paymentDateFilter" />
            </div>
        </div>
    </div>
    <div class="payments-list">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Payment ID</th>
                        <th>Booking Ref</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Payment items will be loaded dynamically -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>