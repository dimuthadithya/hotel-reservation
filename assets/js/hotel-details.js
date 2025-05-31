// Bootstrap alert helper function
function showBootstrapAlert(type, title, message) {
  let icon = '';
  switch (type) {
    case 'success':
      icon = 'check-circle';
      break;
    case 'error':
      icon = 'exclamation-circle';
      type = 'danger';
      break;
    case 'warning':
      icon = 'exclamation-triangle';
      break;
    case 'info':
      icon = 'info-circle';
      break;
  }

  return `
    <div class='alert alert-${type} alert-dismissible fade show' role='alert'>
        <strong><i class='fas fa-${icon} me-2'></i>${title}</strong>
        ${message ? `<br>${message}` : ''}
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>`;
}

document.addEventListener('DOMContentLoaded', function () {
  // Initialize gallery thumbnails
  const galleryThumbs = document.querySelectorAll('.gallery-thumb');
  const mainGalleryImg = document.querySelector('.main-gallery img');

  galleryThumbs.forEach((thumb) => {
    thumb.addEventListener('click', function () {
      const newSrc = this.getAttribute('data-full-img');
      mainGalleryImg.src = newSrc;

      // Update active state
      galleryThumbs.forEach((t) => t.classList.remove('active'));
      this.classList.add('active');
    });
  });

  // Initialize room image sliders
  const roomSliders = document.querySelectorAll('.room-image-slider');
  roomSliders.forEach((slider) => {
    new bootstrap.Carousel(slider, {
      interval: false
    });
  });
  // Initialize date picker for availability
  const dateInputs = document.querySelectorAll('.date-input');
  dateInputs.forEach((input) => {
    const today = new Date().toISOString().split('T')[0];
    input.setAttribute('min', today);

    input.addEventListener('change', function () {
      if (this.id === 'checkInDate') {
        const checkOut = document.querySelector('#checkOutDate');
        if (checkOut) {
          checkOut.min = this.value;
          if (checkOut.value && checkOut.value <= this.value) {
            const nextDay = new Date(this.value);
            nextDay.setDate(nextDay.getDate() + 1);
            checkOut.value = nextDay.toISOString().split('T')[0];
          }
        }
      }
      checkRoomAvailability();
    });
  });

  // Check room availability when dates change
  async function checkRoomAvailability() {
    const checkIn = document.querySelector('#checkInDate').value;
    const checkOut = document.querySelector('#checkOutDate').value;
    const hotelId = document.querySelector('#hotelId').value;

    if (checkIn && checkOut && hotelId) {
      try {
        const response = await fetch('handlers/check_availability.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ checkIn, checkOut, hotelId })
        });

        const data = await response.json();
        updateRoomAvailability(data.rooms);
      } catch (error) {
        console.error('Error checking availability:', error);
        const alertArea =
          document.querySelector('.hotel-alerts') ||
          document.createElement('div');
        alertArea.className = 'hotel-alerts mb-3';
        if (!document.querySelector('.hotel-alerts')) {
          document
            .querySelector('.hotel-details')
            .insertBefore(
              alertArea,
              document.querySelector('.hotel-details').firstChild
            );
        }
        alertArea.innerHTML = showBootstrapAlert(
          'error',
          'Error',
          'Failed to check room availability. Please try again.'
        );
      }
    }
  }

  // Update room availability display
  function updateRoomAvailability(rooms) {
    rooms.forEach((room) => {
      const roomSection = document.querySelector(`[data-room-id="${room.id}"]`);
      if (roomSection) {
        const selectBtn = roomSection.querySelector('.select-room-btn');
        const availabilityBadge = roomSection.querySelector(
          '.availability-badge'
        );

        if (room.available) {
          selectBtn.disabled = false;
          if (availabilityBadge) {
            availabilityBadge.textContent = 'Available';
            availabilityBadge.classList.remove('bg-danger');
            availabilityBadge.classList.add('bg-success');
          }
        } else {
          selectBtn.disabled = true;
          if (availabilityBadge) {
            availabilityBadge.textContent = 'Not Available';
            availabilityBadge.classList.remove('bg-success');
            availabilityBadge.classList.add('bg-danger');
          }
        }
      }
    });
  }

  // Review filters
  const filterBtns = document.querySelectorAll('.review-filter');
  filterBtns.forEach((btn) => {
    btn.addEventListener('click', function () {
      filterBtns.forEach((b) => b.classList.remove('active'));
      this.classList.add('active');
      // Add filter logic here
    });
  });

  // Initialize map if available
  if (document.getElementById('hotelMap')) {
    // Add your map initialization code here
    // Example using Google Maps:
    /*
        const map = new google.maps.Map(document.getElementById('hotelMap'), {
            center: { lat: HOTEL_LAT, lng: HOTEL_LNG },
            zoom: 15
        });
        */
  }

  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        target.scrollIntoView({
          behavior: 'smooth'
        });
      }
    });
  });
  // Handle room selection
  const selectRoomBtns = document.querySelectorAll('.select-room-btn');
  selectRoomBtns.forEach((btn) => {
    btn.addEventListener('click', function () {
      const roomId = this.getAttribute('data-room-id');
      const roomName = this.getAttribute('data-room-name');
      const price = this.getAttribute('data-price');
      const checkIn = document.querySelector('#checkInDate').value;
      const checkOut = document.querySelector('#checkOutDate').value;

      if (!checkIn || !checkOut) {
        Swal.fire({
          icon: 'warning',
          title: 'Select Dates',
          text: 'Please select check-in and check-out dates first.'
        });
        return;
      }

      // Proceed to booking page with room details
      const params = new URLSearchParams({
        room_id: roomId,
        room_name: roomName,
        price: price,
        check_in: checkIn,
        check_out: checkOut
      });
      window.location.href = `booking.php?${params.toString()}`;
    });
  });
});
