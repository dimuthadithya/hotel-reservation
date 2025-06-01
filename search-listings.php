<?php
session_start();


?>

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
          <div class="d-flex justify-content-end gap-2"> <select class="form-select form-select-sm w-auto">
              <option value="recommended">Sort by: Recommended</option>
              <option value="price-low">Price: Low to High</option>
              <option value="price-high">Price: High to Low</option>
              <option value="rating">Guest Rating</option>
            </select>
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
              <input class="form-check-input" type="checkbox" id="property-type-hotel" value="hotel" />
              <label class="form-check-label" for="property-type-hotel">Hotels</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="property-type-villa" value="villa" />
              <label class="form-check-label" for="property-type-villa">Villas</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="property-type-homestay" value="homestay" />
              <label class="form-check-label" for="property-type-homestay">Homestays</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="property-type-resort" value="resort" />
              <label class="form-check-label" for="property-type-resort">Resorts</label>
            </div>
          </div>
          <div class="filter-section">
            <h5 class="filter-title">Star Rating</h5>
            <div class="star-rating-filters">
              <button class="btn btn-outline-secondary btn-sm me-2 mb-2" data-rating="5">
                5 ★
              </button>
              <button class="btn btn-outline-secondary btn-sm me-2 mb-2" data-rating="4">
                4 ★
              </button>
              <button class="btn btn-outline-secondary btn-sm me-2 mb-2" data-rating="3">
                3 ★
              </button>
              <button class="btn btn-outline-secondary btn-sm mb-2" data-rating="2">
                2 ★
              </button>
            </div>
          </div>
          <div class="filter-section">
            <h5 class="filter-title">Popular Amenities</h5>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="amenity-wifi" value="wifi" />
              <label class="form-check-label" for="amenity-wifi">Free WiFi</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="amenity-pool" value="pool" />
              <label class="form-check-label" for="amenity-pool">Swimming Pool</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="amenity-beach" value="beach" />
              <label class="form-check-label" for="amenity-beach">Beach Access</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="amenity-spa" value="spa" />
              <label class="form-check-label" for="amenity-spa">Spa</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="amenity-ayurveda" value="ayurveda" />
              <label class="form-check-label" for="amenity-ayurveda">Ayurveda Center</label>
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
      </div> <!-- Hotel Listings -->
      <div class="col-lg-9">
        <?php
        require_once 'config/db.php';
        $sql = "SELECT h.*, 
                       MIN(rt.base_price) as base_price 
                FROM hotels h
                LEFT JOIN room_types rt ON h.hotel_id = rt.hotel_id AND rt.status = 'active'
                WHERE h.status = 'active'
                GROUP BY h.hotel_id";
        $stmt = $conn->query($sql);
        $hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <div class="results-count mb-4">
          <h4><?= count($hotels) ?> properties found</h4>
        </div>
        <?php
        // Loop through each hotel and display the hotel card
        foreach ($hotels as $hotel) {

          // Fetch real amenities for this hotel
          $amenitiesQuery = "SELECT a.amenity_name 
                           FROM hotel_amenities ha
                           JOIN amenities a ON ha.amenity_id = a.amenity_id
                           WHERE ha.hotel_id = ?
                           ORDER BY a.category";
          $amenitiesStmt = $conn->prepare($amenitiesQuery);
          $amenitiesStmt->execute([$hotel['hotel_id']]);
          $amenities = $amenitiesStmt->fetchAll(PDO::FETCH_COLUMN);
          $hotel['amenities'] = json_encode($amenities);

          include 'components/hotelCard.php';
        }
        ?>
      </div>
    </div>
  </div> <?php include 'components/footer.php'; ?>

  <!-- Range Slider JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.0/nouislider.min.js"></script>
  <!-- Custom JS -->
  <script src="assets/js/search.js"></script>
</body>

</html>