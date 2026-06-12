document.addEventListener('DOMContentLoaded', function () {
  // Bootstrap tooltips
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
    new bootstrap.Tooltip(el);
  });

  // Confirmation dialogs
  document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('click', function (e) {
      if (!confirm(this.dataset.confirm)) e.preventDefault();
    });
  });

  // Auto-dismiss alerts
  document.querySelectorAll('.alert-dismissible').forEach(function (el) {
    setTimeout(function () { el.remove(); }, 5000);
  });

  // ── Scroll-triggered animations ──
  var animateTargets = document.querySelectorAll('[data-animate]');
  if (animateTargets.length && 'IntersectionObserver' in window) {
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          var el = entry.target;
          var anim = el.dataset.animate || 'fade-up';
          el.classList.add('animate-' + anim);
          observer.unobserve(el);
        }
      });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

    animateTargets.forEach(function (el) {
      observer.observe(el);
    });
  } else {
    // Fallback: add animation classes immediately
    animateTargets.forEach(function (el) {
      var anim = el.dataset.animate || 'fade-up';
      el.classList.add('animate-' + anim);
    });
  }

  // ── Sidebar toggle for mobile ──
  var sidebarToggle = document.getElementById('sidebarToggle');
  var sidebar = document.querySelector('.sidebar-desktop');
  var overlay = document.querySelector('.sidebar-overlay');
  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', function () {
      sidebar.classList.toggle('open');
      if (overlay) overlay.classList.toggle('open');
    });
    if (overlay) {
      overlay.addEventListener('click', function () {
        sidebar.classList.remove('open');
        overlay.classList.remove('open');
      });
    }
  }

  // ── Role card selection (auth register) ──
  document.querySelectorAll('.role-card').forEach(function (card) {
    card.addEventListener('click', function () {
      document.querySelectorAll('.role-card').forEach(function (c) {
        c.classList.remove('selected');
      });
      this.classList.add('selected');
      var radio = this.querySelector('input[type="radio"]');
      if (radio) radio.checked = true;
    });
  });

  // ── FAQ accordion toggle (old /faq page) ──
  document.querySelectorAll('.faq-toggle').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var body = this.nextElementSibling;
      if (body) {
        body.classList.toggle('d-none');
        var icon = this.querySelector('.faq-chevron');
        if (icon) icon.classList.toggle('rotated');
      }
    });
  });
});

// ── Home page inline FAQ toggle ──
function toggleFaq(el) {
  var answer = el.nextElementSibling;
  var toggle = el.querySelector('.faq-toggle');
  if (answer && answer.classList.contains('faq-answer')) answer.classList.toggle('open');
  if (toggle) toggle.classList.toggle('open');
}
