<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register - Pearl Stay</title>
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
  <link rel="manifest" href="assets/favicon_io/site.webmanifest" /> <!-- Bootstrap CSS -->
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
  <link href="assets/css/auth.css" rel="stylesheet" />
</head>

<body>
  <?php include 'components/nav.php'; ?>

  <div class="auth-container">
    <div class="auth-card">
      <div class="auth-header">
        <h1>Create Account</h1>
        <p>Join us to discover authentic Sri Lankan stays</p>
      </div>

      <form
        id="registerForm"
        novalidate
        action="./handlers/register.php"
        method="POST">
        <div class="row g-2">
          <div class="col-md-6">
            <div class="form-floating">
              <input
                type="text"
                class="form-control"
                id="firstName"
                name="first_name"
                placeholder="First Name"
                required />
              <label for="firstName">First Name</label>
              <div class="invalid-feedback" style="font-size: 0.75rem">
                Please enter your first name
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-floating">
              <input
                type="text"
                class="form-control"
                id="lastName"
                name="last_name"
                placeholder="Last Name"
                required />
              <label for="lastName">Last Name</label>
              <div class="invalid-feedback" style="font-size: 0.75rem">
                Please enter your last name
              </div>
            </div>
          </div>
        </div>

        <div class="form-floating">
          <input
            type="email"
            class="form-control"
            id="email"
            name="email"
            placeholder="Email address"
            required />
          <label for="email">Email address</label>
          <div class="invalid-feedback">
            Please enter a valid email address
          </div>
        </div>

        <div class="form-floating">
          <input
            type="tel"
            class="form-control"
            id="phone"
            name="phone"
            placeholder="Phone number" />
          <label for="phone">Phone number (optional)</label>
        </div>

        <div class="form-floating position-relative">
          <input
            type="password"
            class="form-control"
            id="password"
            name="password"
            placeholder="Password"
            required />
          <label for="password">Password</label>
          <button
            type="button"
            class="password-toggle"
            id="passwordToggle"
            tabindex="-1">
            <i class="fas fa-eye"></i>
          </button>
          <div class="invalid-feedback">
            Password must be at least 8 characters
          </div>
        </div>

        <div class="password-strength">
          <div class="strength-meter" id="strengthMeter"></div>
        </div>

        <div class="form-floating">
          <input
            type="password"
            class="form-control"
            id="confirmPassword"
            placeholder="Confirm Password"
            required />
          <label for="confirmPassword">Confirm Password</label>
          <div class="invalid-feedback">Passwords do not match</div>
        </div>

        <div class="form-check mb-2">
          <input
            class="form-check-input"
            type="checkbox"
            id="terms"
            required />
          <label
            class="form-check-label"
            for="terms"
            style="font-size: 0.75rem; line-height: 1.3">
            I agree to the <a href="#">Terms of Service</a> and
            <a href="#">Privacy Policy</a>
          </label>
          <div class="invalid-feedback" style="font-size: 0.75rem">
            You must agree to the terms to continue
          </div>
        </div>

        <button type="submit" class="btn auth-btn">Create Account</button>
      </form>

      <div class="social-auth">
        <p>Or register with</p>
        <div class="social-buttons">
          <a href="#" class="social-btn">
            <i class="fab fa-google"></i>
          </a>
          <a href="#" class="social-btn">
            <i class="fab fa-facebook-f"></i>
          </a>
          <a href="#" class="social-btn">
            <i class="fab fa-twitter"></i>
          </a>
        </div>
      </div>

      <div class="auth-links">
        <p>Already have an account? <a href="login.php">Sign In</a></p>
      </div>
    </div>
  </div>

  <?php include 'components/footer.php'; ?>

  <!-- Custom JS -->
  <script src="assets/js/auth.js"></script>
</body>

</html>