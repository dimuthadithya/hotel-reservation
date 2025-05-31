<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Luxury Resort Kandy - Pearl Stay</title>
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
  <link href="assets/css/hotel-details.css" rel="stylesheet" />
</head>

<body>
  <?php include 'components/nav.php'; ?>

  <!-- Gallery Section -->
  <section class="gallery-section">
    <div class="main-gallery">
      <img src="assets/img/luxury-suite.jpg" alt="Luxury Resort Kandy" />
      <a href="#" class="virtual-tour-btn">
        <i class="fas fa-vr-cardboard me-2"></i>Virtual Tour
      </a>
    </div>
    <div class="gallery-thumbnails">
      <div class="container">
        <div class="thumbnail-container">
          <div class="row g-2">
            <div class="col">
              <img
                src="assets/img/luxury-suite.jpg"
                class="gallery-thumb active w-100"
                data-full-img="assets/img/luxury-suite.jpg"
                alt="Room View" />
            </div>
            <div class="col">
              <img
                src="assets/img/pool.jpg"
                class="gallery-thumb w-100"
                data-full-img="assets/img/pool.jpg"
                alt="Pool" />
            </div>
            <div class="col">
              <img
                src="assets/img/restaurant.jpg"
                class="gallery-thumb w-100"
                data-full-img="assets/img/restaurant.jpg"
                alt="Restaurant" />
            </div>
            <div class="col">
              <img
                src="assets/img/spa.jpg"
                class="gallery-thumb w-100"
                data-full-img="assets/img/spa.jpg"
                alt="Spa" />
            </div>
            <div class="col">
              <button class="btn btn-outline-primary w-100 h-100">
                +12 More
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Hotel Info Section -->
  <section class="hotel-info">
    <div class="container">
      <div class="row">
        <div class="col-lg-8">
          <div class="hotel-title">
            <h1 class="hotel-name">Luxury Resort Kandy</h1>
            <p class="hotel-location">
              <i class="fas fa-map-marker-alt me-2"></i>
              Kandy Lake, 2.5km to City Center
            </p>
          </div>
          <div class="hotel-description mb-4">
            <h2 class="h4 mb-3">About This Property</h2>
            <p>
              Nestled in the heart of Kandy, this luxury resort offers a
              perfect blend of modern comfort and traditional Sri Lankan
              hospitality. With breathtaking views of the Kandy Lake and the
              surrounding mountains, guests can enjoy a truly memorable stay
              in one of Sri Lanka's most historic cities.
            </p>
            <p>
              The resort features world-class amenities including a spa,
              infinity pool, and multiple dining options serving both local
              and international cuisine.
            </p>
          </div>
          <div class="amenities-section mb-4">
            <h2 class="h4 mb-3">Property Amenities</h2>
            <div class="amenities-list">
              <div class="amenity-item">
                <i class="fas fa-wifi amenity-icon"></i>
                <span>Free WiFi</span>
              </div>
              <div class="amenity-item">
                <i class="fas fa-swimming-pool amenity-icon"></i>
                <span>Infinity Pool</span>
              </div>
              <div class="amenity-item">
                <i class="fas fa-spa amenity-icon"></i>
                <span>Luxury Spa</span>
              </div>
              <div class="amenity-item">
                <i class="fas fa-utensils amenity-icon"></i>
                <span>Restaurant</span>
              </div>
              <div class="amenity-item">
                <i class="fas fa-leaf amenity-icon"></i>
                <span>Ayurveda Center</span>
              </div>
              <div class="amenity-item">
                <i class="fas fa-car amenity-icon"></i>
                <span>Free Parking</span>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="rating-summary text-center">
            <div class="overall-rating mb-2">9.2</div>
            <div class="rating-text h5 mb-3">Excellent</div>
            <p class="text-muted">Based on 234 reviews</p>
            <div class="rating-bars">
              <div class="rating-bar-item">
                <span>Cleanliness</span>
                <div class="rating-bar">
                  <div class="rating-fill" style="width: 95%"></div>
                </div>
                <span>9.5</span>
              </div>
              <div class="rating-bar-item">
                <span>Service</span>
                <div class="rating-bar">
                  <div class="rating-fill" style="width: 90%"></div>
                </div>
                <span>9.0</span>
              </div>
              <div class="rating-bar-item">
                <span>Location</span>
                <div class="rating-bar">
                  <div class="rating-fill" style="width: 92%"></div>
                </div>
                <span>9.2</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Room Selection -->
  <section class="room-selection py-5">
    <div class="container">
      <h2 class="section-title">Available Rooms</h2>
      <div class="row">
        <div class="col-12">
          <div class="room-card-detailed">
            <div class="row g-0">
              <div class="col-md-4">
                <div class="room-image">
                  <img
                    src="assets/img/luxury-suite.jpg"
                    alt="Deluxe Lake View Room" />
                </div>
              </div>
              <div class="col-md-8">
                <div class="room-details">
                  <h3 class="room-type">Deluxe Lake View Room</h3>
                  <div class="room-features">
                    <span class="feature-badge"><i class="fas fa-ruler-combined me-2"></i>40 m²</span>
                    <span class="feature-badge"><i class="fas fa-bed me-2"></i>King Bed</span>
                    <span class="feature-badge"><i class="fas fa-mountain me-2"></i>Lake View</span>
                  </div>
                  <p class="room-description">
                    Spacious room with a private balcony offering stunning
                    views of Kandy Lake. Features modern amenities and
                    traditional Sri Lankan decor.
                  </p>
                  <div
                    class="d-flex justify-content-between align-items-center mt-3">
                    <div class="room-price">
                      LKR 25,000 <span class="price-period">/night</span>
                    </div>
                    <button
                      class="btn btn-primary select-room-btn"
                      data-room-id="1"
                      data-room-name="Deluxe Lake View Room">
                      Select Room
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Reviews Section -->
  <section class="reviews-section">
    <div class="container">
      <h2 class="section-title">Guest Reviews</h2>
      <div class="row g-4">
        <div class="col-lg-8">
          <div class="review-filters mb-4">
            <button class="btn btn-outline-primary me-2 active">
              All Reviews
            </button>
            <button class="btn btn-outline-primary me-2">
              Excellent (150)
            </button>
            <button class="btn btn-outline-primary me-2">Good (50)</button>
            <button class="btn btn-outline-primary">Average (20)</button>
          </div>
          <div class="reviews-list">
            <div class="review-card-detailed">
              <div class="review-header">
                <div class="reviewer-info">
                  <img
                    src="assets/img/avatar1.jpg"
                    alt="Sarah M."
                    class="reviewer-avatar" />
                  <div>
                    <h5 class="mb-0">Sarah M.</h5>
                    <span class="review-date">May 2024</span>
                  </div>
                </div>
                <div class="review-rating">★★★★★</div>
              </div>
              <div class="review-content">
                <p>
                  "Absolutely amazing stay! The view of Kandy Lake from our
                  room was breathtaking. The staff went above and beyond to
                  make our stay comfortable. The Ayurvedic spa treatment was a
                  highlight of our trip."
                </p>
              </div>
            </div>
            <!-- More review cards would go here -->
          </div>
        </div>
        <div class="col-lg-4">
          <div class="map-section">
            <div class="map-container" id="hotelMap">
              <!-- Map will be initialized here -->
              <img
                src="https://via.placeholder.com/400x400.png?text=Location+Map"
                alt="Hotel Location Map"
                class="w-100 h-100"
                style="object-fit: cover" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <?php include 'components/footer.php'; ?>

  <!-- Bootstrap & jQuery JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Custom JS -->
  <script src="assets/js/hotel-details.js"></script>
</body>

</html>