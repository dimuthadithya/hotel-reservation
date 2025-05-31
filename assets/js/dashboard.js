document.addEventListener('DOMContentLoaded', function () {
  // Initialize Bootstrap tabs
  const triggerTabList = [].slice.call(
    document.querySelectorAll('.nav-tabs a')
  );
  triggerTabList.forEach(function (triggerEl) {
    // Create a new Tab instance for each nav item
    const tabTrigger = new bootstrap.Tab(triggerEl);

    triggerEl.addEventListener('click', function (event) {
      event.preventDefault();
      tabTrigger.show();
    });
  });

  // Profile Image Upload
  const profileImageUpload = document.getElementById('profileImageUpload');
  const profileImage = document.getElementById('profileImage');

  if (profileImageUpload && profileImage) {
    profileImageUpload.addEventListener('change', function (e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          profileImage.src = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    });
  }

  // Edit Review Modal
  const editReviewBtns = document.querySelectorAll('.edit-review-btn');
  editReviewBtns.forEach((btn) => {
    btn.addEventListener('click', function () {
      const reviewId = this.getAttribute('data-review-id');
      const reviewText = document.querySelector(
        `#review-${reviewId} .review-text`
      ).textContent;
      const rating = document
        .querySelector(`#review-${reviewId} .review-rating`)
        .getAttribute('data-rating');

      // Set modal values
      document.getElementById('editReviewText').value = reviewText;
      document.getElementById('editReviewRating').value = rating;
      document.getElementById('editReviewId').value = reviewId;
    });
  });

  // Delete Review Confirmation
  const deleteReviewBtns = document.querySelectorAll('.delete-review-btn');
  deleteReviewBtns.forEach((btn) => {
    btn.addEventListener('click', function () {
      const reviewId = this.getAttribute('data-review-id');
      if (confirm('Are you sure you want to delete this review?')) {
        document.querySelector(`#review-${reviewId}`).remove();
        // Here you would typically make an API call to delete the review
      }
    });
  });
});
