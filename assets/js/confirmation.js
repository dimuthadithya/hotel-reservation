document.addEventListener('DOMContentLoaded', function () {
  // Initialize Add to Calendar button
  const addToCalendarBtn = document.getElementById('addToCalendarBtn');
  if (addToCalendarBtn) {
    addToCalendarBtn.addEventListener('click', function () {
      // Get booking details
      const hotelName = document.getElementById('hotelName').textContent;
      const checkIn = document.getElementById('checkInDate').textContent;
      const checkOut = document.getElementById('checkOutDate').textContent;

      // Create calendar event object
      const event = {
        title: `Stay at ${hotelName}`,
        start: new Date(checkIn),
        end: new Date(checkOut),
        location: document.getElementById('hotelAddress').textContent
      };

      // Create .ics file content
      const icsContent = generateICSFile(event);

      // Create and trigger download
      const blob = new Blob([icsContent], {
        type: 'text/calendar;charset=utf-8'
      });
      const link = document.createElement('a');
      link.href = window.URL.createObjectURL(blob);
      link.download = 'hotel_reservation.ics';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);

      showToast('Success!', 'Event has been added to your calendar.');
    });
  }

  // Initialize Print button
  const printBtn = document.getElementById('printBtn');
  if (printBtn) {
    printBtn.addEventListener('click', function () {
      window.print();
    });
  }

  // Show email sent notification
  function showEmailSentNotification() {
    const toast = document.createElement('div');
    toast.className = 'email-toast';
    toast.innerHTML = `
            <i class="fas fa-check-circle"></i>
            <div>
                <strong>Confirmation Sent!</strong>
                <p class="mb-0">Check your email for booking details</p>
            </div>
        `;
    document.body.appendChild(toast);

    setTimeout(() => {
      toast.remove();
    }, 5000);
  }

  // Show email notification on page load
  setTimeout(showEmailSentNotification, 1000);

  // Initialize map if available
  if (document.getElementById('hotelMap')) {
    // Add your map initialization code here
    // Example using Google Maps:
    /*
        const map = new google.maps.Map(document.getElementById('hotelMap'), {
            center: { lat: HOTEL_LAT, lng: HOTEL_LNG },
            zoom: 15
        });
        const marker = new google.maps.Marker({
            position: { lat: HOTEL_LAT, lng: HOTEL_LNG },
            map: map,
            title: hotelName
        });
        */
  }

  // Generate ICS file content
  function generateICSFile(event) {
    const formatDate = (date) => {
      return date.toISOString().replace(/-|:|\.\d+/g, '');
    };

    return `BEGIN:VCALENDAR
VERSION:2.0
BEGIN:VEVENT
DTSTART:${formatDate(event.start)}
DTEND:${formatDate(event.end)}
SUMMARY:${event.title}
LOCATION:${event.location}
DESCRIPTION:Hotel Reservation
STATUS:CONFIRMED
END:VEVENT
END:VCALENDAR`;
  }

  // Toast Notification
  function showToast(title, message, type = 'success') {
    const toastContainer = document.querySelector('.toast-container');
    if (toastContainer) toastContainer.remove();

    const toast = document.createElement('div');
    toast.className = 'toast-container position-fixed bottom-0 end-0 p-3';
    toast.innerHTML = `
            <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header ${
                  type === 'error' ? 'bg-danger text-white' : ''
                }">
                    <strong class="me-auto">${title}</strong>
                    <button type="button" class="btn-close ${
                      type === 'error' ? 'btn-close-white' : ''
                    }" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">${message}</div>
            </div>
        `;
    document.body.appendChild(toast);

    const toastEl = document.querySelector('.toast');
    const bsToast = new bootstrap.Toast(toastEl, { delay: 3000 });
    bsToast.show();
  }
});
