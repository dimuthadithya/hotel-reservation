<?php
// Determine the current page
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            Pearl Stay
            <span class="text-muted small">Sri Lankan Hospitality</span>
        </a>
        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage === 'index' ? 'active' : ''; ?>" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage === 'search-listings' ? 'active' : ''; ?>" href="search-listings.php">Places to Stay</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage === 'about' ? 'active' : ''; ?>" href="about.php">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage === 'contact' ? 'active' : ''; ?>" href="contact.php">Contact</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage === 'login' ? 'active' : ''; ?>" href="login.php">Login</a>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>" href="dashboard.php">My Account</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="handlers/logout.php">Logout</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a
                        class="btn btn-primary btn-sm ms-2"
                        href="search-listings.php">
                        <i class="fas fa-search me-1"></i>Find Stays
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>