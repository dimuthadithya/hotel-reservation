<?php include_once 'includes/header.php'; ?>
<?php include_once 'includes/sidebar.php'; ?>
<?php include_once '../config/db.php'; ?>

<!-- Main Content -->
<div class="admin-main">
  <!-- Welcome Section -->
  <div class="welcome-section mb-4">
    <h1 class="welcome-title">Dashboard Overview</h1>
    <p class="text-muted">Welcome back! Here's what's happening with your hotels today.</p>
  </div>

  <!-- Stats Cards -->
  <div class="dashboard-stats">
    <div class="row g-4">
      <?php
      // Get total hotels
      $hotels_sql = "SELECT COUNT(*) as total FROM hotels";
      $hotels_result = $conn->query($hotels_sql);
      $total_hotels = $hotels_result->fetch(PDO::FETCH_ASSOC)['total'];

      // Get total bookings
      $bookings_sql = "SELECT COUNT(*) as total FROM bookings";
      $bookings_result = $conn->query($bookings_sql);
      $total_bookings = $bookings_result->fetch(PDO::FETCH_ASSOC)['total'];

      // Get total users
      $users_sql = "SELECT COUNT(*) as total FROM users WHERE role = 'user'";
      $users_result = $conn->query($users_sql);
      $total_users = $users_result->fetch(PDO::FETCH_ASSOC)['total'];

      // Get total revenue
      $revenue_sql = "SELECT SUM(amount) as total FROM payments WHERE status = 'completed'";
      $revenue_result = $conn->query($revenue_sql);
      $total_revenue = $revenue_result->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
      ?>
      <div class="col-md-3">
        <div class="stat-card bg-white shadow-sm rounded p-3">
          <div class="stat-icon bg-primary rounded-circle p-3 mb-2">
            <i class="fas fa-hotel text-white"></i>
          </div>
          <div class="stat-details">
            <h3 class="stat-number"><?= $total_hotels ?></h3>
            <p class="stat-label mb-0">Total Hotels</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card bg-white shadow-sm rounded p-3">
          <div class="stat-icon bg-success rounded-circle p-3 mb-2">
            <i class="fas fa-calendar-check text-white"></i>
          </div>
          <div class="stat-details">
            <h3 class="stat-number"><?= $total_bookings ?></h3>
            <p class="stat-label mb-0">Total Bookings</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card bg-white shadow-sm rounded p-3">
          <div class="stat-icon bg-info rounded-circle p-3 mb-2">
            <i class="fas fa-users text-white"></i>
          </div>
          <div class="stat-details">
            <h3 class="stat-number"><?= $total_users ?></h3>
            <p class="stat-label mb-0">Registered Users</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card bg-white shadow-sm rounded p-3">
          <div class="stat-icon bg-warning rounded-circle p-3 mb-2">
            <i class="fas fa-dollar-sign text-white"></i>
          </div>
          <div class="stat-details">
            <h3 class="stat-number">Rs. <?= number_format($total_revenue, 2) ?></h3>
            <p class="stat-label mb-0">Total Revenue</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Dashboard Widgets -->
  <div class="row mt-4">
    <!-- Recent Bookings -->
    <div class="col-md-8">
      <div class="dashboard-widget">
        <div class="widget-header">
          <h3>Recent Bookings</h3>
          <a href="bookings.php" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Guest</th>
                <th>Hotel</th>
                <th>Check-in</th>
                <th>Status</th>
                <th>Amount</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $recent_bookings_sql = "
                SELECT b.*, u.first_name, u.last_name, h.hotel_name, p.amount, p.status as payment_status
                FROM bookings b
                JOIN users u ON b.user_id = u.user_id
                JOIN hotels h ON b.hotel_id = h.hotel_id
                LEFT JOIN payments p ON b.booking_id = p.booking_id
                ORDER BY b.created_at DESC
                LIMIT 5
              ";
              $bookings_stmt = $conn->query($recent_bookings_sql);
              $recent_bookings = $bookings_stmt->fetchAll(PDO::FETCH_ASSOC);

              foreach ($recent_bookings as $booking): ?>
                <tr>
                  <td><?= htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']) ?></td>
                  <td><?= htmlspecialchars($booking['hotel_name']) ?></td>
                  <td><?= date('M d, Y', strtotime($booking['check_in_date'])) ?></td>
                  <td>
                    <span class="badge bg-<?= $booking['payment_status'] === 'paid' ? 'success' : ($booking['payment_status'] === 'pending' ? 'warning' : 'danger') ?>">
                      <?= ucfirst($booking['payment_status']) ?>
                    </span>
                  </td>
                  <td>Rs. <?= number_format($booking['amount'], 2) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="dashboard-widget">
        <div class="widget-header">
          <h3>Popular Hotels</h3>
        </div>
        <div class="popular-hotels">
          <?php
          $popular_hotels_sql = "
            SELECT h.hotel_id, h.hotel_name, 
                   COUNT(b.booking_id) as booking_count
            FROM hotels h
            LEFT JOIN bookings b ON h.hotel_id = b.hotel_id
            GROUP BY h.hotel_id
            ORDER BY booking_count DESC
            LIMIT 3
          ";
          $popular_hotels_stmt = $conn->query($popular_hotels_sql);
          $popular_hotels = $popular_hotels_stmt->fetchAll(PDO::FETCH_ASSOC);

          foreach ($popular_hotels as $index => $hotel):
            $occupancy_sql = "
              SELECT COUNT(*) as booked_rooms 
              FROM bookings 
              WHERE hotel_id = :hotel_id 
              AND check_in_date <= CURRENT_DATE 
              AND check_out_date > CURRENT_DATE
            ";
            $occupancy_stmt = $conn->prepare($occupancy_sql);
            $occupancy_stmt->execute([':hotel_id' => $hotel['hotel_id']]);
            $booked_rooms = $occupancy_stmt->fetch(PDO::FETCH_ASSOC)['booked_rooms'];

            // Get total rooms
            $rooms_sql = "SELECT COUNT(*) as total_rooms FROM rooms WHERE hotel_id = :hotel_id";
            $rooms_stmt = $conn->prepare($rooms_sql);
            $rooms_stmt->execute([':hotel_id' => $hotel['hotel_id']]);
            $total_rooms = $rooms_stmt->fetch(PDO::FETCH_ASSOC)['total_rooms'];

            $occupancy_rate = $total_rooms > 0 ? ($booked_rooms / $total_rooms) * 100 : 0;
          ?>
            <div class="hotel-rank-item p-3 border-bottom">
              <span class="rank"><?= $index + 1 ?></span>
              <div class="hotel-info">
                <h5 class="mb-1"><?= htmlspecialchars($hotel['hotel_name']) ?></h5>
                <div class="hotel-stats">
                  <span><i class="fas fa-bookmark"></i> <?= $hotel['booking_count'] ?> bookings</span>
                </div>
              </div>
              <div class="occupancy-rate"><?= number_format($occupancy_rate, 0) ?>%</div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Analytics Section -->
  <div class="row mt-4">
    <div class="col-md-12">
      <div class="dashboard-widget">
        <div class="widget-header">
          <h3>Booking Analytics</h3>
        </div>
        <div class="analytics-chart p-3" style="height: 300px;">
          <canvas id="bookingChart"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .stat-card {
    transition: transform 0.2s;
  }

  .stat-card:hover {
    transform: translateY(-5px);
  }

  .hotel-rank-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid #eee;
  }

  .rank {
    font-size: 1.5rem;
    font-weight: bold;
    margin-right: 1rem;
    color: #6c757d;
  }

  .hotel-info {
    flex-grow: 1;
  }

  .hotel-stats {
    display: flex;
    gap: 1rem;
    font-size: 0.875rem;
    color: #6c757d;
  }

  .occupancy-rate {
    font-weight: bold;
    color: #198754;
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    <?php
    // Get booking data for the last 7 days
    $chart_data_sql = "
        SELECT DATE(created_at) as date, COUNT(*) as count
        FROM bookings
        WHERE created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)
        GROUP BY DATE(created_at)
        ORDER BY date
    ";
    $chart_data_stmt = $conn->query($chart_data_sql);
    $chart_data = $chart_data_stmt->fetchAll(PDO::FETCH_ASSOC);

    $labels = [];
    $data = [];
    for ($i = 6; $i >= 0; $i--) {
      $date = date('Y-m-d', strtotime("-$i days"));
      $count = 0;
      foreach ($chart_data as $row) {
        if ($row['date'] === $date) {
          $count = $row['count'];
          break;
        }
      }
      $labels[] = date('M d', strtotime($date));
      $data[] = $count;
    }
    ?>

    const ctx = document.getElementById('bookingChart').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
          label: 'Daily Bookings',
          data: <?= json_encode($data) ?>,
          borderColor: '#0d6efd',
          tension: 0.1,
          fill: false
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1
            }
          }
        },
        plugins: {
          legend: {
            display: false
          }
        }
      }
    });
  });
</script>

<!-- Add dependency scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="js/admin.js"></script>

<?php include_once 'includes/footer.php'; ?>