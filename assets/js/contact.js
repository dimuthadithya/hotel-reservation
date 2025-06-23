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

        // Get form data
        const formData = new FormData(this);

        // Send form data to server
        fetch('handlers/contact.php', {
          method: 'POST',
          body: formData
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              // Show success message
              const alertArea =
                document.querySelector('.contact-form-alerts') ||
                document.createElement('div');
              alertArea.className = 'contact-form-alerts mb-3';
              if (!document.querySelector('.contact-form-alerts')) {
                contactForm.parentNode.insertBefore(alertArea, contactForm);
              }
              alertArea.innerHTML = showBootstrapAlert(
                'success',
                'Message Sent!',
                'We will get back to you soon.'
              );

              // Reset form
              contactForm.reset();
            } else {
              throw new Error(data.message || 'Failed to send message');
            }
          })
          .catch((error) => {
            console.error('Error:', error);
            const alertArea =
              document.querySelector('.contact-form-alerts') ||
              document.createElement('div');
            alertArea.className = 'contact-form-alerts mb-3';
            if (!document.querySelector('.contact-form-alerts')) {
              contactForm.parentNode.insertBefore(alertArea, contactForm);
            }
            alertArea.innerHTML = showBootstrapAlert(
              'error',
              'Message Not Sent',
              error.message || 'Failed to send message. Please try again.'
            );
          })
          .finally(() => {
            // Reset button
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Send Message';
          });
      }
    });
  }
  // Form validation helper
  function validateForm() {
    let isValid = true;
    const errors = [];
    const requiredFields = contactForm.querySelectorAll('[required]');

    requiredFields.forEach((field) => {
      const value = field.value.trim();
      const label = field.getAttribute('data-label') || field.name;

      // Required field validation
      if (!value) {
        isValid = false;
        field.classList.add('is-invalid');
        errors.push(`Please enter your ${label}`);
      } else {
        field.classList.remove('is-invalid');

        // Email validation
        if (field.type === 'email') {
          const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if (!emailPattern.test(value)) {
            isValid = false;
            field.classList.add('is-invalid');
            errors.push('Please enter a valid email address');
          }
        }

        // Phone validation (if present)
        if (field.type === 'tel') {
          const phonePattern = /^(\+94|94|0)[1-9][0-9]{8}$/;
          if (!phonePattern.test(value.replace(/\s+/g, ''))) {
            isValid = false;
            field.classList.add('is-invalid');
            errors.push('Please enter a valid phone number');
          }
        }
      }
    });

    // Message length validation
    const messageField = contactForm.querySelector('textarea[name="message"]');
    if (messageField && messageField.value.trim().length < 10) {
      isValid = false;
      messageField.classList.add('is-invalid');
      errors.push('Message must be at least 10 characters long');
    }

    if (!isValid) {
      Swal.fire({
        icon: 'error',
        title: 'Please Fix the Following:',
        html: errors.map((err) => `• ${err}`).join('<br>')
      });
    }

    return isValid;
  }
  // Show notification helper
  function showNotification(title, message, type = 'success') {
    Swal.fire({
      icon: type,
      title: title,
      text: message,
      toast: true,
      position: 'bottom-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true
    });
  }
});
