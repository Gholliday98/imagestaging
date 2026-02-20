/* ============================================================
   Plastic-Craft Industries — Page Interactivity
   Tabs, Carousel, Mobile Nav
   ============================================================ */

(function () {
  'use strict';

  /* ----------------------------------------------------------
     APPLICATION TABS
     ---------------------------------------------------------- */
  const tabs = document.querySelectorAll('.applications__tab');
  const panels = document.querySelectorAll('.applications__panel');

  tabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
      // Deactivate all
      tabs.forEach(function (t) {
        t.classList.remove('active');
        t.setAttribute('aria-selected', 'false');
      });
      panels.forEach(function (p) {
        p.classList.remove('active');
      });

      // Activate clicked
      tab.classList.add('active');
      tab.setAttribute('aria-selected', 'true');

      var targetId = tab.getAttribute('aria-controls');
      var target = document.getElementById(targetId);
      if (target) {
        target.classList.add('active');
      }
    });
  });

  /* ----------------------------------------------------------
     ADVANTAGES CAROUSEL
     ---------------------------------------------------------- */
  var track = document.getElementById('advantagesTrack');
  var dotsContainer = document.getElementById('advantagesDots');
  var prevBtn = document.querySelector('.advantages__arrow--prev');
  var nextBtn = document.querySelector('.advantages__arrow--next');

  if (track && dotsContainer) {
    var cards = track.querySelectorAll('.advantage-card');
    var totalCards = cards.length;
    var currentPage = 0;

    // Determine visible cards based on viewport
    function getVisibleCount() {
      if (window.innerWidth <= 768) return 1;
      if (window.innerWidth <= 1024) return 2;
      return 3;
    }

    function getTotalPages() {
      var visible = getVisibleCount();
      return Math.ceil(totalCards / visible);
    }

    // Build dots
    function buildDots() {
      dotsContainer.innerHTML = '';
      var pages = getTotalPages();
      for (var i = 0; i < pages; i++) {
        var dot = document.createElement('button');
        dot.className = 'advantages__dot' + (i === currentPage ? ' active' : '');
        dot.setAttribute('aria-label', 'Go to slide group ' + (i + 1));
        dot.dataset.page = i;
        dot.addEventListener('click', function () {
          goToPage(parseInt(this.dataset.page, 10));
        });
        dotsContainer.appendChild(dot);
      }
    }

    // Navigate to page
    function goToPage(page) {
      var pages = getTotalPages();
      if (page < 0) page = pages - 1;
      if (page >= pages) page = 0;
      currentPage = page;

      var visible = getVisibleCount();
      var cardWidth = cards[0].offsetWidth;
      var gap = parseInt(getComputedStyle(track).gap, 10) || 24;
      var offset = currentPage * visible * (cardWidth + gap);

      track.style.transform = 'translateX(-' + offset + 'px)';

      // Update dots
      var dots = dotsContainer.querySelectorAll('.advantages__dot');
      dots.forEach(function (d, i) {
        d.classList.toggle('active', i === currentPage);
      });
    }

    // Arrow listeners
    if (prevBtn) {
      prevBtn.addEventListener('click', function () {
        goToPage(currentPage - 1);
      });
    }
    if (nextBtn) {
      nextBtn.addEventListener('click', function () {
        goToPage(currentPage + 1);
      });
    }

    // Init
    buildDots();
    goToPage(0);

    // Rebuild on resize
    var resizeTimer;
    window.addEventListener('resize', function () {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(function () {
        buildDots();
        goToPage(0);
      }, 200);
    });
  }

  /* ----------------------------------------------------------
     MOBILE NAV TOGGLE
     ---------------------------------------------------------- */
  var navToggle = document.querySelector('.nav-toggle');
  var mainNav = document.getElementById('mainNav');

  if (navToggle && mainNav) {
    navToggle.addEventListener('click', function () {
      var isOpen = mainNav.classList.toggle('open');
      navToggle.setAttribute('aria-expanded', isOpen);
    });
  }

})();
