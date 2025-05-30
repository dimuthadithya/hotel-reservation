document.addEventListener('DOMContentLoaded', function () {
  // Tab Navigation
  const tabLinks = document.querySelectorAll('.nav-link');
  const tabContents = document.querySelectorAll('.tab-pane');

  tabLinks.forEach((link) => {
    link.addEventListener('click', function (e) {
      e.preventDefault();

      // Remove active class from all tabs and contents
      tabLinks.forEach((tab) => tab.classList.remove('active'));
      tabContents.forEach((content) =>
        content.classList.remove('active', 'show')
      );

      // Add active class to clicked tab
      this.classList.add('active');

      // Show corresponding content
      const contentId = this.getAttribute('href');
      const content = document.querySelector(contentId);
      if (content) {
        content.classList.add('active', 'show');
      }
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

  // Settings Form Submission
  const settingsForm = document.getElementById('settingsForm');
  if (settingsForm) {
    settingsForm.addEventListener('submit', function (e) {
      e.preventDefault();

      // Show loading state
      const submitBtn = this.querySelector('button[type="submit"]');
      const originalText = submitBtn.textContent;
      submitBtn.disabled = true;
      submitBtn.innerHTML =
        '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

      // Simulate API call
      setTimeout(() => {
        showToast('Success!', 'Your settings have been updated successfully.');
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
      }, 1500);
    });
  }

  // Password Change Form Submission
  const passwordForm = document.getElementById('passwordForm');
  if (passwordForm) {
    passwordForm.addEventListener('submit', function (e) {
      e.preventDefault();

      const newPassword = document.getElementById('newPassword').value;
      const confirmPassword = document.getElementById('confirmPassword').value;

      if (newPassword !== confirmPassword) {
        showToast('Error', 'Passwords do not match!', 'error');
        return;
      }

      // Show loading state
      const submitBtn = this.querySelector('button[type="submit"]');
      const originalText = submitBtn.textContent;
      submitBtn.disabled = true;
      submitBtn.innerHTML =
        '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';

      // Simulate API call
      setTimeout(() => {
        showToast('Success!', 'Your password has been updated successfully.');
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
        this.reset();
      }, 1500);
    });
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
