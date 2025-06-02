<?php
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!-- Sidebar -->
<div class="admin-sidebar">
    <div class="sidebar-user">
        <img
            src="../assets/img/avatar1.jpg"
            alt="Admin"
            class="admin-avatar" />
        <div class="admin-info">
            <h6 class="admin-name">Admin User</h6>
            <span class="admin-role">Super Admin</span>
        </div>
    </div>
    <ul class="sidebar-nav">
        <li class="nav-item <?php echo $currentPage === 'index' ? 'active' : ''; ?>">
            <a href="index.php">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        <li class="nav-item <?php echo $currentPage === 'hotels' ? 'active' : ''; ?>">
            <a href="hotels.php">
                <i class="fas fa-hotel"></i> Hotels
            </a>
        </li>
        </li>
        <li class="nav-item <?php echo $currentPage === 'rooms' ? 'active' : ''; ?>">
            <a href="rooms.php">
                <i class="fas fa-bed"></i> Rooms
            </a>
        </li>
        <li class="nav-item <?php echo $currentPage === 'amenities' ? 'active' : ''; ?>">
            <a href="amenities.php">
                <i class="fas fa-concierge-bell"></i> Amenities
            </a>
        </li>
        <li class="nav-item <?php echo $currentPage === 'bookings' ? 'active' : ''; ?>">
            <a href="bookings.php">
                <i class="fas fa-calendar-check"></i> Bookings
            </a>
        </li>
        <li class="nav-item <?php echo $currentPage === 'payments' ? 'active' : ''; ?>">
            <a href="payments.php">
                <i class="fas fa-credit-card"></i> Payments
            </a>
        </li>
        <li class="nav-item <?php echo $currentPage === 'payment-verification' ? 'active' : ''; ?>">
            <a href="payment-verification.php">
                <i class="fas fa-check-circle"></i> Payment Verification
            </a>
        </li>
        <li class="nav-item <?php echo $currentPage === 'users' ? 'active' : ''; ?>">
            <a href="users.php">
                <i class="fas fa-users"></i> Users
            </a>
        </li>
        <li class="nav-item">
            <a href="../handlers/logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</div>