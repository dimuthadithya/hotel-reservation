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
    // You can add a date picker library here
    input.setAttribute('min', new Date().toISOString().split('T')[0]);
  });

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
      // Add room selection logic here
      console.log(`Selected room: ${roomName} (ID: ${roomId})`);
    });
  });
});
