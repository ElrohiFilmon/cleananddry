// assets/js/main.js
(() => {
  // Utilities
  const $ = (s) => document.querySelector(s);
  const $$ = (s) => document.querySelectorAll(s);

  // Year auto-fill
  const y = new Date().getFullYear();
  const yearEl = document.getElementById('year');
  if (yearEl) yearEl.textContent = y;

  // Mobile menu
  const mobileMenuBtn = document.getElementById('mobileMenuBtn');
  const mobileMenu = document.getElementById('mobileMenu');
  if (mobileMenuBtn && mobileMenu) {
    mobileMenuBtn.addEventListener('click', () => {
      mobileMenu.classList.toggle('hidden');
    });
  }

  // Dark mode toggle (persist in localStorage)
  const DARK_KEY = 'cleandry_dark';
  const root = document.documentElement;
  function setDark(dark) {
    if (dark) {
      root.classList.add('dark');
      localStorage.setItem(DARK_KEY, '1');
    } else {
      root.classList.remove('dark');
      localStorage.removeItem(DARK_KEY);
    }
  }
  // set initial
  const saved = localStorage.getItem(DARK_KEY);
  if (saved === '1') setDark(true);

  const darkButtons = document.querySelectorAll('#darkToggle, #darkToggleTop');
  darkButtons.forEach(btn => btn.addEventListener('click', () => {
    setDark(!root.classList.contains('dark'));
  }));

  // Simple GSAP hero shine animation
  function heroGSAP() {
    if (!window.gsap) return;
    const shine = document.getElementById('heroShine');
    if (!shine) return;
    // create animated gradient overlay
    shine.style.background = 'linear-gradient(120deg, rgba(255,255,255,0.0) 0%, rgba(255,255,255,0.35) 45%, rgba(255,255,255,0.0) 80%)';
    shine.style.transform = 'translateX(-120%)';
    shine.style.opacity = '0.9';
    gsap.to(shine, { x: '240%', duration: 2.2, repeat: -1, ease: 'power2.inOut', delay: 0.6 });
  }
  document.addEventListener('DOMContentLoaded', heroGSAP);

  // Simple forms interception (demo); replace with real API calls
  const bookingForm = document.getElementById('bookingForm');
  if (bookingForm) {
    bookingForm.addEventListener('submit', (e) => {
      e.preventDefault();
      // Grab form data
      const data = new FormData(bookingForm);
      // Minimal validation demo
      alert('Booking submitted — this demo will redirect to payment page (not implemented).');
      // In production: POST to /api/orders, then redirect to payment gateway
      // fetch('/api/orders', { method:'POST', body: data })
      //   .then(r => r.json()).then(...)
      window.location.href = 'order-details.html';
    });
  }

  // Login & Signup forms demo
  const loginForm = document.getElementById('loginForm');
  if (loginForm) {
    loginForm.addEventListener('submit', (e) => {
      // in production, send credentials to backend and set auth token or cookie
      e.preventDefault();
      alert('Logged in (demo). Redirecting to Home.');
      window.location.href = 'home.html';
    });
  }

  const signupForm = document.getElementById('signupForm');
  if (signupForm) {
    signupForm.addEventListener('submit', (e) => {
      e.preventDefault();
      alert('Account created (demo). Redirecting to Home.');
      window.location.href = 'home.html';
    });
  }

  // Provider actions: accept/decline demo
  document.addEventListener('click', (ev) => {
    if (ev.target.matches('.acceptBtn')) {
      ev.target.textContent = 'Accepted';
      ev.target.classList.remove('bg-green-600');
      ev.target.classList.add('bg-gray-500');
      // In production: API is called to accept order, then open Google Maps if mobile
      alert('Order accepted — open directions via Google Maps (not implemented in demo).');
    } else if (ev.target.matches('.declineBtn')) {
      ev.target.closest('div').remove(); // remove card in demo
      alert('Order declined (demo).');
    } else if (ev.target.matches('#cancelBtn')) {
      if (confirm('Cancel this order? Cancellation fees may apply.')) {
        // call API to cancel => then redirect or update UI
        alert('Order canceled (demo).');
        window.location.href = 'home.html';
      }
    } else if (ev.target.matches('#confirmComplete')) {
      // Mark as completed (demo)
      alert('Order marked complete. Thank you!');
      window.location.href = 'reviews.html';
    }
  });

  // Small helper to wire "Open in Google Maps" links with coordinates (demo)
  const mapsLink = document.getElementById('mapsLink');
  if (mapsLink) {
    // in production, build google maps url using lat,lng or address
    mapsLink.href = 'https://www.google.com/maps';
  }

  // Prefill user name demo
  const userNameEl = document.getElementById('userName');
  if (userNameEl) userNameEl.textContent = 'Alex';

})();
