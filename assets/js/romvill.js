/**
 * Romvill Theme JavaScript
 * Navbar scroll, hero slideshow, stat counters, city modals, mobile menu, contact form toggles
 */

(function () {
    'use strict';

    // ─── Navbar shadow on scroll ───────────────────────────
    const nav = document.querySelector('nav');
    if (nav) {
        window.addEventListener('scroll', function () {
            nav.classList.toggle('shadow-md', window.scrollY > 50);
        });
    }

    // ─── Mobile Menu ───────────────────────────────────────
    const menuToggle = document.getElementById('mobile-menu-toggle');
    const menuClose = document.getElementById('mobile-menu-close');
    const mobileMenu = document.getElementById('mobile-menu');

    if (menuToggle && mobileMenu) {
        menuToggle.addEventListener('click', function () {
            mobileMenu.classList.add('active');
            mobileMenu.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        });
    }
    if (menuClose && mobileMenu) {
        menuClose.addEventListener('click', function () {
            mobileMenu.classList.remove('active');
            mobileMenu.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        });
    }

    // ─── Hero Slideshow ────────────────────────────────────
    var slides = document.querySelectorAll('#hero-slideshow .hero-slide');
    if (slides.length > 1) {
        var current = 0;
        setInterval(function () {
            slides[current].style.opacity = '0';
            current = (current + 1) % slides.length;
            slides[current].style.opacity = '1';
        }, 5000);
    }

    // ─── Stat Card Entrance + Count-Up ─────────────────────
    var statCards = document.querySelectorAll('.stat-card');

    function animateCount(el) {
        var target = parseInt(el.dataset.target, 10);
        var prefix = el.dataset.prefix || '';
        var suffix = el.dataset.suffix || '';
        var duration = 1400;
        var start = performance.now();

        function step(now) {
            var elapsed = Math.min((now - start) / duration, 1);
            var ease = 1 - Math.pow(1 - elapsed, 3);
            var current = Math.round(ease * target);
            el.textContent = prefix + current + suffix;
            if (elapsed < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    }

    if (statCards.length && 'IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    var card = entry.target;
                    var idx = parseInt(card.id.replace('stat-', ''), 10) - 1;
                    setTimeout(function () {
                        card.classList.add('visible');
                        var numEl = card.querySelector('.stat-number');
                        if (numEl) animateCount(numEl);
                    }, idx * 150);
                    observer.unobserve(card);
                }
            });
        }, { threshold: 0.2 });

        statCards.forEach(function (card) {
            observer.observe(card);
        });
    }

    // ─── City Modals ───────────────────────────────────────
    window.openCityModal = function (city) {
        document.querySelectorAll('.city-modal').forEach(function (m) {
            m.classList.add('hidden');
        });
        var modal = document.getElementById('modal-' + city);
        var backdrop = document.getElementById('city-modal-backdrop');
        if (modal && backdrop) {
            backdrop.classList.remove('hidden');
            backdrop.classList.add('flex');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    };

    window.closeCityModal = function () {
        var backdrop = document.getElementById('city-modal-backdrop');
        if (backdrop) {
            backdrop.classList.add('hidden');
            backdrop.classList.remove('flex');
        }
        document.querySelectorAll('.city-modal').forEach(function (m) {
            m.classList.add('hidden');
        });
        document.body.style.overflow = '';
    };

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            window.closeCityModal();
        }
    });

    // ─── Dark Mode Toggle ──────────────────────────────────
    function setTheme(dark) {
        if (dark) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('romvill_theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('romvill_theme', 'light');
        }
    }

    function handleDarkToggle() {
        setTheme(!document.documentElement.classList.contains('dark'));
    }

    var dmToggle = document.getElementById('dark-mode-toggle');
    var dmToggleMobile = document.getElementById('dark-mode-toggle-mobile');
    if (dmToggle) dmToggle.addEventListener('click', handleDarkToggle);
    if (dmToggleMobile) dmToggleMobile.addEventListener('click', handleDarkToggle);

    // ─── Contact Form Toggle Fields ────────────────────────
    var zonaSelect = document.getElementById('zona');
    var otraZonaContainer = document.getElementById('otra-zona-container');

    if (zonaSelect && otraZonaContainer) {
        zonaSelect.addEventListener('change', function (e) {
            if (e.target.value === 'internacional') {
                otraZonaContainer.classList.remove('hidden');
            } else {
                otraZonaContainer.classList.add('hidden');
            }
        });
    }

    var objetivoSelect = document.getElementById('objetivo');
    var otroContainer = document.getElementById('otro-objetivo-container');

    if (objetivoSelect && otroContainer) {
        objetivoSelect.addEventListener('change', function (e) {
            if (e.target.value === 'otro') {
                otroContainer.classList.remove('hidden');
            } else {
                otroContainer.classList.add('hidden');
            }
        });
    }

})();
