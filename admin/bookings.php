<?php include_once 'includes/header.php'; ?>
<?php include_once 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="admin-main">
    <div class="content-header">
        <h2>Booking Management</h2>
        <div class="header-actions">
            <button class="btn btn-outline-primary" id="exportBookings">
                <i class="fas fa-download"></i> Export Report
            </button>
        </div>
    </div>
    <div class="bookings-list">
        <!-- Booking items will be loaded dynamically -->
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>