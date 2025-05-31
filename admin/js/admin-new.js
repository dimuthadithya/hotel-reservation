// Core Admin JS functionality
document.addEventListener('DOMContentLoaded', function () {
  // Toast notification function
  function showToast(message, type = 'success') {
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
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

    const container = document.createElement('div');
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.appendChild(toast);
    document.body.appendChild(container);

    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();

    toast.addEventListener('hidden.bs.toast', () => {
      container.remove();
    });
  }

  // Form submission handlers
  const forms = {
    addHotelForm: submitAddHotelForm,
    editHotelForm: submitEditHotelForm,
    addLocationForm: submitAddLocationForm,
    addRoomTypeForm: submitAddRoomTypeForm,
    addRoomForm: submitAddRoomForm,
    addAmenityForm: submitAddAmenityForm
  };

  // Global form submission handler
  document.addEventListener('submit', async function (e) {
    const form = e.target;
    if (forms[form.id]) {
      e.preventDefault();
      await forms[form.id](form);
    }
  });

  // Add Hotel Form Submission
  async function submitAddHotelForm(form) {
    try {
      const formData = new FormData(form);
      const response = await fetch(form.action, {
        method: 'POST',
        body: formData
      });

      const data = await response.json();

      if (data.success) {
        showToast(data.message || 'Hotel added successfully', 'success');
        const modal = bootstrap.Modal.getInstance(
          document.getElementById('addHotelModal')
        );
        modal.hide();
        loadCurrentPageData();
      } else {
        showToast(data.message || 'An error occurred', 'danger');
      }
    } catch (error) {
      console.error('Form submission error:', error);
      showToast('An error occurred while processing your request', 'danger');
    }
  }

  // Edit Hotel Form Submission
  async function submitEditHotelForm(form) {
    try {
      const formData = new FormData(form);
      const response = await fetch('./handlers/edit_hotel.php', {
        method: 'POST',
        body: formData
      });

      const data = await response.json();

      if (data.success) {
        showToast(data.message || 'Hotel updated successfully', 'success');
        const modal = bootstrap.Modal.getInstance(
          document.getElementById('editHotelModal')
        );
        modal.hide();
        loadCurrentPageData();
      } else {
        showToast(data.message || 'An error occurred', 'danger');
      }
    } catch (error) {
      console.error('Form submission error:', error);
      showToast('An error occurred while processing your request', 'danger');
    }
  }

  // Delete item handler
  document.addEventListener('click', async function (e) {
    if (e.target.matches('[data-delete]')) {
      e.preventDefault();

      if (!confirm('Are you sure you want to delete this item?')) {
        return;
      }

      const url = e.target.href || e.target.dataset.url;
      try {
        const response = await fetch(url, {
          method: 'POST'
        });
        const data = await response.json();

        if (data.success) {
          showToast(data.message || 'Item deleted successfully', 'success');
          loadCurrentPageData();
        } else {
          showToast(data.message || 'An error occurred', 'danger');
        }
      } catch (error) {
        console.error('Delete error:', error);
        showToast('An error occurred while processing your request', 'danger');
      }
    }
  });

  // Function to load data for current page
  function loadCurrentPageData() {
    const currentPage = window.location.pathname
      .split('/')
      .pop()
      .replace('.php', '');
    const dataEndpoint = `./handlers/get_admin_data.php?type=${currentPage}`;

    fetch(dataEndpoint)
      .then((response) => response.json())
      .then((data) => {
        updatePageContent(currentPage, data);
      })
      .catch((error) => {
        console.error('Error loading data:', error);
        showToast('Error loading data', 'danger');
      });
  }

  // Function to update page content
  function updatePageContent(page, data) {
    if (!data) return;

    switch (page) {
      case 'index':
        updateDashboardStats(data);
        break;
      case 'hotels':
        renderHotels(data);
        break;
      case 'bookings':
        renderBookings(data);
        break;
      case 'reviews':
        renderReviews(data);
        break;
      case 'users':
        renderUsers(data);
        break;
    }
  }

  // Render functions for each page type
  function updateDashboardStats(data) {
    document.querySelector('.stat-number:nth-child(1)').textContent =
      data.hotels;
    document.querySelector('.stat-number:nth-child(2)').textContent =
      data.bookings;
    document.querySelector('.stat-number:nth-child(3)').textContent =
      data.users;
    document.querySelector('.stat-number:nth-child(4)').textContent =
      data.avgRating;
  }

  function renderHotels(hotels) {
    const container = document.querySelector('.hotels-list');
    if (!container || !hotels.length) return;

    container.innerHTML = `
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Hotel Name</th>
                            <th>Location</th>
                            <th>Rooms</th>
                            <th>Rating</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${hotels
                          .map(
                            (hotel) => `
                            <tr>
                                <td>${hotel.name}</td>
                                <td>${hotel.district}, ${hotel.province}</td>
                                <td>${hotel.room_count}</td>
                                <td>${
                                  hotel.avg_rating
                                    ? hotel.avg_rating.toFixed(1)
                                    : 'N/A'
                                }</td>
                                <td><span class="badge bg-${getStatusColor(
                                  hotel.status
                                )}">${hotel.status}</span></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="editHotel(${
                                      hotel.id
                                    })">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" data-delete data-url="./handlers/delete_hotel.php?id=${
                                      hotel.id
                                    }">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `
                          )
                          .join('')}
                    </tbody>
                </table>
            </div>
        `;
  }

  function renderBookings(bookings) {
    const container = document.querySelector('.bookings-list');
    if (!container || !bookings.length) return;

    container.innerHTML = `
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Guest</th>
                            <th>Hotel</th>
                            <th>Room</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${bookings
                          .map(
                            (booking) => `
                            <tr>
                                <td>${booking.id}</td>
                                <td>${booking.first_name} ${
                              booking.last_name
                            }<br>${booking.email}</td>
                                <td>${booking.hotel_name}</td>
                                <td>${booking.room_number}</td>
                                <td>${booking.check_in_date}</td>
                                <td>${booking.check_out_date}</td>
                                <td><span class="badge bg-${getStatusColor(
                                  booking.status
                                )}">${booking.status}</span></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="viewBooking(${
                                      booking.id
                                    })">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        `
                          )
                          .join('')}
                    </tbody>
                </table>
            </div>
        `;
  }

  function renderReviews(reviews) {
    const container = document.querySelector('.reviews-list');
    if (!container || !reviews.length) return;

    container.innerHTML = `
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Hotel</th>
                            <th>Guest</th>
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${reviews
                          .map(
                            (review) => `
                            <tr>
                                <td>${review.hotel_name}</td>
                                <td>${review.first_name} ${
                              review.last_name
                            }</td>
                                <td>${'★'.repeat(review.rating)}${'☆'.repeat(
                              5 - review.rating
                            )}</td>
                                <td>${review.comment}</td>
                                <td><span class="badge bg-${getStatusColor(
                                  review.status
                                )}">${review.status}</span></td>
                                <td>
                                    ${
                                      review.status === 'pending'
                                        ? `
                                        <button class="btn btn-sm btn-success" onclick="approveReview(${review.id})">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="rejectReview(${review.id})">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    `
                                        : ''
                                    }
                                </td>
                            </tr>
                        `
                          )
                          .join('')}
                    </tbody>
                </table>
            </div>
        `;
  }

  function renderUsers(users) {
    const container = document.querySelector('.users-list');
    if (!container || !users.length) return;

    container.innerHTML = `
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Bookings</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${users
                          .map(
                            (user) => `
                            <tr>
                                <td>${user.first_name} ${user.last_name}</td>
                                <td>${user.email}</td>
                                <td>${user.role}</td>
                                <td><span class="badge bg-${getStatusColor(
                                  user.status
                                )}">${user.status}</span></td>
                                <td>${user.booking_count}</td>
                                <td>${new Date(
                                  user.created_at
                                ).toLocaleDateString()}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="editUser(${
                                      user.id
                                    })">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    ${
                                      user.role !== 'admin'
                                        ? `
                                        <button class="btn btn-sm btn-danger" data-delete data-url="./handlers/delete_user.php?id=${user.id}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    `
                                        : ''
                                    }
                                </td>
                            </tr>
                        `
                          )
                          .join('')}
                    </tbody>
                </table>
            </div>
        `;
  }

  // Helper function to get status color
  function getStatusColor(status) {
    const colors = {
      active: 'success',
      pending: 'warning',
      inactive: 'secondary',
      confirmed: 'success',
      cancelled: 'danger',
      completed: 'info',
      approved: 'success',
      rejected: 'danger'
    };
    return colors[status.toLowerCase()] || 'secondary';
  }

  // Export functionality
  const exportButtons = document.querySelectorAll('[id$="export"]');
  exportButtons.forEach((button) => {
    button.addEventListener('click', handleExport);
  });

  function handleExport(e) {
    const type = e.target.id.replace('export', '').toLowerCase();
    window.location.href = `./handlers/export_${type}.php`;
  }

  // Initial data load
  loadCurrentPageData();
});
