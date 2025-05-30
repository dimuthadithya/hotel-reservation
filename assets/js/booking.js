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
          '<span class="spinner-border spinner-border-sm me-2"></span>Processing...'; // Simulate form submission
        setTimeout(() => {
          // Redirect to confirmation page
          window.location.href = 'confirmation.html';
        }, 2000);
      }
    });
  }

  // Form validation helper
  function validateForm() {
    let isValid = true;
    const requiredFields = bookingForm.querySelectorAll('[required]');

    requiredFields.forEach((field) => {
      if (!field.value.trim()) {
        isValid = false;
        field.classList.add('is-invalid');
      } else {
        field.classList.remove('is-invalid');
      }
    });

    // Validate terms checkbox
    const termsCheckbox = document.getElementById('termsCheckbox');
    if (termsCheckbox && !termsCheckbox.checked) {
      isValid = false;
      showToast('Error', 'Please accept the terms and conditions', 'error');
    }

    return isValid;
  }

  // Price calculation
  function updateTotalPrice() {
    const checkIn = new Date(checkInDate.value);
    const checkOut = new Date(checkOutDate.value);

    if (checkIn && checkOut && checkOut > checkIn) {
      const nights = (checkOut - checkIn) / (1000 * 60 * 60 * 24);
      const basePrice = 25000; // Price per night in LKR
      const totalBase = basePrice * nights;
      const tax = totalBase * 0.1; // 10% tax
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

  // Toast notification
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
