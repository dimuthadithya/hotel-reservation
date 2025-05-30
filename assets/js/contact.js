// Contact Form Handling
document.addEventListener('DOMContentLoaded', function () {
  const contactForm = document.getElementById('contactForm');

  if (contactForm) {
    contactForm.addEventListener('submit', function (e) {
      e.preventDefault();

      if (validateForm()) {
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML =
          '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';

        // Simulate form submission (replace with actual API call)
        setTimeout(() => {
          showToast(
            'Success',
            'Your message has been sent successfully!',
            'success'
          );

          // Reset form
          contactForm.reset();

          // Reset button
          submitBtn.disabled = false;
          submitBtn.innerHTML = 'Send Message';
        }, 2000);
      }
    });
  }

  // Form validation helper
  function validateForm() {
    let isValid = true;
    const requiredFields = contactForm.querySelectorAll('[required]');

    requiredFields.forEach((field) => {
      if (!field.value.trim()) {
        isValid = false;
        field.classList.add('is-invalid');
        showToast('Error', `Please fill in all required fields`, 'error');
      } else {
        field.classList.remove('is-invalid');
      }

      // Email validation
      if (field.type === 'email' && field.value.trim()) {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(field.value.trim())) {
          isValid = false;
          field.classList.add('is-invalid');
          showToast('Error', 'Please enter a valid email address', 'error');
        }
      }
    });

    return isValid;
  }

  // Toast notification helper
  function showToast(title, message, type = 'success') {
    const toastContainer = document.querySelector('.toast-container');
    if (toastContainer) toastContainer.remove();

    const toast = document.createElement('div');
    toast.className = 'toast-container position-fixed bottom-0 end-0 p-3';
    toast.innerHTML = `
      <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header ${
          type === 'error' ? 'bg-danger text-white' : 'bg-success text-white'
        }">
          <strong class="me-auto">${title}</strong>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
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
