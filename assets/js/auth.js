// Common authentication functions
function checkPasswordStrength(password) {
  if (password.length === 0) return 0;
  let strength = 0;
  if (password.length >= 8) strength++;
  if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
  if (password.match(/\d/)) strength++;
  if (password.match(/[^a-zA-Z\d]/)) strength++;
  return strength;
}

function updateStrengthMeter(password) {
  const meter = document.getElementById('strengthMeter');
  if (!meter) return;

  const strength = checkPasswordStrength(password);
  meter.className = 'strength-meter';

  if (strength >= 4) {
    meter.classList.add('strong');
  } else if (strength >= 2) {
    meter.classList.add('medium');
  } else if (strength >= 1) {
    meter.classList.add('weak');
  }
}

function togglePasswordVisibility(inputId) {
  const input = document.getElementById(inputId);
  const toggleBtn = input.nextElementSibling;

  if (input.type === 'password') {
    input.type = 'text';
    toggleBtn.classList.remove('fa-eye');
    toggleBtn.classList.add('fa-eye-slash');
  } else {
    input.type = 'password';
    toggleBtn.classList.remove('fa-eye-slash');
    toggleBtn.classList.add('fa-eye');
  }
}

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

function validateForm(form) {
  let isValid = true;
  form.querySelectorAll('input[required]').forEach((input) => {
    if (!input.value.trim()) {
      isValid = false;
      input.classList.add('is-invalid');
    } else {
      input.classList.remove('is-invalid');
    }
  });
  return isValid;
}

// Initialize login page
function initializeLoginPage() {
  const loginForm = document.getElementById('loginForm');
  if (!loginForm) return;

  const passwordToggle = document.getElementById('passwordToggle');
  if (passwordToggle) {
    passwordToggle.addEventListener('click', () =>
      togglePasswordVisibility('password')
    );
  }

  loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (!validateForm(loginForm)) return;

    const submitBtn = loginForm.querySelector('button[type="submit"]');
    const spinner = submitBtn.querySelector('.spinner-border');

    submitBtn.disabled = true;
    spinner.classList.remove('d-none');

    // Simulate API call
    try {
      await new Promise((resolve) => setTimeout(resolve, 2000));
      showToast('Success!', 'You have been successfully logged in.');
      window.location.href = 'index.html'; // Redirect to dashboard
    } catch (error) {
      showToast('Error', 'Failed to log in. Please try again.', 'error');
    } finally {
      submitBtn.disabled = false;
      spinner.classList.add('d-none');
    }
  });
}

// Initialize register page
function initializeRegisterPage() {
  const registerForm = document.getElementById('registerForm');
  if (!registerForm) return;

  const password = document.getElementById('password');
  const confirmPassword = document.getElementById('confirmPassword');
  const passwordToggle = document.getElementById('passwordToggle');

  if (passwordToggle) {
    passwordToggle.addEventListener('click', () =>
      togglePasswordVisibility('password')
    );
  }

  if (password) {
    password.addEventListener('input', () => {
      updateStrengthMeter(password.value);
      // Check password confirmation
      if (confirmPassword.value) {
        if (password.value !== confirmPassword.value) {
          confirmPassword.classList.add('is-invalid');
        } else {
          confirmPassword.classList.remove('is-invalid');
        }
      }
    });
  }

  if (confirmPassword) {
    confirmPassword.addEventListener('input', () => {
      if (password.value !== confirmPassword.value) {
        confirmPassword.classList.add('is-invalid');
      } else {
        confirmPassword.classList.remove('is-invalid');
      }
    });
  }

  registerForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    // Additional validation
    if (password.value !== confirmPassword.value) {
      confirmPassword.classList.add('is-invalid');
      return;
    }

    if (!validateForm(registerForm)) return;

    const submitBtn = registerForm.querySelector('button[type="submit"]');
    const spinner = submitBtn.querySelector('.spinner-border');

    submitBtn.disabled = true;
    spinner.classList.remove('d-none');

    // Simulate API call
    try {
      await new Promise((resolve) => setTimeout(resolve, 2000));
      showToast('Success!', 'Your account has been created successfully.');
      window.location.href = 'login.html'; // Redirect to login
    } catch (error) {
      showToast(
        'Error',
        'Failed to create account. Please try again.',
        'error'
      );
    } finally {
      submitBtn.disabled = false;
      spinner.classList.add('d-none');
    }
  });
}
