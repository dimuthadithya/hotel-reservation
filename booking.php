<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Complete Your Booking - Pearl Stay</title>
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
  <link href="assets/css/booking.css" rel="stylesheet" />
</head>

<body>
  <?php include 'components/nav.php'; ?>

  <!-- Booking Section -->
  <div class="booking-container">
    <div class="container">
      <!-- Booking Steps -->
      <div class="booking-steps mb-4">
        <div class="row">
          <div class="col-4">
            <div class="step-item completed">
              <div class="step-number">1</div>
              <div class="step-title">Room Selection</div>
            </div>
          </div>
          <div class="col-4">
            <div class="step-item active">
              <div class="step-number">2</div>
              <div class="step-title">Guest Details</div>
            </div>
          </div>
          <div class="col-4">
            <div class="step-item">
              <div class="step-number">3</div>
              <div class="step-title">Payment</div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <!-- Booking Form -->
        <div class="col-lg-8">
          <form id="bookingForm">
            <!-- Dates Section -->
            <div class="booking-form-section">
              <h3 class="form-section-title">
                <i class="fas fa-calendar"></i>
                Stay Dates
              </h3>
              <div class="date-inputs">
                <div class="form-floating">
                  <input
                    type="date"
                    class="form-control"
                    id="checkInDate"
                    required />
                  <label for="checkInDate">Check-in Date</label>
                </div>
                <div class="form-floating">
                  <input
                    type="date"
                    class="form-control"
                    id="checkOutDate"
                    required />
                  <label for="checkOutDate">Check-out Date</label>
                </div>
              </div>
            </div>

            <!-- Guest Information -->
            <div class="booking-form-section">
              <h3 class="form-section-title">
                <i class="fas fa-user"></i>
                Guest Information
              </h3>
              <div class="guest-inputs">
                <div class="form-floating">
                  <input
                    type="text"
                    class="form-control"
                    id="firstName"
                    required />
                  <label for="firstName">First Name</label>
                </div>
                <div class="form-floating">
                  <input
                    type="text"
                    class="form-control"
                    id="lastName"
                    required />
                  <label for="lastName">Last Name</label>
                </div>
                <div class="form-floating">
                  <input
                    type="email"
                    class="form-control"
                    id="email"
                    required />
                  <label for="email">Email Address</label>
                </div>
                <div class="form-floating">
                  <input
                    type="tel"
                    class="form-control"
                    id="phone"
                    required />
                  <label for="phone">Phone Number</label>
                </div>
              </div>
              <div class="mt-3">
                <div class="form-floating">
                  <textarea
                    class="form-control"
                    id="specialRequests"
                    style="height: 100px"></textarea>
                  <label for="specialRequests">Special Requests (Optional)</label>
                </div>
              </div>
            </div>

            <!-- Payment Section -->
            <div class="booking-form-section">
              <h3 class="form-section-title">
                <i class="fas fa-credit-card"></i>
                Payment Information
              </h3>
              <div class="secure-payment">
                <i class="fas fa-lock"></i>
                <span>Secure Payment - SSL Encrypted</span>
              </div>
              <div class="payment-methods">
                <div
                  class="payment-method-option selected"
                  data-payment-type="credit-card">
                  <i class="fas fa-credit-card"></i>
                  <div>Credit Card</div>
                </div>
                <div
                  class="payment-method-option"
                  data-payment-type="debit-card">
                  <i class="fas fa-credit-card"></i>
                  <div>Debit Card</div>
                </div>
                <div
                  class="payment-method-option"
                  data-payment-type="online-banking">
                  <i class="fas fa-university"></i>
                  <div>Online Banking</div>
                </div>
              </div>

              <div id="creditCardForm">
                <div class="form-floating mb-3">
                  <input
                    type="text"
                    class="form-control"
                    id="cardName"
                    required />
                  <label for="cardName">Name on Card</label>
                </div>
                <div class="card-input-group">
                  <div class="form-floating">
                    <input
                      type="text"
                      class="form-control"
                      id="cardNumber"
                      required />
                    <label for="cardNumber">Card Number</label>
                  </div>
                  <div class="form-floating">
                    <input
                      type="text"
                      class="form-control"
                      id="expiryDate"
                      placeholder="MM/YY"
                      required />
                    <label for="expiryDate">Expiry Date</label>
                  </div>
                  <div class="form-floating">
                    <input
                      type="text"
                      class="form-control"
                      id="cvv"
                      required />
                    <label for="cvv">CVV</label>
                  </div>
                </div>
              </div>
            </div>

            <!-- Terms and Policies -->
            <div class="booking-form-section">
              <h3 class="form-section-title">
                <i class="fas fa-file-contract"></i>
                Policies & Terms
              </h3>
              <div class="policy-section">
                <div class="policy-item">
                  <i class="fas fa-info-circle"></i>
                  <div>
                    <strong>Cancellation Policy:</strong>
                    <p class="mb-0">
                      Free cancellation up to 48 hours before check-in. After
                      that, the first night's charge applies.
                    </p>
                  </div>
                </div>
                <div class="policy-item">
                  <i class="fas fa-clock"></i>
                  <div>
                    <strong>Check-in/Check-out:</strong>
                    <p class="mb-0">
                      Check-in: 2:00 PM - 12:00 AM<br />Check-out: 11:00 AM
                    </p>
                  </div>
                </div>
              </div>

              <div class="form-check terms-checkbox">
                <input
                  class="form-check-input"
                  type="checkbox"
                  id="termsCheckbox"
                  required />
                <label class="form-check-label" for="termsCheckbox">
                  I agree to the <a href="#">Terms & Conditions</a> and
                  <a href="#">Privacy Policy</a>
                </label>
              </div>

              <button type="submit" class="btn btn-primary w-100">
                Confirm Booking
              </button>
            </div>
          </form>
        </div>

        <!-- Booking Summary -->
        <div class="col-lg-4">
          <div class="booking-summary-card">
            <img
              src="assets/img/luxury-suite.jpg"
              alt="Deluxe Lake View Room"
              class="hotel-thumb" />
            <h4>Luxury Resort Kandy</h4>
            <p class="text-muted">Deluxe Lake View Room</p>

            <div class="summary-details mt-4">
              <div class="summary-detail">
                <span>Room Rate (per night)</span>
                <span>LKR 25,000</span>
              </div>
              <div class="summary-detail">
                <span>Total Room Rate</span>
                <span id="basePriceTotal">LKR 25,000</span>
              </div>
              <div class="summary-detail">
                <span>Taxes (10%)</span>
                <span id="taxesTotal">LKR 2,500</span>
              </div>
              <div class="summary-detail">
                <span>Service Fee</span>
                <span id="serviceFeeTotal">LKR 2,500</span>
              </div>
              <div class="summary-detail summary-total">
                <span>Total Amount</span>
                <span id="finalTotal">LKR 30,000</span>
              </div>
            </div>

            <div class="policy-section mt-4">
              <div class="d-flex align-items-center gap-2 mb-2">
                <i class="fas fa-shield-alt text-success"></i>
                <span>Free cancellation available</span>
              </div>
              <div class="d-flex align-items-center gap-2">
                <i class="fas fa-clock text-warning"></i>
                <span>Book now to secure your dates</span>
              </div>
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
  <script src="assets/js/booking.js"></script>
</body>

</html>