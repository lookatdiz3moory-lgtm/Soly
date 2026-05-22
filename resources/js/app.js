/**
 * Soly Clinic — app.js
 * Vanilla JS only. No jQuery. No Alpine (handled inline if needed).
 * Covers: Navbar, Animations, Counters, Carousel,
 *         FAQ Accordion, Scroll Top, Flash Dismiss, Lazy Images
 */

'use strict';

/* ─── Helpers ─────────────────────────────────────────────────── */
const $ = (sel, ctx = document) => ctx.querySelector(sel);
const $$ = (sel, ctx = document) => [...ctx.querySelectorAll(sel)];
const on = (el, ev, fn, opts) => el && el.addEventListener(ev, fn, opts);

function debounce(fn, ms = 150) {
    let t;
    return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
}

function getCsrf() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

/* ─── 1. NAVBAR ───────────────────────────────────────────────── */
function initNavbar() {
    const navbar     = $('#navbar');
    const hamburger  = $('#hamburger');
    const mobileMenu = $('#mobileMenu');
    if (!navbar) return;

    /* Scroll behaviour */
    const onScroll = debounce(() => {
        navbar.classList.toggle('scrolled', window.scrollY > 20);
    }, 50);
    on(window, 'scroll', onScroll, { passive: true });
    onScroll(); // run once on load

    /* Mobile toggle */
    if (hamburger && mobileMenu) {
        on(hamburger, 'click', () => {
            const open = hamburger.classList.toggle('is-active');
            mobileMenu.classList.toggle('is-open', open);
            hamburger.setAttribute('aria-expanded', String(open));
            mobileMenu.setAttribute('aria-hidden', String(!open));
            document.body.style.overflow = open ? 'hidden' : '';
        });

        /* Close on outside tap */
        on(document, 'click', (e) => {
            if (mobileMenu.classList.contains('is-open') && !navbar.contains(e.target)) {
                hamburger.classList.remove('is-active');
                mobileMenu.classList.remove('is-open');
                hamburger.setAttribute('aria-expanded', 'false');
                mobileMenu.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }
        });

        /* Close on Escape */
        on(document, 'keydown', (e) => {
            if (e.key === 'Escape' && mobileMenu.classList.contains('is-open')) {
                hamburger.click();
            }
        });
    }

    /* Active link highlighting */
    const currentPath = window.location.pathname;
    $$('.navbar__link, .navbar__mobile-link').forEach(link => {
        const href = link.getAttribute('href') ?? '';
        const isHome = href === '/' && currentPath === '/';
        const isActive = !isHome && href !== '/' && currentPath.startsWith(href);
        if (isHome || isActive) link.classList.add('navbar__link--active');
    });
}

/* ─── 2. INTERSECTION OBSERVER — Reveal Animations ───────────── */
function initRevealAnimations() {
    const items = $$('[data-animate]');
    if (!items.length) return;

    if (!('IntersectionObserver' in window)) {
        items.forEach(el => el.classList.add('is-visible'));
        return;
    }

    const obs = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            const el    = entry.target;
            const delay = parseInt(el.dataset.delay ?? '0', 10);
            setTimeout(() => el.classList.add('is-visible'), delay);
            obs.unobserve(el);
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -60px 0px' });

    items.forEach(el => obs.observe(el));
}

/* ─── 3. COUNTER ANIMATION ────────────────────────────────────── */
function animateCounter(el) {
    const rawTarget = el.dataset.count ?? el.textContent.replace(/\D/g, '');
    const target    = parseFloat(rawTarget);
    if (isNaN(target)) return;

    const isFloat    = el.dataset.float === 'true' || rawTarget.includes('.');
    const duration   = 1800;
    const startTime  = performance.now();

    const tick = (now) => {
        const elapsed  = now - startTime;
        const progress = Math.min(elapsed / duration, 1);
        // Ease out cubic
        const eased    = 1 - Math.pow(1 - progress, 3);
        const current  = eased * target;
        el.textContent = isFloat
            ? current.toFixed(1)
            : Math.round(current).toLocaleString();
        if (progress < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
}

function initCounters() {
    const counters = $$('[data-count]');
    if (!counters.length || !('IntersectionObserver' in window)) return;

    const obs = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            animateCounter(entry.target);
            obs.unobserve(entry.target);
        });
    }, { threshold: 0.5 });

    counters.forEach(el => obs.observe(el));
}

/* ─── 4. TESTIMONIALS CAROUSEL ───────────────────────────────── */
function initCarousel(carouselId) {
    const carousel = $(`#${carouselId}`);
    if (!carousel) return;

    const track   = $('#testimonialTrack', carousel);
    const slides  = $$('.testimonial-card', carousel);
    const prevBtn = $('#testimonialPrev', carousel);
    const nextBtn = $('#testimonialNext', carousel);
    const dots    = $$('.testimonials__dot', carousel);
    if (!track || slides.length < 2) return;

    let current   = 0;
    let autoTimer = null;

    function getVisible() {
        if (window.innerWidth < 640)  return 1;
        if (window.innerWidth < 1024) return 2;
        return 3;
    }

    function getMax() {
        return Math.max(0, slides.length - getVisible());
    }

    function goTo(idx) {
        const max    = getMax();
        current      = Math.max(0, Math.min(idx, max));
        const vis    = getVisible();
        const offset = (100 / vis) * current;
        track.style.transform = `translateX(-${offset}%)`;

        dots.forEach((d, i) => {
            d.classList.toggle('testimonials__dot--active', i === current);
            d.setAttribute('aria-selected', String(i === current));
        });
    }

    function next() { goTo(current + 1 > getMax() ? 0 : current + 1); }
    function prev() { goTo(current - 1 < 0 ? getMax() : current - 1); }

    function startAuto() { autoTimer = setInterval(next, 5000); }
    function stopAuto()  { clearInterval(autoTimer); }

    on(nextBtn, 'click', () => { stopAuto(); next(); startAuto(); });
    on(prevBtn, 'click', () => { stopAuto(); prev(); startAuto(); });
    dots.forEach((dot, i) => {
        on(dot, 'click', () => { stopAuto(); goTo(i); startAuto(); });
    });

    /* Touch/swipe */
    let touchX = 0;
    on(track, 'touchstart', e => { touchX = e.touches[0].clientX; stopAuto(); }, { passive: true });
    on(track, 'touchend',   e => {
        const diff = touchX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 40) diff > 0 ? next() : prev();
        startAuto();
    });

    /* Pause on hover */
    on(carousel, 'mouseenter', stopAuto);
    on(carousel, 'mouseleave', startAuto);

    on(window, 'resize', debounce(() => goTo(current), 200));

    startAuto();
}

/* ─── 5. FAQ ACCORDION ────────────────────────────────────────── */
function initFaqAccordion() {
    $$('.faq__item').forEach(item => {
        const question = $('.faq__question', item);
        const answer   = $('.faq__answer', item);
        if (!question || !answer) return;

        on(question, 'click', () => {
            const isOpen = item.classList.contains('is-open');

            /* Close all */
            $$('.faq__item.is-open').forEach(other => {
                other.classList.remove('is-open');
                const ans = $('.faq__answer', other);
                if (ans) ans.style.maxHeight = '0';
            });

            /* Toggle clicked */
            if (!isOpen) {
                item.classList.add('is-open');
                answer.style.maxHeight = answer.scrollHeight + 'px';
                question.setAttribute('aria-expanded', 'true');
            } else {
                question.setAttribute('aria-expanded', 'false');
            }
        });
    });
}

/* ─── 6. SCROLL TO TOP ────────────────────────────────────────── */
function initScrollTop() {
    const btn = $('#scrollTop');
    if (!btn) return;

    const onScroll = debounce(() => {
        btn.classList.toggle('is-visible', window.scrollY > 400);
    }, 100);
    on(window, 'scroll', onScroll, { passive: true });
    on(btn, 'click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
}

/* ─── 7. FLASH MESSAGE AUTO-DISMISS ──────────────────────────── */
function initFlashMessages() {
    $$('.flash').forEach(flash => {
        setTimeout(() => {
            flash.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            flash.style.opacity    = '0';
            flash.style.transform  = 'translateX(110%)';
            setTimeout(() => flash.remove(), 400);
        }, 5000);
    });
}

/* ─── 8. LAZY LOADING ─────────────────────────────────────────── */
function initLazyImages() {
    if (!('IntersectionObserver' in window)) return;

    const obs = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            const img = entry.target;
            if (img.dataset.src) {
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                img.classList.add('is-loaded');
            }
            obs.unobserve(img);
        });
    }, { rootMargin: '200px' });

    $$('img[data-src]').forEach(img => obs.observe(img));
}

/* ─── 9. BEFORE/AFTER SLIDER ──────────────────────────────────── */
function initBeforeAfterSliders() {
    $$('.ba-slider').forEach(slider => {
        const handle  = $('.ba-handle', slider);
        const overlay = $('.ba-overlay', slider);
        if (!handle || !overlay) return;

        let active = false;

        function setPos(clientX) {
            const rect = slider.getBoundingClientRect();
            const pct  = Math.max(0, Math.min(100, ((clientX - rect.left) / rect.width) * 100));
            overlay.style.width = pct + '%';
            handle.style.left   = pct + '%';
        }

        on(handle, 'mousedown',  () => { active = true; });
        on(document,'mouseup',   () => { active = false; });
        on(document,'mousemove', e  => { if (active) setPos(e.clientX); });
        on(handle, 'touchstart', () => { active = true; }, { passive: true });
        on(document,'touchend',  () => { active = false; });
        on(document,'touchmove', e  => { if (active) setPos(e.touches[0].clientX); }, { passive: true });
    });
}



/* ─── 12. TOAST NOTIFICATION ──────────────────────────────────── */
function showToast(message, type = 'info', duration = 4000) {
    let container = $('.flash-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'flash-container';
        document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    toast.className = `flash flash--${type}`;
    toast.innerHTML = `<span>${message}</span><button class="flash__close" onclick="this.parentElement.remove()">×</button>`;
    container.appendChild(toast);
    setTimeout(() => {
        toast.style.transition = 'opacity 0.35s, transform 0.35s';
        toast.style.opacity    = '0';
        toast.style.transform  = 'translateX(110%)';
        setTimeout(() => toast.remove(), 350);
    }, duration);
}

/* ─── 13. GOOGLE MAPS LAZY EMBED ──────────────────────────────── */
function initMapEmbed() {
    const placeholder = $('.map-embed-placeholder');
    if (!placeholder) return;

    const obs = new IntersectionObserver(entries => {
        if (!entries[0].isIntersecting) return;
        const src = placeholder.dataset.src;
        if (!src) return;
        const iframe = document.createElement('iframe');
        iframe.src         = src;
        iframe.allowFullscreen = true;
        iframe.loading     = 'lazy';
        iframe.referrerPolicy = 'no-referrer-when-downgrade';
        iframe.style.cssText   = 'width:100%;min-height:380px;border:0;border-radius:inherit;display:block';
        iframe.title       = 'Soly Clinic location on Google Maps';
        placeholder.replaceWith(iframe);
        obs.disconnect();
    }, { rootMargin: '300px' });

    obs.observe(placeholder);
}

/* ─── 14. SMOOTH ANCHOR SCROLLING ─────────────────────────────── */
function initAnchorScrolling() {
    $$('a[href^="#"]').forEach(link => {
        on(link, 'click', e => {
            const id  = link.getAttribute('href').slice(1);
            const target = document.getElementById(id);
            if (!target) return;
            e.preventDefault();
            const navHeight = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--navbar-total')) || 108;
            const top = target.getBoundingClientRect().top + window.scrollY - navHeight - 16;
            window.scrollTo({ top, behavior: 'smooth' });
            history.pushState(null, '', `#${id}`);
        });
    });
}


/* ─── INIT ALL ────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    initNavbar();
    initRevealAnimations();
    initCounters();
    initCarousel('testimonialCarousel');
    initFaqAccordion();
    initScrollTop();
    initFlashMessages();
    initLazyImages();
    initBeforeAfterSliders();
    initMapEmbed();
    initAnchorScrolling();
});

/* Expose utility globally for inline usage */
window.SolyClinic = { showToast, getCsrf };
