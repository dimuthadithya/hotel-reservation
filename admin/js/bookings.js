// Bootstrap alert helper function
function showBootstrapAlert(type, title, message) {
  return `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <strong>${title}</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
}

// View booking details
function viewBooking(bookingId) {
  fetch(`handlers/get_booking.php?id=${bookingId}`)
    .then((response) => response.json())
    .then((data) => {
      if (data.status === 'success') {
        const booking = data.booking;
        const checkInDate = new Date(booking.check_in_date).toLocaleDateString(
          'en-US',
          {
            month: 'long',
            day: 'numeric',
            year: 'numeric'
          }
        );
        const checkOutDate = new Date(
          booking.check_out_date
        ).toLocaleDateString('en-US', {
          month: 'long',
          day: 'numeric',
          year: 'numeric'
        });

        // Format details in a clean layout
        document.getElementById('bookingDetails').innerHTML = `
                    <div class="booking-details">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted">Booking Information</h6>
                                <p class="mb-1"><strong>Reference:</strong> ${
                                  booking.booking_reference
                                }</p>
                                <p class="mb-1"><strong>Status:</strong> <span class="badge bg-${getStatusClass(
                                  booking.booking_status
                                )}">${booking.booking_status.toUpperCase()}</span></p>
                                <p class="mb-1"><strong>Created:</strong> ${new Date(
                                  booking.created_at
                                ).toLocaleString()}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Hotel Details</h6>
                                <p class="mb-1"><strong>Hotel:</strong> ${
                                  booking.hotel_name
                                }</p>
                                <p class="mb-1"><strong>Room:</strong> ${
                                  booking.room_number
                                } (${booking.room_type})</p>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted">Guest Information</h6>
                                <p class="mb-1"><strong>Name:</strong> ${
                                  booking.guest_name
                                }</p>
                                <p class="mb-1"><strong>Email:</strong> ${
                                  booking.guest_email
                                }</p>
                                <p class="mb-1"><strong>Phone:</strong> ${
                                  booking.guest_phone
                                }</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Stay Details</h6>
                                <p class="mb-1"><strong>Check In:</strong> ${checkInDate}</p>
                                <p class="mb-1"><strong>Check Out:</strong> ${checkOutDate}</p>
                                <p class="mb-1"><strong>Guests:</strong> ${
                                  booking.adults
                                } Adults${
          booking.children > 0 ? `, ${booking.children} Children` : ''
        }</p>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted">Payment Details</h6>
                                <p class="mb-1"><strong>Total Amount:</strong> LKR ${parseFloat(
                                  booking.total_amount
                                ).toLocaleString('en-US', {
                                  minimumFractionDigits: 2
                                })}</p>
                                <p class="mb-1"><strong>Room Rate:</strong> LKR ${parseFloat(
                                  booking.room_rate
                                ).toLocaleString('en-US', {
                                  minimumFractionDigits: 2
                                })}/night</p>
                                <p class="mb-1"><strong>Total Nights:</strong> ${
                                  booking.total_nights
                                }</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Additional Information</h6>
                                <p class="mb-1"><strong>Special Requests:</strong></p>
                                <p class="text-muted">${
                                  booking.special_requests || 'None'
                                }</p>
                            </div>
                        </div>
                    </div>
                `;

        // Show the modal
        const modal = new bootstrap.Modal(
          document.getElementById('viewBookingModal')
        );
        modal.show();
      } else {
        alert('Error loading booking details');
      }
    })
    .catch((error) => {
      console.error('Error:', error);
      alert('Error loading booking details');
    });
}

// Helper function to get status badge class
function getStatusClass(status) {
  return (
    {
      confirmed: 'success',
      pending: 'warning',
      cancelled: 'danger',
      checked_in: 'info',
      checked_out: 'secondary'
    }[status] || 'secondary'
  );
}

// Initialize any necessary functionality
document.addEventListener('DOMContentLoaded', function () {
  // Any future initialization can go here
});
