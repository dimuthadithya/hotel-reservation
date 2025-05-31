<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Search Stays - Pearl Stay</title>
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
  <!-- Range Slider -->
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.0/nouislider.min.css"
    rel="stylesheet" />
  <!-- Custom CSS -->
  <link href="assets/css/styles.css" rel="stylesheet" />
  <link href="assets/css/nav.css" rel="stylesheet" />
  <link href="assets/css/footer.css" rel="stylesheet" />
  <link href="assets/css/search.css" rel="stylesheet" />
</head>

<body>
  <?php include 'components/nav.php'; ?>

  <!-- Search Header -->
  <section class="search-header py-3 bg-light border-bottom">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-8">
          <div class="search-summary d-flex align-items-center gap-3">
            <div class="location">
              <i class="fas fa-map-marker-alt text-primary"></i>
              <span class="ms-2">Kandy, Sri Lanka</span>
            </div>
            <div class="dates">
              <i class="fas fa-calendar text-primary"></i>
              <span class="ms-2">Jun 1 - Jun 5</span>
            </div>
            <div class="guests">
              <i class="fas fa-user text-primary"></i>
              <span class="ms-2">2 Adults</span>
            </div>
            <button class="btn btn-sm btn-outline-primary">
              Modify Search
            </button>
          </div>
        </div>
        <div class="col-lg-4 text-end">
          <div class="d-flex justify-content-end gap-2">
            <select class="form-select form-select-sm w-auto">
              <option>Sort by: Recommended</option>
              <option>Price: Low to High</option>
              <option>Price: High to Low</option>
              <option>Guest Rating</option>
            </select>
            <button class="btn btn-sm btn-outline-primary">
              <i class="fas fa-map-marked-alt"></i> Map View
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Main Content -->
  <div class="container py-4">
    <div class="row g-4">
      <!-- Filter Sidebar -->
      <div class="col-lg-3">
        <div class="filters-sidebar">
          <div class="filter-section">
            <h5 class="filter-title">Price Range</h5>
            <div id="price-range"></div>
            <div class="price-inputs mt-2">
              <span id="price-min"></span> - <span id="price-max"></span>
            </div>
          </div>

          <div class="filter-section">
            <h5 class="filter-title">Property Type</h5>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="hotel" />
              <label class="form-check-label" for="hotel">Hotels</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="villa" />
              <label class="form-check-label" for="villa">Villas</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="homestay" />
              <label class="form-check-label" for="homestay">Homestays</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="resort" />
              <label class="form-check-label" for="resort">Resorts</label>
            </div>
          </div>

          <div class="filter-section">
            <h5 class="filter-title">Star Rating</h5>
            <div class="star-rating-filters">
              <button class="btn btn-outline-secondary btn-sm me-2 mb-2">
                5 ★
              </button>
              <button class="btn btn-outline-secondary btn-sm me-2 mb-2">
                4 ★
              </button>
              <button class="btn btn-outline-secondary btn-sm me-2 mb-2">
                3 ★
              </button>
              <button class="btn btn-outline-secondary btn-sm mb-2">
                2 ★
              </button>
            </div>
          </div>

          <div class="filter-section">
            <h5 class="filter-title">Popular Amenities</h5>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="wifi" />
              <label class="form-check-label" for="wifi">Free WiFi</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="pool" />
              <label class="form-check-label" for="pool">Swimming Pool</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="beach" />
              <label class="form-check-label" for="beach">Beach Access</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="spa" />
              <label class="form-check-label" for="spa">Spa</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="ayurveda" />
              <label class="form-check-label" for="ayurveda">Ayurveda Center</label>
            </div>
          </div>

          <div class="filter-section">
            <h5 class="filter-title">Guest Rating</h5>
            <div class="rating-buttons">
              <button class="btn btn-outline-secondary btn-sm me-2 mb-2">
                Excellent 9+
              </button>
              <button class="btn btn-outline-secondary btn-sm me-2 mb-2">
                Very Good 8+
              </button>
              <button class="btn btn-outline-secondary btn-sm mb-2">
                Good 7+
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Hotel Listings -->
      <div class="col-lg-9">
        <div class="results-count mb-4">
          <h4>15 properties found in Kandy</h4>
        </div>

        <!-- Hotel Card -->
        <div class="hotel-card mb-4">
          <div class="row g-0">
            <div class="col-md-4">
              <div class="hotel-image">
                <img
                  src="assets/img/kandy-hotel.jpg"
                  alt="Luxury Resort Kandy"
                  class="img-fluid" />
                <button class="btn btn-sm btn-like">
                  <i class="far fa-heart"></i>
                </button>
              </div>
            </div>
            <div class="col-md-8">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                  <div>
                    <h5 class="card-title">Luxury Resort Kandy</h5>
                    <div class="hotel-rating">
                      <span class="stars">★★★★★</span>
                      <span class="rating-text">5-star hotel</span>
                    </div>
                    <p class="location">
                      <i class="fas fa-map-marker-alt"></i> Kandy Lake, 2.5km
                      to City Center
                    </p>
                  </div>
                  <div class="text-end">
                    <div class="review-score">
                      <span class="score">9.2</span>
                      <span class="score-text">Excellent</span>
                      <span class="review-count">234 reviews</span>
                    </div>
                  </div>
                </div>
                <div class="amenities">
                  <span class="badge bg-light text-dark me-2">Pool</span>
                  <span class="badge bg-light text-dark me-2">Spa</span>
                  <span class="badge bg-light text-dark me-2">Restaurant</span>
                  <span class="badge bg-light text-dark">Free WiFi</span>
                </div>
                <div
                  class="mt-3 d-flex justify-content-between align-items-end">
                  <div>
                    <p class="mb-0">
                      <strong>Last booked:</strong> 2 hours ago
                    </p>
                    <small class="text-success">Free cancellation</small>
                  </div>
                  <div class="text-end">
                    <div class="price">
                      <small class="text-muted">per night</small>
                      <h5 class="mb-0">LKR 25,000</h5>
                    </div>
                    <a href="#" class="btn btn-primary mt-2">View Details</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- More hotel cards would go here... -->
      </div>
    </div>
  </div> <?php include 'components/footer.php'; ?>

  <!-- Range Slider JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.0/nouislider.min.js"></script>
  <!-- Custom JS -->
  <script src="assets/js/search.js"></script>
</body>

</html>