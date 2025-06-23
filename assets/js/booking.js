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
  // Initialize date pickers
  const checkInDate = document.getElementById('checkInDate');
  const checkOutDate = document.getElementById('checkOutDate');

  if (checkInDate && checkOutDate) {
    // Set minimum date as today
    const today = new Date().toISOString().split('T')[0];
    checkInDate.min = today;

    // Update checkout minimum date when checkin changes
    checkInDate.addEventListener('change', function () {
      checkOutDate.min = this.value;
      if (checkOutDate.value && checkOutDate.value <= this.value) {
        const nextDay = new Date(this.value);
        nextDay.setDate(nextDay.getDate() + 1);
        checkOutDate.value = nextDay.toISOString().split('T')[0];
      }
      updateTotalPrice();
    });

    checkOutDate.addEventListener('change', updateTotalPrice);
  }

  // Payment method selection
  const paymentMethods = document.querySelectorAll('.payment-method-option');
  const creditCardForm = document.getElementById('creditCardForm');
  const onlineBankingForm = document.getElementById('onlineBankingForm');

  paymentMethods.forEach((method) => {
    method.addEventListener('click', function () {
      // Remove selected class from all methods
      paymentMethods.forEach((m) => m.classList.remove('selected'));
      // Add selected class to clicked method
      this.classList.add('selected');

      // Show/hide relevant payment forms
      const paymentType = this.getAttribute('data-payment-type');
      if (creditCardForm && onlineBankingForm) {
        if (paymentType === 'credit-card') {
          creditCardForm.classList.remove('d-none');
          onlineBankingForm.classList.add('d-none');
        } else if (paymentType === 'online-banking') {
          creditCardForm.classList.add('d-none');
          onlineBankingForm.classList.remove('d-none');
        }
      }
    });
  });

  // Form validation
  const bookingForm = document.getElementById('bookingForm');
  if (bookingForm) {
    bookingForm.addEventListener('submit', function (e) {
      e.preventDefault();
      if (validateForm()) {
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML =
          '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

        // Get form data
        const formData = new FormData(this);

        // Submit the booking
        fetch('handlers/process_booking.php', {
          method: 'POST',
          body: formData
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              // Show success message
              const alertArea =
                document.querySelector('.booking-form-alerts') ||
                document.createElement('div');
              alertArea.className = 'booking-form-alerts mb-3';
              if (!document.querySelector('.booking-form-alerts')) {
                bookingForm.parentNode.insertBefore(alertArea, bookingForm);
              }
              alertArea.innerHTML = showBootstrapAlert(
                'success',
                'Booking Successful!',
                'Redirecting to confirmation page...'
              );

              // Redirect after a short delay
              setTimeout(() => {
                window.location.href =
                  'confirmation.php?booking_id=' + data.booking_id;
              }, 2000);
            } else {
              throw new Error(data.message || 'Failed to process booking');
            }
          })
          .catch((error) => {
            console.error('Error:', error);
            const alertArea =
              document.querySelector('.booking-form-alerts') ||
              document.createElement('div');
            alertArea.className = 'booking-form-alerts mb-3';
            if (!document.querySelector('.booking-form-alerts')) {
              bookingForm.parentNode.insertBefore(alertArea, bookingForm);
            }
            alertArea.innerHTML = showBootstrapAlert(
              'error',
              'Booking Failed',
              error.message || 'Failed to process booking. Please try again.'
            );
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Complete Booking';
          });
      }
    });
  }
  // Form validation helper
  function validateForm() {
    let isValid = true;
    const requiredFields = bookingForm.querySelectorAll('[required]');
    const errors = [];

    requiredFields.forEach((field) => {
      if (!field.value.trim()) {
        isValid = false;
        field.classList.add('is-invalid');
        errors.push(
          `Please enter ${field.getAttribute('data-label') || field.name}`
        );
      } else {
        field.classList.remove('is-invalid');

        // Validate email format if it's an email field
        if (field.type === 'email' && !isValidEmail(field.value)) {
          isValid = false;
          field.classList.add('is-invalid');
          errors.push('Please enter a valid email address');
        }

        // Validate phone format if it's a phone field
        if (field.type === 'tel' && !isValidPhone(field.value)) {
          isValid = false;
          field.classList.add('is-invalid');
          errors.push('Please enter a valid phone number');
        }
      }
    });

    // Validate terms checkbox
    const termsCheckbox = document.getElementById('termsCheckbox');
    if (termsCheckbox && !termsCheckbox.checked) {
      isValid = false;
      errors.push('Please accept the terms and conditions');
    }

    // Show validation errors if any
    if (!isValid) {
      Swal.fire({
        icon: 'error',
        title: 'Please Fix the Following:',
        html: errors.map((err) => `• ${err}`).join('<br>')
      });
    }

    return isValid;
  }
  // Price calculation
  function updateTotalPrice() {
    const checkIn = new Date(checkInDate.value);
    const checkOut = new Date(checkOutDate.value);

    if (checkIn && checkOut && checkOut > checkIn) {
      const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
      const basePriceElement = document.getElementById('basePrice');
      const basePrice = basePriceElement
        ? parseFloat(basePriceElement.value)
        : 25000; // Price per night in LKR
      const totalBase = basePrice * nights;
      const taxRate = 0.1; // 10% tax
      const tax = Math.round(totalBase * taxRate);
      const serviceFee = 2500;
      const total = totalBase + tax + serviceFee;

      // Update summary elements
      document.getElementById(
        'basePriceTotal'
      ).textContent = `LKR ${totalBase.toLocaleString()}`;
      document.getElementById(
        'taxesTotal'
      ).textContent = `LKR ${tax.toLocaleString()}`;
      document.getElementById(
        'serviceFeeTotal'
      ).textContent = `LKR ${serviceFee.toLocaleString()}`;
      document.getElementById(
        'finalTotal'
      ).textContent = `LKR ${total.toLocaleString()}`;
    }
  }
  // Notification helper
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

  // Helper functions for validation
  function isValidEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
  }

  function isValidPhone(phone) {
    // Allows formats: +94XXXXXXXXX, 94XXXXXXXXX, 0XXXXXXXXX
    const regex = /^(\+94|94|0)[1-9][0-9]{8}$/;
    return regex.test(phone.replace(/\s+/g, ''));
  }
});
