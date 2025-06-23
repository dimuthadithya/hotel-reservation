function previewImage(input, previewId) {
  const preview = document.getElementById(previewId);
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function (e) {
      preview.src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
  }
}

function editHotel(hotelId) {
  // Existing edit hotel functionality
  fetch(`handlers/get_hotel.php?id=${hotelId}`)
    .then((response) => response.json())
    .then((hotel) => {
      document.getElementById('editHotelId').value = hotel.hotel_id;
      document.getElementById('editHotelName').value = hotel.hotel_name;
      document.getElementById('editDescription').value = hotel.description;
      document.getElementById('editAddress').value = hotel.address;
      document.getElementById('editDistrict').value = hotel.district;
      document.getElementById('editProvince').value = hotel.province;
      document.getElementById('editPropertyType').value = hotel.property_type;
      document.getElementById('editStarRating').value = hotel.star_rating;
      document.getElementById('editStatus').value = hotel.status;

      // Set the current image preview if it exists
      const imagePreview = document.getElementById('editImagePreview');
      if (hotel.main_image) {
        imagePreview.src =
          '../uploads/img/hotels/' + hotel.hotel_id + '/' + hotel.main_image;
      } else {
        imagePreview.src =
          'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
      }

      $('#editHotelModal').modal('show');
    })
    .catch((error) => console.error('Error:', error));
}

// Add Hotel Form Validation
document
  .getElementById('addHotelForm')
  .addEventListener('submit', function (e) {
    // Client-side validation
    const requiredFields = [
      'hotel_name',
      'description',
      'address',
      'district',
      'province',
      'star_rating',
      'property_type'
    ];
    let isValid = true;

    requiredFields.forEach((field) => {
      const input = this.elements[field];
      if (!input.value.trim()) {
        isValid = false;
        input.classList.add('is-invalid');
      } else {
        input.classList.remove('is-invalid');
      }
    });

    if (!isValid) {
      e.preventDefault();
      alert('Please fill in all required fields');
      return;
    }

    // File validation
    const fileInput = this.elements['main_image'];
    if (fileInput.files.length > 0) {
      const file = fileInput.files[0];
      if (file.size > 2 * 1024 * 1024) {
        // 2MB
        e.preventDefault();
        alert('File size too large. Maximum size is 2MB.');
        return;
      }
      if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
        e.preventDefault();
        alert('Invalid file type. Only JPG, PNG and GIF are allowed.');
        return;
      }
    }
  });
