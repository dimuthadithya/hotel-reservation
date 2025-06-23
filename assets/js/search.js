document.addEventListener('DOMContentLoaded', function () {
  // Initialize price range slider
  const priceRange = document.getElementById('price-range');
  const priceMin = document.getElementById('price-min');
  const priceMax = document.getElementById('price-max');

  if (priceRange) {
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
          return Number(value.replace(/[^\d.]/g, ''));
        }
      }
    });

    // Update price labels and trigger filter
    priceRange.noUiSlider.on('update', function (values, handle) {
      if (handle === 0) {
        priceMin.innerHTML = values[0];
      } else {
        priceMax.innerHTML = values[1];
      }
      filterHotels();
    });
  }

  // Handle checkbox filters
  const filterCheckboxes = document.querySelectorAll('.form-check-input');
  filterCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener('change', filterHotels);
  });

  // Handle star rating and guest rating buttons
  const ratingButtons = document.querySelectorAll(
    '.star-rating-filters button, .rating-buttons button'
  );
  ratingButtons.forEach((button) => {
    button.addEventListener('click', function (e) {
      e.preventDefault();
      const isStarRating = this.closest('.star-rating-filters');
      const group = isStarRating
        ? '.star-rating-filters button'
        : '.rating-buttons button';

      // If ctrl/cmd key is held, allow multiple selection
      if (!e.ctrlKey && !e.metaKey) {
        document.querySelectorAll(group).forEach((btn) => {
          if (btn !== this) btn.classList.remove('active');
        });
      }

      this.classList.toggle('active');
      filterHotels();
    });
  });

  // Handle sort functionality
  const sortSelect = document.querySelector('.form-select');
  if (sortSelect) {
    sortSelect.addEventListener('change', filterHotels);
  }

  // Update results count and UI
  function updateResults(visibleHotels) {
    // Update visibility
    const allHotels = document.querySelectorAll('.hotel-card');
    allHotels.forEach((card) => {
      card.style.display = visibleHotels.includes(card) ? 'block' : 'none';
    });

    // Update results count
    const resultsCount = document.querySelector('.results-count h4');
    if (resultsCount) {
      resultsCount.textContent = `${visibleHotels.length} properties found`;
    }

    // Sort visible hotels if needed
    if (sortSelect && visibleHotels.length > 0) {
      const sortBy = sortSelect.value;
      visibleHotels.sort((a, b) => {
        const priceA = parseFloat(a.dataset.price) || 0;
        const priceB = parseFloat(b.dataset.price) || 0;
        const ratingA = parseFloat(a.dataset.rating) || 0;
        const ratingB = parseFloat(b.dataset.rating) || 0;

        switch (sortBy) {
          case 'Price: Low to High':
            return priceA - priceB;
          case 'Price: High to Low':
            return priceB - priceA;
          case 'Guest Rating':
            return ratingB - ratingA;
          default: // Recommended
            // Sort by rating first, then price if ratings are equal
            if (ratingA !== ratingB) return ratingB - ratingA;
            return priceA - priceB;
        }
      });

      // Re-append sorted hotels to maintain sort order
      const container = visibleHotels[0].parentNode;
      visibleHotels.forEach((hotel) => container.appendChild(hotel));
    }
  }

  function filterHotels() {
    const hotelCards = document.querySelectorAll('.hotel-card');
    if (!hotelCards.length) return;

    try {
      let visibleHotels = Array.from(hotelCards);

      // Get filter values
      const selectedPropertyTypes = Array.from(
        document.querySelectorAll('[id^="property-type-"]:checked')
      ).map((cb) => cb.value);

      const selectedAmenities = Array.from(
        document.querySelectorAll('[id^="amenity-"]:checked')
      ).map((cb) => cb.value);

      const selectedStarRatings = Array.from(
        document.querySelectorAll('.star-rating-filters button.active')
      ).map((btn) => parseInt(btn.dataset.rating || btn.textContent));

      const selectedGuestRatings = Array.from(
        document.querySelectorAll('.rating-buttons button.active')
      ).map((btn) => {
        const text = btn.textContent.trim();
        return parseFloat(text.replace('+', ''));
      });

      // Get price range values
      const priceValues = priceRange ? priceRange.noUiSlider.get() : null;
      const minPrice = priceValues
        ? parseFloat(priceValues[0].replace(/[^\d.]/g, ''))
        : 0;
      const maxPrice = priceValues
        ? parseFloat(priceValues[1].replace(/[^\d.]/g, ''))
        : Infinity;

      // Filter by property type
      if (selectedPropertyTypes.length > 0) {
        visibleHotels = visibleHotels.filter((hotel) => {
          const propertyType = (hotel.dataset.propertyType || '').toLowerCase();
          return selectedPropertyTypes
            .map((type) => type.toLowerCase())
            .includes(propertyType);
        });
      }

      // Filter by amenities
      if (selectedAmenities.length > 0) {
        visibleHotels = visibleHotels.filter((hotel) => {
          try {
            const hotelAmenities = JSON.parse(hotel.dataset.amenities || '[]');
            return selectedAmenities.every((amenity) =>
              hotelAmenities
                .map((a) => a.toLowerCase())
                .includes(amenity.toLowerCase())
            );
          } catch (e) {
            return false;
          }
        });
      }

      // Filter by star rating
      if (selectedStarRatings.length > 0) {
        visibleHotels = visibleHotels.filter((hotel) => {
          const starRating = parseInt(hotel.dataset.starRating) || 0;
          return selectedStarRatings.includes(starRating);
        });
      }

      // Filter by guest rating
      if (selectedGuestRatings.length > 0) {
        visibleHotels = visibleHotels.filter((hotel) => {
          const rating = parseFloat(hotel.dataset.rating) || 0;
          return selectedGuestRatings.some((minRating) => rating >= minRating);
        });
      }

      // Filter by price range
      if (priceRange && priceValues) {
        visibleHotels = visibleHotels.filter((hotel) => {
          const price = parseFloat(hotel.dataset.price) || 0;
          return price >= minPrice && price <= maxPrice;
        });
      }

      // Update results and sort
      updateResults(visibleHotels);
    } catch (error) {
      // On error, show all hotels
      hotelCards.forEach((card) => (card.style.display = 'block'));
      const resultsCount = document.querySelector('.results-count h4');
      if (resultsCount) {
        resultsCount.textContent = `${hotelCards.length} properties found`;
      }
    }
  }

  // Initial filter
  filterHotels();
});
