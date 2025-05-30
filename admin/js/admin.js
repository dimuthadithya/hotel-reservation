// Admin Dashboard JavaScript

document.addEventListener('DOMContentLoaded', function () {
  // Initialize tooltips
  const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
  tooltips.forEach((tooltip) => new bootstrap.Tooltip(tooltip));

  // Hotel Management
  initHotelManagement();

  // Booking Management
  initBookingManagement();

  // Review Management
  initReviewManagement();

  // User Management
  initUserManagement();

  // Charts
  initDashboardCharts();
});

// Hotel Management Functions
function initHotelManagement() {
  const hotelsList = document.querySelector('.hotels-list');
  if (!hotelsList) return;

  // Load hotels
  loadHotels();

  // Handle hotel form submission
  const addHotelForm = document.getElementById('addHotelForm');
  if (addHotelForm) {
    addHotelForm.addEventListener('submit', function (e) {
      e.preventDefault();
      // Add hotel logic here
      saveHotel(new FormData(this));
    });
  }
}

function loadHotels() {
  // Fetch hotels from API
  const hotels = [
    // Sample data - replace with actual API call
    {
      id: 1,
      name: 'Luxury Resort Kandy',
      location: 'Kandy, Sri Lanka',
      rating: 4.8,
      status: 'active'
    }
  ];

  const hotelsList = document.querySelector('.hotels-list');
  if (!hotelsList) return;

  hotelsList.innerHTML = hotels
    .map(
      (hotel) => `
        <div class="hotel-item" data-id="${hotel.id}">
            <div class="hotel-details">
                <h3>${hotel.name}</h3>
                <p>${hotel.location}</p>
                <div class="hotel-rating">
                    ${hotel.rating} <i class="fas fa-star text-warning"></i>
                </div>
            </div>
            <div class="hotel-actions">
                <button class="btn btn-sm btn-outline-primary" onclick="editHotel(${hotel.id})">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-outline-danger" onclick="deleteHotel(${hotel.id})">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>
    `
    )
    .join('');
}

function saveHotel(formData) {
  // API call to save hotel
  console.log('Saving hotel...', formData);
}

// Booking Management Functions
function initBookingManagement() {
  loadBookings();

  // Export bookings
  const exportBtn = document.getElementById('exportBookings');
  if (exportBtn) {
    exportBtn.addEventListener('click', exportBookingReport);
  }
}

function loadBookings() {
  // Fetch bookings from API
  const bookings = [
    // Sample data - replace with actual API call
    {
      id: 1,
      guestName: 'John Doe',
      hotelName: 'Luxury Resort Kandy',
      checkIn: '2025-06-01',
      checkOut: '2025-06-05',
      status: 'confirmed'
    }
  ];

  const bookingsList = document.querySelector('.bookings-list');
  if (!bookingsList) return;

  bookingsList.innerHTML = bookings
    .map(
      (booking) => `
        <div class="booking-item" data-id="${booking.id}">
            <div class="booking-details">
                <h4>${booking.guestName}</h4>
                <p>${booking.hotelName}</p>
                <div class="booking-dates">
                    ${booking.checkIn} to ${booking.checkOut}
                </div>
                <span class="badge bg-success">${booking.status}</span>
            </div>
            <div class="booking-actions">
                <button class="btn btn-sm btn-outline-primary" onclick="modifyBooking(${booking.id})">
                    <i class="fas fa-edit"></i> Modify
                </button>
                <button class="btn btn-sm btn-outline-danger" onclick="cancelBooking(${booking.id})">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </div>
        </div>
    `
    )
    .join('');
}

// Review Management Functions
function initReviewManagement() {
  loadReviews();

  const reviewFilter = document.getElementById('reviewFilter');
  if (reviewFilter) {
    reviewFilter.addEventListener('change', function () {
      loadReviews(this.value);
    });
  }
}

function loadReviews(filter = 'all') {
  // Fetch reviews from API
  const reviews = [
    // Sample data - replace with actual API call
    {
      id: 1,
      author: 'Jane Smith',
      hotel: 'Luxury Resort Kandy',
      rating: 5,
      comment: 'Excellent stay, wonderful service!',
      status: 'pending'
    }
  ];

  const reviewsList = document.querySelector('.reviews-list');
  if (!reviewsList) return;

  reviewsList.innerHTML = reviews
    .filter((review) => filter === 'all' || review.status === filter)
    .map(
      (review) => `
            <div class="review-item" data-id="${review.id}">
                <div class="review-details">
                    <div class="review-header">
                        <h4>${review.author}</h4>
                        <div class="review-rating">
                            ${review.rating} <i class="fas fa-star text-warning"></i>
                        </div>
                    </div>
                    <p>${review.comment}</p>
                    <small>${review.hotel}</small>
                </div>
                <div class="review-actions">
                    <button class="btn btn-sm btn-success" onclick="approveReview(${review.id})">
                        <i class="fas fa-check"></i> Approve
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="rejectReview(${review.id})">
                        <i class="fas fa-times"></i> Reject
                    </button>
                </div>
            </div>
        `
    )
    .join('');
}

// User Management Functions
function initUserManagement() {
  loadUsers();

  const userSearch = document.querySelector(
    '.header-actions input[type="search"]'
  );
  if (userSearch) {
    userSearch.addEventListener('input', function () {
      loadUsers(this.value);
    });
  }
}

function loadUsers(search = '') {
  // Fetch users from API
  const users = [
    // Sample data - replace with actual API call
    {
      id: 1,
      name: 'John Doe',
      email: 'john@example.com',
      status: 'active',
      role: 'user'
    }
  ];

  const usersList = document.querySelector('.users-list');
  if (!usersList) return;

  usersList.innerHTML = users
    .filter(
      (user) =>
        search === '' ||
        user.name.toLowerCase().includes(search.toLowerCase()) ||
        user.email.toLowerCase().includes(search.toLowerCase())
    )
    .map(
      (user) => `
            <div class="user-item" data-id="${user.id}">
                <div class="user-details">
                    <h4>${user.name}</h4>
                    <p>${user.email}</p>
                    <span class="badge bg-${
                      user.status === 'active' ? 'success' : 'secondary'
                    }">${user.status}</span>
                </div>
                <div class="user-actions">
                    <button class="btn btn-sm btn-outline-primary" onclick="editUser(${
                      user.id
                    })">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deactivateUser(${
                      user.id
                    })">
                        <i class="fas fa-ban"></i> Deactivate
                    </button>
                </div>
            </div>
        `
    )
    .join('');
}

// Dashboard Charts
function initDashboardCharts() {
  // Bookings Chart
  const bookingsCtx = document.getElementById('bookingsChart');
  if (bookingsCtx) {
    new Chart(bookingsCtx, {
      type: 'line',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [
          {
            label: 'Monthly Bookings',
            data: [65, 59, 80, 81, 56, 55],
            borderColor: '#8E44AD',
            tension: 0.1
          }
        ]
      },
      options: {
        responsive: true
      }
    });
  }

  // Revenue Chart
  const revenueCtx = document.getElementById('revenueChart');
  if (revenueCtx) {
    new Chart(revenueCtx, {
      type: 'bar',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [
          {
            label: 'Monthly Revenue',
            data: [12000, 19000, 15000, 25000, 22000, 30000],
            backgroundColor: '#2ECC71'
          }
        ]
      },
      options: {
        responsive: true
      }
    });
  }
}

// Export Functions
function exportBookingReport() {
  // Implement export logic here
  console.log('Exporting booking report...');
}

// Utility Functions
function showToast(message, type = 'success') {
  // Implement toast notification
  console.log(`${type}: ${message}`);
}

// Event Handlers
document.getElementById('adminLogout')?.addEventListener('click', function (e) {
  e.preventDefault();
  // Implement logout logic
  window.location.href = '../login.html';
});
