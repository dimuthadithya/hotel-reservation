$(document).ready(function () {
  // Navbar scroll effect
  $(window).scroll(function () {
    if ($(window).scrollTop() > 50) {
      $('.navbar').addClass('scrolled');
    } else {
      $('.navbar').removeClass('scrolled');
    }
  });

  // Smooth scrolling for navigation links
  $('a[href^="#"]').on('click', function (event) {
    var target = $(this.getAttribute('href'));
    if (target.length) {
      event.preventDefault();
      $('html, body')
        .stop()
        .animate(
          {
            scrollTop: target.offset().top - 80
          },
          1000
        );
    }
  });

  // Initialize carousel with custom settings
  $('#heroCarousel').carousel({
    interval: 6000,
    pause: 'hover'
  });

  // Add fade animation to sections on scroll
  $(window).scroll(function () {
    $('.fade-in-section').each(function () {
      if (
        $(window).scrollTop() + $(window).height() >
        $(this).offset().top + 100
      ) {
        $(this).addClass('visible');
      }
    });
  });

  // Initialize date pickers with today as minimum date
  var today = new Date().toISOString().split('T')[0];
  $('input[type="date"]').attr('min', today);

  // Simple form validation for newsletter
  $('.newsletter-section button').on('click', function () {
    var email = $(this).prev('input[type="email"]').val();
    if (!email) {
      alert('Please enter your email address');
      return;
    }
    if (!isValidEmail(email)) {
      alert('Please enter a valid email address');
      return;
    }
    alert('Thank you for subscribing!');
    $(this).prev('input[type="email"]').val('');
  });

  // Email validation helper function
  function isValidEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
  }

  // Room search validation
  $('.hero-section button').on('click', function () {
    var location = $(this).prev('input[type="text"]').val();
    if (!location) {
      alert('Please enter a destination');
      return;
    }
    // Here you would typically handle the search functionality
    alert('Searching for rooms in ' + location);
  });

  // Enhanced room card hover effects
  $('.room-card').hover(
    function () {
      $(this).addClass('shadow-lg');
      $(this).find('.btn').addClass('btn-hover');
    },
    function () {
      $(this).removeClass('shadow-lg');
      $(this).find('.btn').removeClass('btn-hover');
    }
  );
});
