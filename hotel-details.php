<?php
session_start();
require_once 'config/db.php';
require_once 'includes/utility_functions.php';

// Get hotel details
$hotel_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$hotel_id) {
  header('Location: index.php');
  exit;
}

$sql = "SELECT * FROM hotels WHERE hotel_id = :hotel_id AND status = 'active'";
$stmt = $conn->prepare($sql);
$stmt->execute(['hotel_id' => $hotel_id]);
$hotel = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$hotel) {
  header('Location: index.php');
  exit;
}

// Add dummy amenities based on star rating (temporary until amenities table is ready)
$dummyAmenities = ['Free WiFi', 'Parking', 'Restaurant', 'Air Conditioning'];
if ($hotel['star_rating'] >= 4) {
  $dummyAmenities[] = 'Swimming Pool';
  $dummyAmenities[] = 'Spa';
}
if ($hotel['star_rating'] >= 5) {
  $dummyAmenities[] = 'Fitness Center';
  $dummyAmenities[] = 'Room Service';
}
$hotel['amenities'] = $dummyAmenities;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($hotel['hotel_name']) ?> - Pearl Stay</title>
  <!-- Favicon -->
  <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon_io/apple-touch-icon.png" />
  <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon_io/favicon-32x32.png" />
  <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon_io/favicon-16x16.png" />
  <link rel="manifest" href="assets/favicon_io/site.webmanifest" />
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  <!-- Custom CSS -->
  <link href="assets/css/styles.css" rel="stylesheet" />
  <link href="assets/css/nav.css" rel="stylesheet" />
  <link href="assets/css/footer.css" rel="stylesheet" />
  <link href="assets/css/hotel-details.css" rel="stylesheet" />
  <!-- Favicon -->
</head>

<body>
  <?php include 'components/nav.php'; ?>
  <!-- Gallery Section -->
  <section class="gallery-section">
    <div class="main-gallery">
      <img src="<?= $hotel['main_image'] ? 'uploads/img/hotels/' . $hotel['hotel_id'] . '/' . $hotel['main_image'] : 'assets/img/luxury-suite.jpg' ?>"
        alt="<?= htmlspecialchars($hotel['hotel_name']) ?>" />
    </div>

  </section>

  <!-- Hotel Info Section -->
  <section class="hotel-info">
    <div class="container">
      <div class="row">
        <div class="col-lg-8">
          <div class="hotel-title">
            <h1 class="hotel-name"><?= htmlspecialchars($hotel['hotel_name']) ?></h1>
            <p class="hotel-location">
              <i class="fas fa-map-marker-alt me-2"></i>
              <?= htmlspecialchars($hotel['address']) ?>
              <?php if ($hotel['district']): ?>
                <br><small class="text-muted"><?= htmlspecialchars($hotel['district']) ?>, <?= htmlspecialchars($hotel['province']) ?></small>
              <?php endif; ?>
            </p>
          </div>
          <div class="hotel-description mb-4">
            <h2 class="h4 mb-3">About This Property</h2>
            <?php if ($hotel['description']): ?>
              <?= nl2br(htmlspecialchars($hotel['description'])) ?>
            <?php else: ?>
              <p>Experience luxury and comfort at our premier hotel. Our dedicated staff ensures a memorable stay with world-class amenities and impeccable service.</p>
            <?php endif; ?>
          </div>
          <div class="amenities-section mb-4">
            <h2 class="h4 mb-3">Property Amenities</h2>
            <?php
            // Get hotel amenities with icons
            $amenitiesQuery = "SELECT a.amenity_name, a.icon_class, a.category 
                FROM hotel_amenities ha
                JOIN amenities a ON ha.amenity_id = a.amenity_id
                WHERE ha.hotel_id = :hotel_id
                ORDER BY a.category";
            $amenitiesStmt = $conn->prepare($amenitiesQuery);
            $amenitiesStmt->execute(['hotel_id' => $hotel_id]);
            $hotelAmenities = $amenitiesStmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <div class="amenities-list">
              <?php foreach ($hotelAmenities as $amenity): ?>
                <div class="amenity-item">
                  <i class="fas <?= htmlspecialchars($amenity['icon_class']) ?> amenity-icon"></i>
                  <span><?= htmlspecialchars($amenity['amenity_name']) ?></span>
                </div>
              <?php endforeach; ?>
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
          <?php          // Get room types for this hotel with availability info
          $roomTypesQuery = "SELECT rt.*, 
              COUNT(CASE WHEN r.status = 'available' THEN 1 END) as available_rooms
              FROM room_types rt
              LEFT JOIN rooms r ON rt.room_type_id = r.room_type_id
              WHERE rt.hotel_id = :hotel_id 
              AND rt.status = 'active'
              GROUP BY rt.room_type_id
              ORDER BY rt.base_price";

          $roomTypesStmt = $conn->prepare($roomTypesQuery);
          $roomTypesStmt->execute(['hotel_id' => $hotel_id]);
          $roomTypes = $roomTypesStmt->fetchAll(PDO::FETCH_ASSOC);
          foreach ($roomTypes as $roomType):
            $roomImages = json_decode($roomType['images'], true);
            $mainImage = !empty($roomImages) ? $roomImages[0] : 'assets/img/luxury-suite.jpg';
            $amenities = json_decode($roomType['room_amenities'], true) ?? [];
          ?>
            <div class="room-card-detailed">
              <div class="row g-0">
                <div class="col-md-4">
                  <div class="room-image">
                    <img src="<?= htmlspecialchars($mainImage) ?>" alt="<?= htmlspecialchars($roomType['type_name']) ?>" />
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="room-details">
                    <h3 class="room-type"><?= htmlspecialchars($roomType['type_name']) ?></h3>
                    <div class="room-features">
                      <?php if ($roomType['room_size']): ?>
                        <span class="feature-badge"><i class="fas fa-ruler-combined me-2"></i><?= htmlspecialchars($roomType['room_size']) ?></span>
                      <?php endif; ?>
                      <span class="feature-badge"><i class="fas fa-bed me-2"></i><?= htmlspecialchars($roomType['bed_type']) ?></span>
                      <span class="feature-badge"><i class="fas fa-user-friends me-2"></i>Max <?= htmlspecialchars($roomType['max_occupancy']) ?> guests</span>
                    </div>
                    <p class="room-description">
                      <?= nl2br(htmlspecialchars($roomType['description'])) ?>
                    </p>
                    <?php if (!empty($amenities)): ?>
                      <div class="room-amenities mb-3">
                        <?php foreach ($amenities as $amenity): ?>
                          <span class="badge bg-light text-dark me-2"><?= htmlspecialchars(trim($amenity)) ?></span>
                        <?php endforeach; ?>
                      </div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                      <div>
                        <div class="room-price">
                          LKR <?= number_format($roomType['base_price'], 2) ?> <span class="price-period">/night</span>
                        </div>
                        <div class="room-availability text-<?= $roomType['available_rooms'] > 0 ? 'success' : 'danger' ?>">
                          <i class="fas fa-<?= $roomType['available_rooms'] > 0 ? 'check' : 'times' ?>-circle me-1"></i>
                          <?php if ($roomType['available_rooms'] > 0): ?>
                            <?= $roomType['available_rooms'] ?> rooms available
                          <?php else: ?>
                            No rooms available
                          <?php endif; ?>
                        </div>
                      </div> <a href="checkout.php?room_type=<?= $roomType['room_type_id'] ?>"
                        class="btn btn-primary select-room-btn"
                        <?= $roomType['available_rooms'] == 0 ? 'disabled' : '' ?>>
                        Select Room
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
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