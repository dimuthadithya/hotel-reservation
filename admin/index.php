<?php include_once 'includes/header.php'; ?>
<?php include_once 'includes/sidebar.php'; ?>

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
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-primary">
            <i class="fas fa-hotel"></i>
          </div>
          <div class="stat-details">
            <h3 class="stat-number">24</h3>
            <span class="stat-label">Total Hotels</span>
            <div class="stat-trend positive">
              <i class="fas fa-arrow-up"></i> 12% from last month
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-success">
            <i class="fas fa-calendar-check"></i>
          </div>
          <div class="stat-details">
            <h3 class="stat-number">156</h3>
            <span class="stat-label">Active Bookings</span>
            <div class="stat-trend positive">
              <i class="fas fa-arrow-up"></i> 8% from last week
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-info">
            <i class="fas fa-users"></i>
          </div>
          <div class="stat-details">
            <h3 class="stat-number">1,248</h3>
            <span class="stat-label">Total Users</span>
            <div class="stat-trend positive">
              <i class="fas fa-arrow-up"></i> 24% from last month
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon bg-warning">
            <i class="fas fa-star"></i>
          </div>
          <div class="stat-details">
            <h3 class="stat-number">4.8</h3>
            <span class="stat-label">Avg. Rating</span>
            <div class="stat-trend positive">
              <i class="fas fa-arrow-up"></i> 0.3 from last month
            </div>
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
          <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
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
              <tr>
                <td>John Doe</td>
                <td>Luxury Resort</td>
                <td>2024-02-15</td>
                <td><span class="badge bg-success">Confirmed</span></td>
                <td>$350</td>
              </tr>
              <tr>
                <td>Jane Smith</td>
                <td>City Hotel</td>
                <td>2024-02-16</td>
                <td><span class="badge bg-warning">Pending</span></td>
                <td>$220</td>
              </tr>
              <tr>
                <td>Mike Johnson</td>
                <td>Beach Resort</td>
                <td>2024-02-17</td>
                <td><span class="badge bg-success">Confirmed</span></td>
                <td>$450</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div> <!-- Latest Bookings Section End -->
  </div>

  <!-- Analytics Section -->
  <div class="row mt-4">
    <div class="col-md-6">
      <div class="dashboard-widget">
        <div class="widget-header">
          <h3>Booking Analytics</h3>
          <select class="form-select form-select-sm" style="width: auto;">
            <option>Last 7 days</option>
            <option>Last 30 days</option>
            <option>Last 3 months</option>
          </select>
        </div>
        <div class="analytics-chart" style="height: 300px;">
          <!-- Chart will be rendered here -->
          <div class="d-flex justify-content-center align-items-center h-100">
            <div class="text-muted">Loading chart...</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="dashboard-widget">
        <div class="widget-header">
          <h3>Popular Hotels</h3>
          <select class="form-select form-select-sm" style="width: auto;">
            <option>This Month</option>
            <option>Last Month</option>
            <option>Last 3 Months</option>
          </select>
        </div>
        <div class="popular-hotels">
          <div class="hotel-rank-item">
            <span class="rank">1</span>
            <div class="hotel-info">
              <h4>Luxury Resort</h4>
              <div class="hotel-stats">
                <span><i class="fas fa-bookmark"></i> 145 bookings</span>
                <span><i class="fas fa-star text-warning"></i> 4.8</span>
              </div>
            </div>
            <div class="occupancy-rate">85%</div>
          </div>
          <div class="hotel-rank-item">
            <span class="rank">2</span>
            <div class="hotel-info">
              <h4>City Hotel</h4>
              <div class="hotel-stats">
                <span><i class="fas fa-bookmark"></i> 98 bookings</span>
                <span><i class="fas fa-star text-warning"></i> 4.6</span>
              </div>
            </div>
            <div class="occupancy-rate">72%</div>
          </div>
          <div class="hotel-rank-item">
            <span class="rank">3</span>
            <div class="hotel-info">
              <h4>Beach Resort</h4>
              <div class="hotel-stats">
                <span><i class="fas fa-bookmark"></i> 87 bookings</span>
                <span><i class="fas fa-star text-warning"></i> 4.7</span>
              </div>
            </div>
            <div class="occupancy-rate">68%</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</div>
</div>

<!-- Modals -->
<!-- Add Hotel Modal -->
<div class="modal fade" id="addHotelModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add New Hotel</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="addHotelForm" action="./handlers/add_hotel.php" method="POST" enctype="multipart/form-data">
          <div class="row mb-3">
            <div class="col-md-12">
              <label class="form-label">Hotel Name</label>
              <input type="text" class="form-control" name="hotel_name" required />
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-12">
              <label class="form-label">Description</label>
              <textarea class="form-control" name="description" rows="3" required></textarea>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-12">
              <label class="form-label">Address</label>
              <textarea class="form-control" name="address" rows="2" required></textarea>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">District</label>
              <input type="text" class="form-control" name="district" required />
            </div>
            <div class="col-md-6">
              <label class="form-label">Province</label>
              <input type="text" class="form-control" name="province" required />
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Star Rating</label>
              <select class="form-select" name="star_rating" required>
                <option value="">Select Rating</option>
                <option value="1">1 Star</option>
                <option value="2">2 Stars</option>
                <option value="3">3 Stars</option>
                <option value="4">4 Stars</option>
                <option value="5">5 Stars</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Property Type</label>
              <select class="form-select" name="property_type" required>
                <option value="hotel">Hotel</option>
                <option value="resort">Resort</option>
                <option value="villa">Villa</option>
                <option value="homestay">Homestay</option>
                <option value="guesthouse">Guesthouse</option>
                <option value="boutique">Boutique</option>
              </select>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Contact Phone</label>
              <input type="tel" class="form-control" name="contact_phone" />
            </div>
            <div class="col-md-6">
              <label class="form-label">Contact Email</label>
              <input type="email" class="form-control" name="contact_email" />
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-12">
              <label class="form-label">Website URL</label>
              <input type="url" class="form-control" name="website_url" />
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Total Rooms</label>
              <input type="number" class="form-control" name="total_rooms" value="0" min="0" />
            </div>
            <div class="col-md-6">
              <label class="form-label">Status</label>
              <select class="form-select" name="status" required>
                <option value="pending">Pending</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Hotel Photos</label>
            <input type="file" class="form-control" name="hotel_images[]" multiple accept="image/*" />
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button
          type="button"
          class="btn btn-secondary"
          data-bs-dismiss="modal">
          Cancel
        </button>
        <button type="submit" name="add_hotel" class="btn btn-primary" form="addHotelForm">
          Add Hotel
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Edit Hotel Modal -->
<div class="modal fade" id="editHotelModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Hotel</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="editHotelForm">
          <input type="hidden" id="editHotelId" />
          <div class="mb-3">
            <label class="form-label">Hotel Name</label>
            <input
              type="text"
              class="form-control"
              id="editHotelName"
              required />
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea
              class="form-control"
              id="editHotelDescription"
              rows="3"
              required></textarea>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Location</label>
              <input
                type="text"
                class="form-control"
                id="editHotelLocation"
                required />
            </div>
            <div class="col-md-6">
              <label class="form-label">Category</label>
              <select class="form-select" id="editHotelCategory" required>
                <option value="">Select Category</option>
                <option value="luxury">Luxury</option>
                <option value="business">Business</option>
                <option value="resort">Resort</option>
                <option value="boutique">Boutique</option>
              </select>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Price per Night</label>
              <div class="input-group">
                <span class="input-group-text">$</span>
                <input
                  type="number"
                  class="form-control"
                  id="editHotelPrice"
                  required />
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Room Count</label>
              <input
                type="number"
                class="form-control"
                id="editHotelRooms"
                required />
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Current Photos</label>
            <div class="current-photos row g-2" id="editHotelCurrentPhotos">
              <!-- Current photos will be loaded here -->
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Add New Photos</label>
            <input
              type="file"
              class="form-control"
              multiple
              accept="image/*" />
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select" id="editHotelStatus" required>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
              <option value="maintenance">Under Maintenance</option>
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button
          type="button"
          class="btn btn-secondary"
          data-bs-dismiss="modal">
          Cancel
        </button>
        <button type="submit" class="btn btn-primary" form="editHotelForm">
          Save Changes
        </button>
      </div>
    </div>
  </div>
</div>
<!-- Add Location Modal -->
<div class="modal fade" id="addLocationModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add New Location</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="addLocationForm">
          <div class="mb-3">
            <label class="form-label">Location Name</label>
            <input
              type="text"
              class="form-control"
              name="locationName"
              required />
          </div>
          <div class="mb-3">
            <label class="form-label">District</label>
            <input
              type="text"
              class="form-control"
              name="district"
              required />
          </div>
          <div class="mb-3">
            <label class="form-label">Province</label>
            <input
              type="text"
              class="form-control"
              name="province"
              required />
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea
              class="form-control"
              name="description"
              rows="3"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Location Image</label>
            <input
              type="file"
              class="form-control"
              name="imageUrl"
              accept="image/*" />
          </div>
          <div class="form-check mb-3">
            <input
              type="checkbox"
              class="form-check-input"
              name="isPopular"
              id="isPopular" />
            <label class="form-check-label" for="isPopular">Mark as Popular Location</label>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button
          type="button"
          class="btn btn-secondary"
          data-bs-dismiss="modal">
          Cancel
        </button>
        <button
          type="submit"
          class="btn btn-primary"
          form="addLocationForm">
          Add Location
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Add Room Type Modal -->
<div class="modal fade" id="addRoomTypeModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Room Type</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="addRoomTypeForm">
          <div class="mb-3">
            <label class="form-label">Hotel</label>
            <select class="form-select" name="hotelId" required>
              <option value="">Select Hotel</option>
              <!-- Hotels will be loaded dynamically -->
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Room Type Name</label>
            <input
              type="text"
              class="form-control"
              name="typeName"
              required />
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Base Price</label>
              <div class="input-group">
                <span class="input-group-text">LKR</span>
                <input
                  type="number"
                  class="form-control"
                  name="basePrice"
                  required />
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Max Occupancy</label>
              <input
                type="number"
                class="form-control"
                name="maxOccupancy"
                required />
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Room Size</label>
              <input
                type="text"
                class="form-control"
                name="roomSize"
                placeholder="e.g. 30 sqm" />
            </div>
            <div class="col-md-6">
              <label class="form-label">Bed Type</label>
              <input
                type="text"
                class="form-control"
                name="bedType"
                placeholder="e.g. King Size" />
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Room Type Images</label>
            <input
              type="file"
              class="form-control"
              name="images"
              multiple
              accept="image/*" />
          </div>
          <div class="mb-3">
            <label class="form-label">Room Amenities</label>
            <div class="row" id="roomAmenitiesList">
              <!-- Room amenities checkboxes will be loaded dynamically -->
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button
          type="button"
          class="btn btn-secondary"
          data-bs-dismiss="modal">
          Cancel
        </button>
        <button
          type="submit"
          class="btn btn-primary"
          form="addRoomTypeForm">
          Add Room Type
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Add Room Modal -->
<div class="modal fade" id="addRoomModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Room</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="addRoomForm">
          <div class="mb-3">
            <label class="form-label">Hotel</label>
            <select class="form-select" name="hotelId" required>
              <option value="">Select Hotel</option>
              <!-- Hotels will be loaded dynamically -->
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Room Type</label>
            <select class="form-select" name="roomTypeId" required>
              <option value="">Select Room Type</option>
              <!-- Room types will be loaded dynamically based on hotel selection -->
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Room Number</label>
            <input
              type="text"
              class="form-control"
              name="roomNumber"
              required />
          </div>
          <div class="mb-3">
            <label class="form-label">Floor Number</label>
            <input
              type="number"
              class="form-control"
              name="floorNumber"
              required />
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select" name="status" required>
              <option value="available">Available</option>
              <option value="maintenance">Maintenance</option>
              <option value="out_of_order">Out of Order</option>
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button
          type="button"
          class="btn btn-secondary"
          data-bs-dismiss="modal">
          Cancel
        </button>
        <button type="submit" class="btn btn-primary" form="addRoomForm">
          Add Room
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Add Amenity Modal -->
<div class="modal fade" id="addAmenityModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add New Amenity</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="addAmenityForm">
          <div class="mb-3">
            <label class="form-label">Amenity Name</label>
            <input
              type="text"
              class="form-control"
              name="amenityName"
              required />
          </div>
          <div class="mb-3">
            <label class="form-label">Category</label>
            <select class="form-select" name="category" required>
              <option value="basic">Basic</option>
              <option value="comfort">Comfort</option>
              <option value="business">Business</option>
              <option value="recreation">Recreation</option>
              <option value="accessibility">Accessibility</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Icon Class</label>
            <div class="input-group">
              <span class="input-group-text">fa-</span>
              <input
                type="text"
                class="form-control"
                name="iconClass"
                placeholder="e.g. wifi" />
            </div>
            <div class="form-text">
              Enter a Font Awesome icon name without the 'fa-' prefix
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea
              class="form-control"
              name="description"
              rows="2"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button
          type="button"
          class="btn btn-secondary"
          data-bs-dismiss="modal">
          Cancel
        </button>
        <button type="submit" class="btn btn-primary" form="addAmenityForm">
          Add Amenity
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="js/admin.js"></script>
</body>

</html>