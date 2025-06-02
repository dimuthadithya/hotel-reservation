<?php
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!-- Sidebar -->
<div class="admin-sidebar">
    <?php
    // Get user details
    try {
        $user_sql = "SELECT first_name, last_name, role, profile_image FROM users WHERE user_id = ?";
        $user_stmt = $conn->prepare($user_sql);
        $user_stmt->execute([$_SESSION['user_id']]);
        $user = $user_stmt->fetch(PDO::FETCH_ASSOC);

        $profile_image = $user['profile_image'] ?? '../assets/img/avatar1.jpg';
        $full_name = $user['first_name'] . ' ' . $user['last_name'];
    } catch (PDOException $e) {
        // If there's an error, use default values
        $profile_image = '../assets/img/avatar1.jpg';
        $full_name = 'Admin User';
        $user = ['role' => 'admin'];
    }
    ?>
    <div class="sidebar-user">
        <div class="admin-info">
            <h6 class="admin-name"><?= htmlspecialchars($full_name) ?></h6>
            <span class="admin-role"><?= ucfirst(htmlspecialchars($user['role'])) ?></span>
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