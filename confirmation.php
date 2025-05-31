<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Booking Confirmed - Pearl Stay</title>
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
  <link href="assets/css/confirmation.css" rel="stylesheet" />
</head>

<body>
  <?php include 'components/nav.php'; ?>

  <!-- Confirmation Content -->
  <div class="confirmation-container">
    <div class="container">
      <div class="confirmation-card">
        <!-- Success Header -->
        <div class="success-header">
          <div class="success-icon">
            <i class="fas fa-check"></i>
          </div>
          <h1>Booking Confirmed!</h1>
          <p class="text-muted">
            Thank you for choosing Pearl Stay. Your booking has been
            confirmed.
          </p>
        </div>

        <!-- Booking Reference -->
        <div class="booking-reference">
          <p class="mb-1">Your Booking Reference</p>
          <div class="reference-number">BK123456</div>
        </div>

        <!-- Reservation Summary -->
        <div class="confirmation-section">
          <h2 class="section-title">
            <i class="fas fa-file-alt"></i>
            Reservation Summary
          </h2>
          <div class="detail-grid">
            <div class="detail-item" id="hotelName">
              <span class="detail-label">Hotel</span>
              <span class="detail-value">Luxury Resort Kandy</span>
            </div>
            <div class="detail-item" id="roomType">
              <span class="detail-label">Room Type</span>
              <span class="detail-value">Deluxe Lake View Room</span>
            </div>
            <div class="detail-item" id="checkInDate">
              <span class="detail-label">Check-in</span>
              <span class="detail-value">May 15, 2024</span>
            </div>
            <div class="detail-item" id="checkOutDate">
              <span class="detail-label">Check-out</span>
              <span class="detail-value">May 18, 2024</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Guests</span>
              <span class="detail-value">2 Adults</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Total Amount</span>
              <span class="detail-value">LKR 85,000</span>
            </div>
          </div>
        </div>

        <!-- Check-in Instructions -->
        <div class="confirmation-section">
          <h2 class="section-title">
            <i class="fas fa-clipboard-check"></i>
            Check-in Instructions
          </h2>
          <div class="important-info">
            <p class="mb-2">
              <strong>Check-in Time:</strong> 2:00 PM - 12:00 AM
            </p>
            <p class="mb-0">Please present the following at check-in:</p>
            <ul class="mb-0">
              <li>Government-issued photo ID</li>
              <li>Credit card used for booking</li>
              <li>Booking confirmation (digital copy accepted)</li>
            </ul>
          </div>
        </div>

        <!-- Hotel Contact Information -->
        <div class="confirmation-section">
          <h2 class="section-title">
            <i class="fas fa-hotel"></i>
            Hotel Information
          </h2>
          <div class="row">
            <div class="col-md-6">
              <div class="detail-item" id="hotelAddress">
                <span class="detail-label">Address</span>
                <span class="detail-value">
                  123 Kandy Lake Road<br />
                  Kandy, Sri Lanka
                </span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Phone</span>
                <span class="detail-value">+94 81 234 5678</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Email</span>
                <span class="detail-value">info@luxuryresortkandy.com</span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="map-container" id="hotelMap">
                <!-- Map will be initialized here -->
                <img
                  src="https://via.placeholder.com/600x300.png?text=Location+Map"
                  alt="Hotel Location Map"
                  class="w-100 h-100"
                  style="object-fit: cover" />
              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="action-buttons">
          <button
            class="btn action-button calendar-btn"
            id="addToCalendarBtn">
            <i class="fas fa-calendar-plus"></i>
            Add to Calendar
          </button>
          <button class="btn action-button print-btn" id="printBtn">
            <i class="fas fa-print"></i>
            Print Confirmation
          </button>
          <a
            href="https://maps.google.com"
            target="_blank"
            class="btn action-button calendar-btn">
            <i class="fas fa-directions"></i>
            Get Directions
          </a>
        </div>
      </div>
    </div>
  </div>

  <?php include 'components/footer.php'; ?>

  <!-- Bootstrap & jQuery JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Custom JS -->
  <script src="assets/js/confirmation.js"></script>
</body>

</html>