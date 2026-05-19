/**
 * Soly Clinic — components.js  (Phase 2)
 * Loaded after app.js.
 * Handles: navbar scroll collapse, hero entrance, counters,
 *          testimonials carousel, FAQ accordion, scroll-top, lazy images.
 */

'use strict';

/* ─── Tiny helpers ────────────────────────────────────────────── */
const qs   = (s, c = document) => c.querySelector(s);
const qsa  = (s, c = document) => [...c.querySelectorAll(s)];
const on   = (el, ev, fn, opts) => el && el.addEventListener(ev, fn, opts);
const db   = (fn, ms = 100) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };

/* ─── 1. NAVBAR — scroll collapse + strip hide ────────────────── */
function initNavbar() {
  const navbar = qs('#navbar');
  if (!navbar) return;

  let lastY  = 0;
  let ticking = false;

  function update() {
    const y = window.scrollY;
    navbar.classList.toggle('is-scrolled', y > 40);
    // Update CSS variable for downstream sections
    document.documentElement.style.setProperty(
      '--navbar-total',
      navbar.classList.contains('is-scrolled') ? '64px' : '108px'
    );
    lastY   = y;
    ticking = false;
  }

  on(window, 'scroll', () => {
    if (!ticking) { requestAnimationFrame(update); ticking = true; }
  }, { passive: true });

  update(); // run once on load

  /* Mobile hamburger */
  const hamburger  = qs('#hamburger', navbar);
  const mobileMenu = qs('#mobileMenu', navbar);
  if (!hamburger || !mobileMenu) return;

  function closeMobile() {
    hamburger.classList.remove('is-active');
    mobileMenu.classList.remove('is-open');
    hamburger.setAttribute('aria-expanded', 'false');
    mobileMenu.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  on(hamburger, 'click', () => {
    const open = hamburger.classList.toggle('is-active');
    mobileMenu.classList.toggle('is-open', open);
    hamburger.setAttribute('aria-expanded', String(open));
    mobileMenu.setAttribute('aria-hidden', String(!open));
    document.body.style.overflow = open ? 'hidden' : '';
  });

  on(document, 'click', e => {
    if (mobileMenu.classList.contains('is-open') && !navbar.contains(e.target)) closeMobile();
  });
  on(document, 'keydown', e => { if (e.key === 'Escape') closeMobile(); });

  /* Mark active nav link */
  const path = window.location.pathname;
  qsa('.navbar__link, .navbar__mobile-link').forEach(link => {
    const href = link.getAttribute('href') || '';
    if (href === '/' ? path === '/' : href !== '/' && path.startsWith(href)) {
      link.classList.add('navbar__link--active');
    }
  });
}

/* ─── 2. REVEAL ANIMATIONS (IntersectionObserver) ─────────────── */
function initReveal() {
  const items = qsa('[data-animate]');
  if (!items.length) return;

  if (!('IntersectionObserver' in window)) {
    items.forEach(el => el.classList.add('is-visible'));
    return;
  }

  const io = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      const delay = parseInt(entry.target.dataset.delay || '0', 10);
      setTimeout(() => entry.target.classList.add('is-visible'), delay);
      io.unobserve(entry.target);
    });
  }, { threshold: 0.10, rootMargin: '0px 0px -56px 0px' });

  items.forEach(el => io.observe(el));
}

/* ─── 3. COUNTER ANIMATION ────────────────────────────────────── */
function animateCount(el) {
  const raw    = el.dataset.count || el.textContent.replace(/[^0-9.]/g, '');
  const target = parseFloat(raw);
  if (isNaN(target) || target === 0) return;

  const isFloat = raw.includes('.') || el.dataset.float === 'true';
  const ms      = 1900;
  const start   = performance.now();

  const step = now => {
    const prog = Math.min((now - start) / ms, 1);
    const eased = 1 - Math.pow(1 - prog, 3);   // ease-out-cubic
    el.textContent = isFloat
      ? (eased * target).toFixed(1)
      : Math.round(eased * target).toLocaleString();
    if (prog < 1) requestAnimationFrame(step);
  };
  requestAnimationFrame(step);
}

function initCounters() {
  const items = qsa('[data-count]');
  if (!items.length || !('IntersectionObserver' in window)) return;

  const io = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (!e.isIntersecting) return;
      animateCount(e.target);
      io.unobserve(e.target);
    });
  }, { threshold: 0.5 });

  items.forEach(el => io.observe(el));
}

/* ─── 4. HERO ENTRANCE — stagger children ────────────────────── */
function initHeroEntrance() {
  const hero = qs('.hero');
  if (!hero) return;

  // Elements already handled by [data-animate] + IntersectionObserver.
  // Also run counter immediately since hero is always in viewport.
  qsa('[data-count]', hero).forEach(el => {
    const io = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (!e.isIntersecting) return;
        // Short delay so the number reveals after fade-in
        setTimeout(() => animateCount(e.target), 400);
        io.unobserve(e.target);
      });
    }, { threshold: 0.5 });
    io.observe(el);
  });
}

/* ─── 5. TESTIMONIALS CAROUSEL ───────────────────────────────── */
function initCarousel() {
  const wrap    = qs('#testimonialCarousel');
  if (!wrap) return;

  const track   = qs('#testimonialTrack',  wrap);
  const slides  = qsa('.testimonial-card', wrap);
  const prevBtn = qs('#testimonialPrev',   wrap);
  const nextBtn = qs('#testimonialNext',   wrap);
  const dots    = qsa('.testimonials__dot', wrap);
  if (!track || slides.length < 2) return;

  let current = 0;
  let auto    = null;

  function visible() {
    const w = window.innerWidth;
    return w < 640 ? 1 : w < 1024 ? 2 : 3;
  }
  function maxIdx() { return Math.max(0, slides.length - visible()); }

  function goTo(idx) {
    current = Math.max(0, Math.min(idx, maxIdx()));
    const pct = (100 / visible()) * current;
    track.style.transform = `translateX(-${pct}%)`;

    dots.forEach((d, i) => {
      const active = i === current;
      d.classList.toggle('testimonials__dot--active', active);
      d.setAttribute('aria-selected', String(active));
    });
  }

  const next = () => goTo(current + 1 > maxIdx() ? 0 : current + 1);
  const prev = () => goTo(current - 1 < 0 ? maxIdx() : current - 1);

  const startAuto = () => { clearInterval(auto); auto = setInterval(next, 5200); };
  const stopAuto  = () => clearInterval(auto);

  on(nextBtn, 'click', () => { stopAuto(); next(); startAuto(); });
  on(prevBtn, 'click', () => { stopAuto(); prev(); startAuto(); });
  dots.forEach((d, i) => on(d, 'click', () => { stopAuto(); goTo(i); startAuto(); }));

  /* Swipe */
  let tx = 0;
  on(track, 'touchstart',  e => { tx = e.touches[0].clientX; stopAuto(); }, { passive: true });
  on(track, 'touchend',    e => {
    if (Math.abs(tx - e.changedTouches[0].clientX) > 42) {
      tx > e.changedTouches[0].clientX ? next() : prev();
    }
    startAuto();
  });

  on(wrap, 'mouseenter', stopAuto);
  on(wrap, 'mouseleave', startAuto);
  on(window, 'resize', db(() => goTo(current), 180));

  startAuto();
}

/* ─── 6. FAQ ACCORDION ────────────────────────────────────────── */
function initFaq() {
  qsa('.faq__item').forEach(item => {
    const q = qs('.faq__question', item);
    const a = qs('.faq__answer',   item);
    if (!q || !a) return;

    on(q, 'click', () => {
      const open = item.classList.contains('is-open');
      // Close all
      qsa('.faq__item.is-open').forEach(other => {
        other.classList.remove('is-open');
        const ans = qs('.faq__answer', other);
        if (ans) ans.style.maxHeight = '0';
        qs('.faq__question', other)?.setAttribute('aria-expanded', 'false');
      });
      if (!open) {
        item.classList.add('is-open');
        a.style.maxHeight = a.scrollHeight + 'px';
        q.setAttribute('aria-expanded', 'true');
      }
    });
  });
}

/* ─── 7. SCROLL TO TOP ────────────────────────────────────────── */
function initScrollTop() {
  const btn = qs('#scrollTop');
  if (!btn) return;
  on(window, 'scroll', db(() => btn.classList.toggle('is-visible', window.scrollY > 420), 80), { passive: true });
  on(btn, 'click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
}

/* ─── 8. FLASH AUTO-DISMISS ───────────────────────────────────── */
function initFlash() {
  qsa('.flash').forEach(el => {
    setTimeout(() => {
      Object.assign(el.style, { transition: 'opacity .38s, transform .38s', opacity: '0', transform: 'translateX(110%)' });
      setTimeout(() => el.remove(), 380);
    }, 5200);
  });
}

/* ─── 9. LAZY IMAGES ──────────────────────────────────────────── */
function initLazy() {
  if (!('IntersectionObserver' in window)) return;
  const io = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (!e.isIntersecting) return;
      const img = e.target;
      if (img.dataset.src) { img.src = img.dataset.src; img.removeAttribute('data-src'); }
      io.unobserve(img);
    });
  }, { rootMargin: '220px' });
  qsa('img[data-src]').forEach(img => io.observe(img));
}

/* ─── 10. SMOOTH ANCHOR SCROLL ────────────────────────────────── */
function initAnchorScroll() {
  qsa('a[href^="#"]').forEach(link => {
    on(link, 'click', e => {
      const id = link.getAttribute('href').slice(1);
      const target = document.getElementById(id);
      if (!target) return;
      e.preventDefault();
      const navH = parseInt(
        getComputedStyle(document.documentElement).getPropertyValue('--navbar-total') || '108'
      );
      window.scrollTo({ top: target.getBoundingClientRect().top + window.scrollY - navH - 16, behavior: 'smooth' });
    });
  });
}

/* ─── 11. GOOGLE MAPS LAZY EMBED ──────────────────────────────── */
function initMapEmbed() {
  const ph = qs('.map-embed-placeholder');
  if (!ph) return;
  const io = new IntersectionObserver(entries => {
    if (!entries[0].isIntersecting) return;
    const src = ph.dataset.src;
    if (!src) return;
    const iframe = Object.assign(document.createElement('iframe'), {
      src, allowFullscreen: true, loading: 'lazy',
      referrerPolicy: 'no-referrer-when-downgrade',
      title: 'Soly Clinic on Google Maps',
    });
    iframe.style.cssText = 'width:100%;min-height:380px;border:0;border-radius:inherit;display:block';
    ph.replaceWith(iframe);
    io.disconnect();
  }, { rootMargin: '320px' });
  io.observe(ph);
}

/* ─── INIT ────────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  initNavbar();
  initReveal();
  initCounters();
  initHeroEntrance();
  initCarousel();
  initFaq();
  initScrollTop();
  initFlash();
  initLazy();
  initAnchorScroll();
  initMapEmbed();
});
