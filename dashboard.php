<?php
require_once 'config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = :id");
$stmt->bindParam(':id', $userId);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch user's bookings
$bookingStmt = $conn->prepare("
    SELECT b.*, h.hotel_name, r.room_number, rt.type_name as room_type,
           b.total_amount as total_price, b.booking_status as status, 
           b.check_in_date, b.check_out_date,
           b.adults, b.children, b.booking_reference
    FROM bookings b
    JOIN room_bookings rb ON b.booking_id = rb.booking_id
    JOIN rooms r ON rb.room_id = r.room_id
    JOIN hotels h ON b.hotel_id = h.hotel_id
    JOIN room_types rt ON b.room_type_id = rt.room_type_id
    WHERE b.user_id = :user_id
    ORDER BY b.check_in_date DESC
");
$bookingStmt->bindParam(':user_id', $userId);
$bookingStmt->execute();
$bookings = $bookingStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Account - Pearl Stay</title>
  <!-- Favicon -->
  <link
    rel="apple-touch-icon"
    sizes="180x180"
    href="assets/favicon_io/apple-touch-icon.png" />
  <link
    rel="icon"
    type="image/png"
    sizes="32x32"
    href="assets/favicon_io/favicon-32x32.png" />
  <link
    rel="icon"
    type="image/png"
    sizes="16x16"
    href="assets/favicon_io/favicon-16x16.png" />
  <link rel="manifest" href="assets/favicon_io/site.webmanifest" />
  <!-- Bootstrap CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet" />
  <!-- Font Awesome -->
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    rel="stylesheet" />
  <!-- Custom CSS -->
  <link href="assets/css/styles.css" rel="stylesheet" />
  <link href="assets/css/nav.css" rel="stylesheet" />
  <link href="assets/css/footer.css" rel="stylesheet" />
  <link href="assets/css/dashboard.css" rel="stylesheet" />
</head>

<body>
  <?php include 'components/nav.php'; ?>

  <!-- Dashboard Content -->
  <div class="dashboard-container">
    <div class="container">
      <!-- Profile Card -->
      <div class="profile-card">
        <div class="profile-header">
          <div class="position-relative">
            <img
              src="assets/img/avatar1.jpg"
              alt="Profile"
              class="profile-avatar"
              id="profileImage" />
            <label
              for="profileImageUpload"
              class="position-absolute bottom-0 end-0 btn btn-sm btn-light rounded-circle">
              <i class="fas fa-camera"></i>
              <input
                type="file"
                id="profileImageUpload"
                class="d-none"
                accept="image/*" />
            </label>
          </div>
          <div class="profile-info">
            <h2><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></h2>
            <p class="text-muted mb-2"><?php echo $user['email']; ?></p>
            <div class="profile-status">
              <i class="fas fa-check-circle"></i>
              <span>Verified Member since <?php echo $user['created_at']; ?></span>
            </div>
          </div>
        </div>
      </div>

      <!-- Navigation Tabs -->
      <div class="dashboard-tabs">
        <ul class="nav nav-tabs" role="tablist">
          <li class="nav-item">
            <a
              class="nav-link active"
              data-bs-toggle="tab"
              href="#profile"
              role="tab">
              <i class="fas fa-user me-2"></i>Profile
            </a>
          </li>
          <li class="nav-item">
            <a
              class="nav-link"
              data-bs-toggle="tab"
              href="#bookings"
              role="tab">
              <i class="fas fa-calendar-alt me-2"></i>My Bookings
            </a>
          </li>
          <li class="nav-item">
            <a
              class="nav-link"
              data-bs-toggle="tab"
              href="#settings"
              role="tab">
              <i class="fas fa-cog me-2"></i>Settings
            </a>
          </li>
        </ul>
      </div>

      <!-- Tab Content -->
      <div class="tab-content">
        <!-- Profile Tab -->
        <div class="tab-pane fade show active" id="profile" role="tabpanel">
          <div class="content-card">
            <h3 class="mb-4">Personal Information</h3>
            <form id="settingsForm" action="./handlers/update_profile.php" method="POST">
              <div class="row g-3">
                <div class="col-md-6">
                  <div class="form-floating">
                    <input
                      type="text"
                      class="form-control"
                      name="first_name"
                      id="firstName"
                      value="<?php echo $user['first_name']; ?>" />
                    <label for="firstName">First Name</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-floating">
                    <input
                      type="text"
                      class="form-control"
                      id="lastName"
                      name="last_name"
                      value="<?php echo $user['last_name']; ?>" />
                    <label for="lastName">Last Name</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-floating">
                    <input
                      type="email"
                      class="form-control"
                      id="email"
                      name="email"
                      value="<?php echo $user['email']; ?>" />
                    <label for="email">Email Address</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-floating">
                    <input
                      type="tel"
                      class="form-control"
                      id="phone"
                      name="phone"
                      value="<?php echo $user['phone']; ?>" />
                    <label for="phone">Phone Number</label>
                  </div>
                </div>
              </div>
              <button type="submit" name="update_profile" class="btn btn-primary mt-4">
                Save Changes
              </button>
            </form>
          </div>
        </div> <!-- Bookings Tab -->
        <div class="tab-pane fade" id="bookings" role="tabpanel">
          <div class="content-card">
            <h3 class="mb-4">My Bookings</h3>
            <?php if (empty($bookings)): ?>
              <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>You don't have any bookings yet.
              </div>
            <?php else: ?>
              <?php foreach ($bookings as $booking):
                $checkInDate = new DateTime($booking['check_in_date']);
                $checkOutDate = new DateTime($booking['check_out_date']);
                $today = new DateTime();

                // Determine booking status and corresponding CSS class
                $statusClass = '';
                $statusText = '';

                if ($booking['booking_status'] === 'cancelled') {
                  $statusClass = 'danger';
                  $statusText = 'Cancelled';
                } elseif ($booking['booking_status'] === 'checked_out' || $today > $checkOutDate) {
                  $statusClass = 'success';
                  $statusText = 'Completed';
                } elseif ($booking['booking_status'] === 'checked_in' || ($today >= $checkInDate && $today <= $checkOutDate)) {
                  $statusClass = 'primary';
                  $statusText = 'Active';
                } else {
                  $statusClass = 'info';
                  $statusText = 'Upcoming';
                }

                // Calculate time remaining for payment if booking is confirmed and payment is pending
                $paymentDeadline = null;
                $hoursRemaining = 0;
                if ($booking['booking_status'] === 'confirmed' && $booking['payment_status'] === 'pending' && !empty($booking['payment_deadline'])) {
                  $paymentDeadline = new DateTime($booking['payment_deadline']);
                  $timeRemaining = $paymentDeadline->diff($today);
                  $hoursRemaining = ($timeRemaining->invert) ? ($timeRemaining->d * 24 + $timeRemaining->h) : 0;
                }
              ?>
                <div class="booking-card mb-4">
                  <div class="booking-header">
                    <h4><?php echo htmlspecialchars($booking['hotel_name']); ?></h4>
                    <span class="booking-status status-<?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                  </div>

                  <?php if ($booking['booking_status'] === 'confirmed' && $booking['payment_status'] === 'pending'): ?>
                    <div class="alert alert-warning">
                      <i class="fas fa-clock me-2"></i>
                      Payment Required: <?php echo $hoursRemaining; ?> hours remaining to complete payment
                      <br>
                      <small>Booking will be automatically cancelled if payment is not received within the time limit.</small>
                    </div>
                  <?php endif; ?>

                  <div class="booking-details">
                    <div class="booking-detail-item">
                      <i class="fas fa-calendar"></i>
                      <span>Check-in: <?php echo $checkInDate->format('M d, Y'); ?></span>
                    </div>
                    <div class="booking-detail-item">
                      <i class="fas fa-calendar-check"></i>
                      <span>Check-out: <?php echo $checkOutDate->format('M d, Y'); ?></span>
                    </div>
                    <div class="booking-detail-item">
                      <i class="fas fa-bed"></i>
                      <span>Room: <?php echo htmlspecialchars($booking['room_number']); ?> (<?php echo htmlspecialchars($booking['room_type']); ?>)</span>
                    </div>
                    <div class="booking-detail-item">
                      <i class="fas fa-user"></i>
                      <span><?php echo $booking['adults']; ?> Adults<?php echo $booking['children'] > 0 ? ', ' . $booking['children'] . ' Children' : ''; ?></span>
                    </div>
                    <div class="booking-detail-item">
                      <i class="fas fa-receipt"></i>
                      <span>Booking ID: <?php echo htmlspecialchars($booking['booking_reference']); ?></span>
                    </div>
                    <div class="booking-detail-item">
                      <i class="fas fa-dollar-sign"></i>
                      <span>Total Amount: LKR <?= number_format($booking['total_price'], 2) ?></span>
                    </div>
                    <div class="booking-detail-item">
                      <i class="fas fa-money-check"></i>
                      <span>Payment:
                        <?php
                        $paymentStmt = $conn->prepare("
                            SELECT status, payment_method 
                            FROM payments 
                            WHERE booking_id = ? 
                            ORDER BY payment_id DESC 
                            LIMIT 1
                        ");
                        $paymentStmt->execute([$booking['booking_id']]);
                        $paymentInfo = $paymentStmt->fetch(PDO::FETCH_ASSOC);

                        if ($paymentInfo) {
                          if ($paymentInfo['payment_method'] === 'bank_transfer') {
                            echo '<span class="badge bg-info">Bank Transfer - ';
                          } else {
                            echo '<span class="badge bg-warning">Cash Payment - ';
                          }
                          echo ucfirst($paymentInfo['status']) . '</span>';
                        } else {
                          echo '<span class="badge bg-secondary">Not Initiated</span>';
                        }
                        ?>
                      </span>
                    </div>
                  </div>
                  <div class="d-flex gap-2"> <?php if ($booking['booking_status'] === 'confirmed' && $booking['payment_status'] === 'pending'): ?>
                      <?php if (!$paymentInfo): ?>
                        <a href="payment.php?booking_id=<?= $booking['booking_id'] ?>" class="btn btn-success btn-sm">
                          <i class="fas fa-credit-card me-1"></i>Pay Now
                        </a>
                      <?php else: ?>
                        <?php if ($paymentInfo['payment_method'] === 'bank_transfer'): ?>
                          <?php if ($paymentInfo['status'] === 'pending'): ?>
                            <span class="badge bg-info">Bank Transfer Pending Verification</span>
                          <?php elseif ($paymentInfo['status'] === 'completed'): ?>
                            <span class="badge bg-success">Payment Verified</span>
                          <?php else: ?>
                            <span class="badge bg-danger">Payment Failed</span>
                          <?php endif; ?>
                        <?php else: ?>
                          <span class="badge bg-warning">Cash Payment on Arrival</span>
                        <?php endif; ?>
                      <?php endif; ?>
                    <?php endif; ?> <?php if ($booking['payment_status'] === 'paid'): ?>
                      <button class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-download me-1"></i>Invoice
                      </button>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div> <!-- Settings Tab -->
        <div class="tab-pane fade" id="settings" role="tabpanel">
          <div class="content-card">
            <h3 class="mb-4">Account Settings</h3>
            <!-- Password Change Form -->
            <form id="passwordForm" class="mb-5" action="./handlers/update_password.php" method="POST">
              <h5 class="mb-3">Change Password</h5>
              <div class="row g-3">
                <div class="col-md-6">
                  <div class="form-floating">
                    <input
                      type="password"
                      class="form-control"
                      id="currentPassword"
                      name="current_password"
                      required />
                    <label for="currentPassword">Current Password</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-floating">
                    <input
                      type="password"
                      class="form-control"
                      name="new_password"
                      id="newPassword"
                      required />
                    <label for="newPassword">New Password</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-floating">
                    <input
                      type="password"
                      class="form-control"
                      name="confirm_password"
                      id="confirmPassword"
                      required />
                    <label for="confirmPassword">Confirm New Password</label>
                  </div>
                </div>
              </div>
              <button type="submit" name="update_password" class="btn btn-primary mt-3">
                Update Password
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>

  <?php include 'components/footer.php'; ?>

  <!-- Bootstrap & jQuery JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Custom JS -->
  <script src="assets/js/dashboard.js"></script>
</body>

</html>