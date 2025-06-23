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
    rel="stylesheet" /> <!-- Custom CSS -->
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
      <h2 class="mb-0">Available Properties</h2>
    </div>
  </section>

  <!-- Main Content -->
  <div class="container py-4">
    <div class="row">
      <div class="col-12">
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
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
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
    </div>
  </div>
  <?php include 'components/footer.php'; ?>
  <!-- Custom JS -->
  <script src="assets/js/search.js"></script>
</body>

</html>