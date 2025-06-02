<?php
session_start();
include './config/db.php';

$sql = "SELECT * FROM hotels WHERE status = 'active' LIMIT 3";
$stmt = $conn->query($sql);
$hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pearl Stay - Sri Lankan Hospitality</title>
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
  <!-- Bootstrap 5 CSS -->
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
</head>

<body>
  <?php include 'components/nav.php'; ?>

  <!-- Hero Section -->
  <section class="hero-section" id="home">
    <div
      id="heroCarousel"
      class="carousel slide carousel-fade"
      data-bs-ride="carousel"
      data-bs-interval="5000">
      <div class="carousel-inner">
        <div class="carousel-item active">
          <img
            src="assets/img/hero-1.jpg"
            class="d-block w-100"
            alt="Luxury Hotel" />
          <div class="carousel-caption text-start">
            <span class="caption-subtitle">Welcome to</span>
            <h1 class="caption-title">DreamStay Hotel</h1>
            <p class="caption-desc">
              Where luxury meets comfort in the heart of paradise
            </p>
            <div class="caption-buttons">
              <a href="./search-listings.php" class="btn btn-primary me-3">View Our Hotels</a>
            </div>
          </div>
        </div>
        <div class="carousel-item">
          <img
            src="assets/img/hero-2.jpg"
            class="d-block w-100"
            alt="Luxury Suite" />
          <div class="carousel-caption text-start">
            <span class="caption-subtitle">Experience</span>
            <h1 class="caption-title">Ultimate Luxury</h1>
            <p class="caption-desc">
              Indulge in our world-class amenities and services
            </p>
            <div class="caption-buttons">
              <a href="#amenities" class="btn btn-primary">Explore Amenities</a>
            </div>
          </div>
        </div>
        <div class="carousel-item">
          <img
            src="assets/img/hero-3.jpg"
            class="d-block w-100"
            alt="Special Offer" />
          <div class="carousel-caption text-start">
            <span class="caption-subtitle">Special Offer</span>
            <h1 class="caption-title">Summer Paradise</h1>
            <p class="caption-desc">
              Get 20% off on all suite bookings this summer
            </p>
            <div class="caption-buttons">
              <a href="#book" class="btn btn-primary">Book Now</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Rooms Section -->
  <section class="py-5" id="rooms">
    <div class="container">
      <div class="row">
        <?php
        // hotels
        foreach ($hotels as $hotel) {

          $hotelName = $hotel['hotel_name'];
          $hotelId = $hotel['hotel_id'];
          $hotelDistrict   = $hotel['district'];
          $hotelImage = $hotel['main_image'];


          include './components/indexHotelCard.php';
        }
        ?>
      </div>
    </div>
  </section>

  <!-- Amenities Section -->
  <section class="bg-light py-5">
    <div class="container">
      <h2 class="text-center mb-5">Featured Amenities</h2>
      <div class="row">
        <div class="col-md-3">
          <div class="amenity-card">
            <img src="assets/img/pool.jpg" alt="Swimming Pool" class="mb-3" />
            <h5>Swimming Pool</h5>
            <p>Relax and unwind in our outdoor swimming pool</p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="amenity-card">
            <img src="assets/img/gym.jpg" alt="Fitness Center" class="mb-3" />
            <h5>Fitness Center</h5>
            <p>Stay active with our state-of-the-art fitness center</p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="amenity-card">
            <img src="assets/img/spa.jpg" alt="Spa & Wellness" class="mb-3" />
            <h5>Spa & Wellness</h5>
            <p>Rejuvenate your senses at our luxurious spa</p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="amenity-card">
            <img
              src="assets/img/restaurant.jpg"
              alt="Fine Dining"
              class="mb-3" />
            <h5>Fine Dining</h5>
            <p>Savor exquisite cuisine at our fine restaurant</p>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Summer Offer Section -->
  <section class="summer-offer">
    <div class="container text-center">
      <h2 class="mb-4">Exclusive Summer Offer</h2>
      <p class="lead mb-4">
        Book your stay now and get 20% off on all room types!
      </p>
      <p class="mb-4">Limited time offer!</p>
      <a href="#" class="btn btn-primary btn-lg">Book Now</a>
    </div>
  </section>

  <!-- Newsletter Section -->
  <section class="newsletter-section">
    <div class="container text-center">
      <h2 class="mb-4">Stay Updated</h2>
      <p class="mb-4">
        Sign up for our newsletter to receive exclusive offers and travel
        tips.
      </p>
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="input-group mb-3">
            <input
              type="email"
              class="form-control"
              placeholder="Enter your email" />
            <button class="btn btn-primary" type="button">Subscribe</button>
          </div>
        </div>
      </div>
    </div>
  </section> <?php include 'components/footer.php'; ?>

  <!-- Custom JS -->
  <script src="assets/js/main.js"></script>
</body>

</html>