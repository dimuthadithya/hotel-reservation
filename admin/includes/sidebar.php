<?php
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!-- Sidebar -->
<div class="admin-sidebar">
    <div class="sidebar-user">
        <?php if (isset($_SESSION['profile_image']) && $_SESSION['profile_image']): ?>
            <img src="../uploads/img/<?= htmlspecialchars($_SESSION['profile_image']) ?>"
                alt="<?= htmlspecialchars($_SESSION['first_name']) ?>"
                class="admin-avatar" />
        <?php else: ?>
            <div class="admin-avatar-placeholder">
                <?= strtoupper(substr($_SESSION['first_name'] ?? 'A', 0, 1)) ?>
            </div>
        <?php endif; ?>
        <div class="admin-info">
            <h6 class="admin-name"><?= htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']) ?></h6>
            <span class="admin-role"><?= ucfirst($_SESSION['role']) ?></span>
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
        </li>        <li class="nav-item <?php echo $currentPage === 'users' ? 'active' : ''; ?>">
            <a href="users.php">
                <i class="fas fa-users"></i> Users
            </a>
        </li>
        <li class="nav-divider"></li>
        <li class="nav-item">
            <a href="../handlers/logout.php" onclick="return confirm('Are you sure you want to log out?');">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</div>

<style>
.admin-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    object-fit: cover;
}

.admin-avatar-placeholder {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background-color: #435ebe;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    font-weight: bold;
}

.admin-info {
    padding-left: 10px;
}

.nav-divider {
    height: 1px;
    background-color: rgba(255, 255, 255, 0.1);
    margin: 15px 0;
}

.sidebar-nav li:last-child {
    margin-top: auto;
}

.sidebar-nav li:last-child a {
    color: #dc3545;
}

.sidebar-nav li:last-child a:hover {
    background-color: rgba(220, 53, 69, 0.1);
}