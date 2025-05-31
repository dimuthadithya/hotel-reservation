// Admin Dashboard JavaScript

document.addEventListener('DOMContentLoaded', function () {
  // Initialize tooltips
  const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
  tooltips.forEach((tooltip) => new bootstrap.Tooltip(tooltip));

  // Initialize tab functionality
  document.querySelectorAll('a[data-bs-toggle="tab"]').forEach((tab) => {
    tab.addEventListener('click', function (e) {
      e.preventDefault();
      const target = this.getAttribute('href');
      const tabContent = document.querySelector(target);

      // Remove active class from all tabs and content
      document
        .querySelectorAll('.tab-pane')
        .forEach((pane) => pane.classList.remove('show', 'active'));
      document
        .querySelectorAll('.nav-item')
        .forEach((item) => item.classList.remove('active'));

      // Add active class to clicked tab and content
      tabContent.classList.add('show', 'active');
      this.parentElement.classList.add('active');

      // Load content based on tab
      if (target === '#hotels') {
        loadHotels();
      } else if (target === '#bookings') {
        loadBookings();
      } else if (target === '#reviews') {
        loadReviews();
      } else if (target === '#users') {
        loadUsers();
      }
    });
  });

  // Initialize components
  initHotelManagement();
  initBookingManagement();
  initReviewManagement();
  initUserManagement();
});

// Hotel Management Functions
function initHotelManagement() {
  const hotelsList = document.querySelector('.hotels-list');
  if (!hotelsList) return;

  // Load hotels
  loadHotels();

  // Handle add hotel form submission
  const addHotelForm = document.getElementById('addHotelForm');
  if (addHotelForm) {
    addHotelForm.addEventListener('submit', function (e) {
      e.preventDefault();
      saveHotel(new FormData(this));
      // Close modal after submission
      const modal = bootstrap.Modal.getInstance(
        document.getElementById('addHotelModal')
      );
      modal.hide();
    });
  }

  // Handle edit hotel form submission
  const editHotelForm = document.getElementById('editHotelForm');
  if (editHotelForm) {
    editHotelForm.addEventListener('submit', function (e) {
      e.preventDefault();
      const formData = new FormData(this);
      // Add the hotel ID to the form data
      formData.append('hotelId', document.getElementById('editHotelId').value);
      saveHotel(formData);
      // Close modal after submission
      const modal = bootstrap.Modal.getInstance(
        document.getElementById('editHotelModal')
      );
      modal.hide();
    });
  }
}

function loadHotels() {
  // Sample data - in production, this would come from an API
  const hotels = [
    {
      id: 1,
      name: 'Luxury Resort Kandy',
      location: 'Kandy, Sri Lanka',
      rating: 4.8,
      status: 'active',
      price: '$200',
      imageUrl: '../assets/img/luxury-suite.jpg',
      amenities: ['WiFi', 'Pool', 'Spa', 'Restaurant']
    },
    {
      id: 2,
      name: 'Beach Villa Resort',
      location: 'Galle, Sri Lanka',
      rating: 4.6,
      status: 'active',
      price: '$180',
      imageUrl: '../assets/img/beach-villa.jpg',
      amenities: ['Beach Access', 'Pool', 'Restaurant']
    },
    {
      id: 3,
      name: 'Rustic Cabin Retreat',
      location: 'Nuwara Eliya, Sri Lanka',
      rating: 4.5,
      status: 'active',
      price: '$150',
      imageUrl: '../assets/img/rustic-cabin.jpg',
      amenities: ['Mountain View', 'Fireplace', 'Restaurant']
    }
  ];

  const hotelsList = document.querySelector('.hotels-list');
  if (!hotelsList) return;

  hotelsList.innerHTML = hotels
    .map(
      (hotel) => `
        <div class="hotel-item">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <img src="${hotel.imageUrl}" alt="${
        hotel.name
      }" class="hotel-thumbnail img-fluid rounded" />
                </div>
                <div class="col-md-6">
                    <div class="hotel-details">
                        <h4 class="hotel-name">${hotel.name}</h4>
                        <p class="hotel-location"><i class="fas fa-map-marker-alt text-primary"></i> ${
                          hotel.location
                        }</p>
                        <div class="hotel-rating">
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-star"></i> ${hotel.rating}
                            </span>
                            <span class="badge bg-${
                              hotel.status === 'active' ? 'success' : 'danger'
                            }">
                                ${hotel.status}
                            </span>
                        </div>
                        <div class="hotel-amenities mt-2">
                            ${hotel.amenities
                              .map(
                                (amenity) =>
                                  `<span class="badge bg-light text-dark me-1">${amenity}</span>`
                              )
                              .join('')}
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="hotel-price text-center">
                        <h5>${hotel.price}</h5>
                        <small>per night</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="hotel-actions d-flex flex-column gap-2">
                        <button class="btn btn-sm btn-outline-primary" onclick="editHotel(${
                          hotel.id
                        })">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteHotel(${
                          hotel.id
                        })">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                        <button class="btn btn-sm btn-outline-success" onclick="manageRooms(${
                          hotel.id
                        })">
                            <i class="fas fa-door-open"></i> Rooms
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `
    )
    .join('');
}

function editHotel(hotelId) {
  // Find the hotel data from our sample data
  const hotels = [
    {
      id: 1,
      name: 'Luxury Resort Kandy',
      description: 'Luxury resort with stunning views of Kandy Lake',
      location: 'Kandy, Sri Lanka',
      category: 'luxury',
      rating: 4.8,
      status: 'active',
      price: '200',
      rooms: 50,
      imageUrl: '../assets/img/luxury-suite.jpg',
      amenities: ['WiFi', 'Pool', 'Spa', 'Restaurant']
    },
    {
      id: 2,
      name: 'Beach Villa Resort',
      description: 'Beautiful beachfront resort in historic Galle',
      location: 'Galle, Sri Lanka',
      category: 'resort',
      rating: 4.6,
      status: 'active',
      price: '180',
      rooms: 35,
      imageUrl: '../assets/img/beach-villa.jpg',
      amenities: ['Beach Access', 'Pool', 'Restaurant']
    }
  ];

  const hotel = hotels.find((h) => h.id === hotelId);
  if (!hotel) {
    showToast('Hotel not found', 'error');
    return;
  }

  // Populate the form fields
  document.getElementById('editHotelId').value = hotel.id;
  document.getElementById('editHotelName').value = hotel.name;
  document.getElementById('editHotelDescription').value = hotel.description;
  document.getElementById('editHotelLocation').value = hotel.location;
  document.getElementById('editHotelCategory').value = hotel.category;
  document.getElementById('editHotelPrice').value = hotel.price;
  document.getElementById('editHotelRooms').value = hotel.rooms;
  document.getElementById('editHotelStatus').value = hotel.status;

  // Show current photos (simplified version)
  const currentPhotosContainer = document.getElementById(
    'editHotelCurrentPhotos'
  );
  currentPhotosContainer.innerHTML = `
    <div class="col-4">
      <img src="${hotel.imageUrl}" class="img-fluid rounded" />
    </div>
  `;

  // Show the modal
  const editModal = new bootstrap.Modal(
    document.getElementById('editHotelModal')
  );
  editModal.show();
}

function deleteHotel(hotelId) {
  if (confirm('Are you sure you want to delete this hotel?')) {
    // Implement hotel deletion logic
    showToast('Hotel deleted successfully!', 'success');
    loadHotels(); // Reload the hotels list
  }
}

function manageRooms(hotelId) {
  // Implement room management logic
  showToast('Opening room management...', 'info');
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
  const toastContainer = document.createElement('div');
  toastContainer.className = 'position-fixed bottom-0 end-0 p-3';
  toastContainer.style.zIndex = '9999';

  const toast = document.createElement('div');
  toast.className = `toast align-items-center text-white bg-${type} border-0`;
  toast.setAttribute('role', 'alert');
  toast.setAttribute('aria-live', 'assertive');
  toast.setAttribute('aria-atomic', 'true');

  toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;

  toastContainer.appendChild(toast);
  document.body.appendChild(toastContainer);

  const bsToast = new bootstrap.Toast(toast);
  bsToast.show();

  // Remove the toast after it's hidden
  toast.addEventListener('hidden.bs.toast', () => {
    toastContainer.remove();
  });
}

// Event Handlers
document.getElementById('adminLogout')?.addEventListener('click', function (e) {
  e.preventDefault();
  // Implement logout logic
  window.location.href = '../login.html';
});
