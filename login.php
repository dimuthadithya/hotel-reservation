<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Pearl Stay</title>
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
  <link href="assets/css/auth.css" rel="stylesheet" />
</head>

<body>
  <?php include 'components/nav.php'; ?>

  <div class="auth-container">
    <div class="auth-card">
      <div class="auth-header">
        <h1>Welcome Back</h1>
        <p>Sign in to your Pearl Stay account</p>
      </div>

      <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
          <?php
          echo $_SESSION['success'];
          unset($_SESSION['success']);
          ?>
        </div>
      <?php endif; ?>

      <?php if (isset($_SESSION['login_errors'])): ?>
        <div class="alert alert-danger">
          <ul class="mb-0" style="font-size: 0.9rem;">
            <?php
            foreach ($_SESSION['login_errors'] as $error) {
              echo "<li>$error</li>";
            }
            unset($_SESSION['login_errors']);
            ?>
          </ul>
        </div>
      <?php endif; ?>

      <form id="loginForm" novalidate action="./handlers/login.php" method="POST">
        <div class="form-floating">
          <input
            type="email"
            class="form-control <?php echo isset($_SESSION['form_data']['email_error']) ? 'is-invalid' : ''; ?>"
            id="email"
            name="email"
            placeholder="Email address"
            value="<?php echo htmlspecialchars($_SESSION['form_data']['email'] ?? ''); ?>"
            required />
          <label for="email">Email address</label>
          <div class="invalid-feedback">Please enter a valid email address</div>
        </div>
        <div class="form-floating position-relative">
          <input
            type="password"
            class="form-control <?php echo isset($_SESSION['form_data']['password_error']) ? 'is-invalid' : ''; ?>"
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
            <?php echo $_SESSION['form_data']['password_error'] ?? 'Please enter your password'; ?>
          </div>
        </div>
        <button type="submit" name="login" class="btn auth-btn">
          Sign In
        </button>
      </form>



      <div class="auth-links">
        <p>Don't have an account? <a href="register.php">Create Account</a></p>
      </div>
    </div>
  </div>

  <?php include 'components/footer.php'; ?>

  <!-- Custom JS -->
  <script src="assets/js/auth.js"></script>
</body>

</html>