/* Studio Volume — interactions
   nav state · scroll reveals · cursor dot · parallax · burger · theme toggle
--------------------------------------------------------------- */

(() => {
    'use strict';

    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ------------------------------------------------------- theme toggle */

    const themeToggle = document.querySelector('.theme-toggle');
    const html = document.documentElement;

    // Initialize theme — deep (dark) is the default. Light only when explicitly saved.
    const initTheme = () => {
        const savedTheme = localStorage.getItem('theme');

        if (savedTheme === 'dark' || savedTheme === 'light') {
            html.setAttribute('data-theme', savedTheme);
            updateToggleState(savedTheme === 'dark');
        } else {
            // First visit / no preference → deep dark sunset theme
            html.setAttribute('data-theme', 'dark');
            updateToggleState(true);
            localStorage.setItem('theme', 'dark');
        }
    };

    const updateToggleState = (isDark) => {
        if (!themeToggle) return;
        themeToggle.setAttribute('aria-pressed', isDark);
        themeToggle.setAttribute('aria-label', isDark ? 'Switch to light mode' : 'Switch to dark mode');
    };

    const toggleTheme = () => {
        const isDark = html.getAttribute('data-theme') === 'dark';
        const newTheme = isDark ? 'light' : 'dark';

        if (prefersReduced) {
            // Instant switch for reduced motion preference
            html.setAttribute('data-theme', newTheme);
            updateToggleState(newTheme === 'dark');
            localStorage.setItem('theme', newTheme);
            return;
        }

        // Create flash overlay for sunset/sunrise effect
        const flash = document.createElement('div');
        flash.className = 'theme-flash';
        document.body.appendChild(flash);

        // Add transitioning class for smooth color transitions
        document.body.classList.add('theme-transitioning');

        // Trigger the theme change
        setTimeout(() => {
            html.setAttribute('data-theme', newTheme);
            updateToggleState(newTheme === 'dark');
            localStorage.setItem('theme', newTheme);
        }, 100);

        // Clean up after transition
        setTimeout(() => {
            document.body.classList.remove('theme-transitioning');
            flash.remove();
        }, 900);
    };

    if (themeToggle) {
        themeToggle.addEventListener('click', toggleTheme);

        // Keyboard support
        themeToggle.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                toggleTheme();
            }
        });
    }

    initTheme();

    /* ------------------------------------------------------------ nav state */

    const nav = document.querySelector('.nav');

    const onScroll = () => {
        if (nav) nav.classList.toggle('scrolled', window.scrollY > 40);
    };

    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });

    /* ----------------------------------------------------------- active link */

    const links = [...document.querySelectorAll('.nav-links a')].filter(a => !a.classList.contains('nav-cta'));
    const path = window.location.pathname.replace(/\/+$/, '') || '/';

    links.forEach(a => {
        const href = (a.getAttribute('href') || '').replace(/\/+$/, '') || '/';
        a.classList.toggle('active', href === path);
    });

    /* -------------------------------------------------------------- burger */

    const burger = document.querySelector('.burger');
    const menu = document.querySelector('.mobile-menu');

    if (burger && menu) {
        const closeMenu = () => {
            burger.classList.remove('open');
            menu.classList.remove('open');
            document.body.style.overflow = '';
        };

        burger.addEventListener('click', () => {
            const open = !menu.classList.contains('open');
            burger.classList.toggle('open', open);
            menu.classList.toggle('open', open);
            document.body.style.overflow = open ? 'hidden' : '';
        });

        menu.querySelectorAll('a').forEach(a => a.addEventListener('click', closeMenu));
        window.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeMenu();
        });
    }

    /* -------------------------------------------------------- scroll reveals */

    const revealEls = [...document.querySelectorAll('.rv, .rv-img')];

    if (revealEls.length) {
        if (prefersReduced || !('IntersectionObserver' in window)) {
            revealEls.forEach(el => el.classList.add('in'));
        } else {
            const io = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('in');
                        io.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -6% 0px' });

            revealEls.forEach(el => io.observe(el));
        }
    }

    /* --------------------------------------------------------- statement text */

    const statement = document.querySelector('.statement');

    if (statement) {
        if (prefersReduced || !('IntersectionObserver' in window)) {
            statement.classList.add('visible');
        } else {
            const so = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        so.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.35 });

            so.observe(statement);
        }
    }

    /* ---------------------------------------------------------- parallax raf */

    const parallaxEls = [...document.querySelectorAll('[data-parallax]')]
        .map(el => ({ el, speed: parseFloat(el.dataset.parallax) || 0.08 }));

    if (parallaxEls.length && !prefersReduced) {
        let ticking = false;

        const update = () => {
            const vh = window.innerHeight;

            parallaxEls.forEach(({ el, speed }) => {
                const rect = el.getBoundingClientRect();
                const center = rect.top + rect.height / 2 - vh / 2;
                el.style.transform = `translate3d(0, ${(center * speed).toFixed(2)}px, 0)`;
            });

            ticking = false;
        };

        const requestTick = () => {
            if (!ticking) {
                ticking = true;
                requestAnimationFrame(update);
            }
        };

        requestTick();
        window.addEventListener('scroll', requestTick, { passive: true });
        window.addEventListener('resize', requestTick);
    }

    /* ----------------------------------------------------------- cursor dot */

    const dot = document.querySelector('.cursor-dot');
    const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

    if (dot && finePointer && !prefersReduced) {
        let tx = 0, ty = 0, x = 0, y = 0, raf = null;

        const loop = () => {
            x += (tx - x) * 0.2;
            y += (ty - y) * 0.2;
            dot.style.transform = `translate3d(${x.toFixed(2)}px, ${y.toFixed(2)}px, 0)`;
            raf = requestAnimationFrame(loop);
        };

        window.addEventListener('mousemove', e => {
            tx = e.clientX;
            ty = e.clientY;
            dot.classList.add('on');
            if (raf === null) loop();
        }, { passive: true });

        document.addEventListener('mouseleave', () => {
            dot.classList.remove('on');
            if (raf !== null) {
                cancelAnimationFrame(raf);
                raf = null;
            }
        });

        const growTargets = document.querySelectorAll('a, button, .work-card, input, select, textarea');
        growTargets.forEach(el => {
            el.addEventListener('mouseenter', () => dot.classList.add('grow'));
            el.addEventListener('mouseleave', () => dot.classList.remove('grow'));
        });
    }

    /* ----------------------------------------------------------- footer year */

    document.querySelectorAll('[data-year]').forEach(el => {
        el.textContent = new Date().getFullYear();
    });
})();

