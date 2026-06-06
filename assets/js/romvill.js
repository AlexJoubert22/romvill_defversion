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

    // ─── Hero Slideshow + Ken Burns + Parallax ───
    var slides = document.querySelectorAll('#hero-slideshow .hero-slide');
    function ensureBg(el) {
        if (el && el.dataset.bg && !el.style.backgroundImage) {
            el.style.backgroundImage = el.dataset.bg;
        }
    }
    // Ken Burns: zoom lento 1.0 → 1.08; duración distinta por slide para no sincronizar.
    var kbDur = [22, 20, 24, 21];
    function kenBurns(el, i) {
        if (!el) return;
        el.style.animation = 'none';
        void el.offsetWidth; // reflow para reiniciar la animación desde scale(1.0)
        el.style.animation = 'kenburns ' + kbDur[i % kbDur.length] + 's ease-in-out forwards';
    }
    function kenBurnsStop(el) {
        if (!el) return;
        el.style.animation = 'none';
        el.style.transform = 'scale(1.0)';
    }
    if (slides.length > 1) {
        // Precarga el slide 2 DESPUÉS de la carga, para no competir con el LCP (fondo).
        window.addEventListener('load', function () {
            setTimeout(function () { ensureBg(slides[1]); }, 1200);
        });
        var current = 0;
        kenBurns(slides[0], 0); // arranca el zoom del primer slide
        setInterval(function () {
            slides[current].style.opacity = '0';
            kenBurnsStop(slides[current]);
            current = (current + 1) % slides.length;
            ensureBg(slides[current]);
            ensureBg(slides[(current + 1) % slides.length]); // precarga el siguiente
            slides[current].style.opacity = '1';
            kenBurns(slides[current], current); // reinicia el zoom desde 1.0
        }, 5000);
    }

    // ─── Parallax sutil del fondo del hero al hacer scroll ───
    var heroSS = document.getElementById('hero-slideshow');
    var heroEl = heroSS ? heroSS.closest('main') : null;
    if (heroSS && heroEl && !(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches)) {
        var pTicking = false;
        function parallax() {
            var y = window.pageYOffset || document.documentElement.scrollTop || 0;
            if (y < heroEl.offsetHeight) {
                heroSS.style.transform = 'translateY(' + (y * 0.3) + 'px)';
            }
            pTicking = false;
        }
        window.addEventListener('scroll', function () {
            if (!pTicking) { window.requestAnimationFrame(parallax); pTicking = true; }
        }, { passive: true });
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
