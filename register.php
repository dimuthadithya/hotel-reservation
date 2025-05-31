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

      <?php if (isset($_SESSION['register_errors']) && !empty($_SESSION['register_errors'])): ?>
        <div class="alert alert-danger">
          <ul class="mb-0" style="font-size: 0.9rem;">
            <?php
            foreach ($_SESSION['register_errors'] as $error) {
              echo "<li>$error</li>";
            }
            unset($_SESSION['register_errors']);
            ?>
          </ul>
        </div>
      <?php endif; ?>

      <form
        id="registerForm"
        novalidate
        action="./handlers/register.php"
        method="POST">
        <div class="row g-2">
          <div class="col-md-6">
            <div class="form-floating"> <input
                type="text"
                class="form-control <?php echo isset($_SESSION['register_errors']['first_name']) ? 'is-invalid' : ''; ?>"
                id="firstName"
                name="first_name"
                placeholder="First Name"
                value="<?php echo htmlspecialchars($_SESSION['register_form_data']['first_name'] ?? ''); ?>"
                required />
              <label for="firstName">First Name</label>
              <div class="invalid-feedback" style="font-size: 0.75rem">
                <?php echo $_SESSION['register_errors']['first_name'] ?? 'Please enter your first name'; ?>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-floating"> <input
                type="text"
                class="form-control <?php echo isset($_SESSION['register_errors']['last_name']) ? 'is-invalid' : ''; ?>"
                id="lastName"
                name="last_name"
                placeholder="Last Name"
                value="<?php echo htmlspecialchars($_SESSION['register_form_data']['last_name'] ?? ''); ?>"
                required />
              <label for="lastName">Last Name</label>
              <div class="invalid-feedback" style="font-size: 0.75rem">
                <?php echo $_SESSION['register_errors']['last_name'] ?? 'Please enter your last name'; ?>
              </div>
            </div>
          </div>
        </div>

        <div class="form-floating"> <input
            type="email"
            class="form-control <?php echo isset($_SESSION['register_errors']['email']) ? 'is-invalid' : ''; ?>"
            id="email"
            name="email"
            placeholder="Email address"
            value="<?php echo htmlspecialchars($_SESSION['register_form_data']['email'] ?? ''); ?>"
            required />
          <label for="email">Email address</label>
          <div class="invalid-feedback">
            <?php echo $_SESSION['register_errors']['email'] ?? 'Please enter a valid email address'; ?>
          </div>
        </div>

        <div class="form-floating"> <input
            type="tel"
            class="form-control <?php echo isset($_SESSION['register_errors']['phone']) ? 'is-invalid' : ''; ?>"
            id="phone"
            name="phone"
            placeholder="Phone number"
            value="<?php echo htmlspecialchars($_SESSION['register_form_data']['phone'] ?? ''); ?>" />
          <label for="phone">Phone number (optional)</label>
          <?php if (isset($_SESSION['register_errors']['phone'])): ?>
            <div class="invalid-feedback">
              <?php echo $_SESSION['register_errors']['phone']; ?>
            </div>
          <?php endif; ?>
        </div>

        <div class="form-floating position-relative"> <input
            type="password"
            class="form-control <?php echo isset($_SESSION['register_errors']['password']) ? 'is-invalid' : ''; ?>"
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
            <?php echo $_SESSION['register_errors']['password'] ?? 'Password must be at least 8 characters with uppercase, lowercase, and number'; ?>
          </div>
        </div>

        <div class="password-strength">
          <div class="strength-meter" id="strengthMeter"></div>
        </div>

        <div class="form-floating"> <input
            type="password"
            class="form-control <?php echo isset($_SESSION['register_errors']['confirm_password']) ? 'is-invalid' : ''; ?>"
            id="confirmPassword"
            name="confirmPassword"
            placeholder="Confirm Password"
            required />
          <label for="confirmPassword">Confirm Password</label>
          <div class="invalid-feedback">
            <?php echo $_SESSION['register_errors']['confirm_password'] ?? 'Passwords do not match'; ?>
          </div>
        </div>

        <button type="submit" class="btn auth-btn">Create Account</button>
      </form>



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