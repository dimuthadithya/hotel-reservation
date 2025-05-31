<?php include_once 'includes/header.php'; ?>
<?php include_once 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="admin-main">
    <div class="content-header">
        <h2>Review Management</h2>
        <div class="header-actions">
            <select class="form-select" id="reviewFilter">
                <option value="all">All Reviews</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
    </div>
    <div class="reviews-list">
        <!-- Review items will be loaded dynamically -->
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>