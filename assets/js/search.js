document.addEventListener('DOMContentLoaded', function () {
  // Initialize price range slider
  if (document.getElementById('price-range')) {
    const priceRange = document.getElementById('price-range');
    const priceMin = document.getElementById('price-min');
    const priceMax = document.getElementById('price-max');

    noUiSlider.create(priceRange, {
      start: [5000, 50000],
      connect: true,
      range: {
        min: 5000,
        max: 50000
      },
      step: 1000,
      format: {
        to: function (value) {
          return 'LKR ' + Math.round(value).toLocaleString();
        },
        from: function (value) {
          return Number(value.replace('LKR ', '').replace(',', ''));
        }
      }
    });

    priceRange.noUiSlider.on('update', function (values, handle) {
      if (handle === 0) {
        priceMin.innerHTML = values[0];
      } else {
        priceMax.innerHTML = values[1];
      }
    });
  }

  // Initialize star rating buttons
  const ratingButtons = document.querySelectorAll('.star-rating-filters .btn');
  ratingButtons.forEach((button) => {
    button.addEventListener('click', function () {
      this.classList.toggle('active');
    });
  });

  // Initialize guest rating buttons
  const guestRatingButtons = document.querySelectorAll('.rating-buttons .btn');
  guestRatingButtons.forEach((button) => {
    button.addEventListener('click', function () {
      this.classList.toggle('active');
    });
  });
  // Favorite buttons functionality removed as wishlist feature is no longer used

  // Filter show/hide for mobile
  const showFiltersBtn = document.querySelector('.show-filters-btn');
  const filtersSidebar = document.querySelector('.filters-sidebar');

  if (showFiltersBtn && filtersSidebar) {
    showFiltersBtn.addEventListener('click', function () {
      filtersSidebar.classList.toggle('show');
    });
  }
  // Sort functionality
  const sortSelect = document.querySelector('select');
  if (sortSelect) {
    sortSelect.addEventListener('change', function () {
      const sortBy = this.value;
      const hotelCards = Array.from(document.querySelectorAll('.hotel-card'));
      const hotelContainer = document.querySelector('.hotel-listings');

      if (hotelContainer && hotelCards.length > 0) {
        hotelCards.sort((a, b) => {
          const priceA = parseFloat(a.dataset.price || 0);
          const priceB = parseFloat(b.dataset.price || 0);
          const ratingA = parseFloat(a.dataset.rating || 0);
          const ratingB = parseFloat(b.dataset.rating || 0);

          switch (sortBy) {
            case 'price-low':
              return priceA - priceB;
            case 'price-high':
              return priceB - priceA;
            case 'rating':
              return ratingB - ratingA;
            default:
              return 0;
          }
        });

        hotelContainer.innerHTML = '';
        hotelCards.forEach((card) => hotelContainer.appendChild(card));
      }
    });
  }
});
