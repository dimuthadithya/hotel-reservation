<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact Us - Pearl Stay</title>
  <!-- Favicon -->
  <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon_io/apple-touch-icon.png" />
  <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon_io/favicon-32x32.png" />
  <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon_io/favicon-16x16.png" />
  <link rel="manifest" href="assets/favicon_io/site.webmanifest" />
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  <!-- Custom CSS -->
  <link href="assets/css/styles.css" rel="stylesheet" />
  <link href="assets/css/nav.css" rel="stylesheet" />
  <link href="assets/css/footer.css" rel="stylesheet" />
  <link href="assets/css/contact.css" rel="stylesheet" />
</head>

<body>
  <?php include 'components/nav.php'; ?>

  <div class="container py-5">
    <div class="row">
      <div class="col-lg-8">
        <h1 class="mb-4">Contact Us</h1>
        <p class="lead mb-5">Have any questions? We'd love to hear from you.</p>

        <form id="contactForm" class="contact-form">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control" id="name" placeholder="Your Name" required />
                <label for="name">Your Name</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="email" class="form-control" id="email" placeholder="Email Address" required />
                <label for="email">Email Address</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating">
                <input type="text" class="form-control" id="subject" placeholder="Subject" required />
                <label for="subject">Subject</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating">
                <textarea class="form-control" id="message" style="height: 150px" placeholder="Message" required></textarea>
                <label for="message">Message</label>
              </div>
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-primary">Send Message</button>
            </div>
          </div>
        </form>
      </div>

      <div class="col-lg-4">
        <div class="contact-info mt-5 mt-lg-0">
          <h3>Get in Touch</h3>
          <div class="info-item">
            <i class="fas fa-map-marker-alt"></i>
            <div>
              <h4>Location</h4>
              <p>123 Kandy Lake Road<br>Kandy, Sri Lanka</p>
            </div>
          </div>
          <div class="info-item">
            <i class="fas fa-phone"></i>
            <div>
              <h4>Call Us</h4>
              <p>+94 81 234 5678<br>+94 81 234 5679</p>
            </div>
          </div>
          <div class="info-item">
            <i class="fas fa-envelope"></i>
            <div>
              <h4>Email Us</h4>
              <p>info@pearlstay.com<br>support@pearlstay.com</p>
            </div>
          </div>
          <div class="info-item">
            <i class="fas fa-clock"></i>
            <div>
              <h4>Open Hours</h4>
              <p>Monday - Friday<br>9:00 AM - 5:00 PM</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-5">
      <div class="col-12">
        <div class="map-container">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31736.178147705297!2d80.62151672525179!3d7.292803818730325!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae366266498acd3%3A0x411a3818a1e03c35!2sKandy%2C%20Sri%20Lanka!5e0!3m2!1sen!2sus!4v1624451234567!5m2!1sen!2sus"
            width="100%"
            height="450"
            style="border:0;"
            allowfullscreen=""
            loading="lazy">
          </iframe>
        </div>
      </div>
    </div>
  </div>

  <?php include 'components/footer.php'; ?>

  <!-- Custom JS -->
  <script src="assets/js/contact.js"></script>
</body>

</html>