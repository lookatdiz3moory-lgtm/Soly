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

/* ─── 10. BOOKING FORM — Live Availability ────────────────────── */
function initBookingForm() {
    const form = $('#bookingForm');
    if (!form) return;

    const serviceEl = $('#service_id', form);
    const doctorSec = $('#doctorSection', form);
    const dateSec   = $('#dateSection', form);
    const slotSec   = $('#slotSection', form);
    const slotsGrid = $('#slotsGrid', form);
    const slotInput = $('#slot_start', form);
    const slotEnd   = $('#slot_end', form);
    const dateInput = $('#appointment_date', form);

    /* Utility */
    const show = el => el && (el.style.display = '');
    const hide = el => el && (el.style.display = 'none');
    const fmtTime = t => {
        const [h, m] = t.split(':').map(Number);
        return `${h % 12 || 12}:${String(m).padStart(2,'0')} ${h < 12 ? 'AM' : 'PM'}`;
    };

    /* Step 1 → Load doctors when service chosen */
    on(serviceEl, 'change', async () => {
        const id = serviceEl?.value;
        hide(doctorSec); hide(dateSec); hide(slotSec);
        if (!id) return;

        if (doctorSec) {
            doctorSec.innerHTML = '<p class="form-hint" style="padding:12px">Loading doctors…</p>';
            show(doctorSec);
        }

        try {
            const res  = await fetch(`/api/doctors-by-service/${id}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            if (!data.success || !data.doctors?.length) {
                if (doctorSec) doctorSec.innerHTML = '<p class="form-error" style="padding:12px">No doctors available for this service.</p>';
                return;
            }
            renderDoctorCards(data.doctors);
        } catch {
            if (doctorSec) doctorSec.innerHTML = '<p class="form-error" style="padding:12px">Failed to load doctors.</p>';
        }
    });

    function renderDoctorCards(doctors) {
        if (!doctorSec) return;
        const html = `
          <div class="form-group">
            <label class="form-label form-label--required">Select Doctor</label>
            <div class="doctor-radio-grid">
              ${doctors.map(d => `
                <label class="doctor-radio">
                  <input type="radio" name="doctor_id" value="${d.id}" data-duration="${d.slot_duration ?? 30}">
                  <div class="doctor-radio__inner">
                    <div class="doctor-radio__avatar">${d.name.charAt(0)}</div>
                    <div>
                      <div class="doctor-radio__name">${d.name}</div>
                      <div class="doctor-radio__spec">${d.specialty ?? ''}</div>
                    </div>
                  </div>
                </label>`).join('')}
            </div>
          </div>`;
        doctorSec.innerHTML = html;

        /* Auto-select single doctor */
        if (doctors.length === 1) {
            const radio = $('input[type="radio"]', doctorSec);
            if (radio) { radio.checked = true; onDoctorSelected(); }
        }

        on(doctorSec, 'change', e => {
            if (e.target.name === 'doctor_id') onDoctorSelected();
        });
    }

    function onDoctorSelected() {
        show(dateSec);
        hide(slotSec);
        if (dateInput) {
            const today   = new Date();
            const maxDate = new Date(); maxDate.setDate(today.getDate() + 60);
            dateInput.min = toISO(today);
            dateInput.max = toISO(maxDate);
        }
    }

    /* Step 2 → Load slots when date chosen */
    on(dateInput, 'change', loadSlots);

    async function loadSlots() {
        const doctorId = $('input[name="doctor_id"]:checked', form)?.value;
        const date     = dateInput?.value;
        if (!doctorId || !date) return;

        show(slotSec);
        if (slotsGrid) {
            slotsGrid.innerHTML = '<div class="slots-loading">Loading available times…</div>';
        }
        if (slotInput) slotInput.value = '';
        if (slotEnd)   slotEnd.value   = '';

        try {
            const res  = await fetch(`/api/available-slots?doctor_id=${doctorId}&date=${date}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            if (!data.success || !data.slots?.length) {
                if (slotsGrid) slotsGrid.innerHTML = '<p class="form-hint" style="padding:8px">No available slots on this date.</p>';
                return;
            }
            renderSlots(data.slots);
        } catch {
            if (slotsGrid) slotsGrid.innerHTML = '<p class="form-error" style="padding:8px">Could not load slots.</p>';
        }
    }

    function renderSlots(slots) {
        if (!slotsGrid) return;
        slotsGrid.innerHTML = slots.map(s =>
            `<button type="button" class="time-slot" data-start="${s.start}" data-end="${s.end}">
                ${fmtTime(s.start)}
             </button>`
        ).join('');

        $$('.time-slot', slotsGrid).forEach(btn => {
            on(btn, 'click', () => {
                $$('.time-slot', slotsGrid).forEach(b => b.classList.remove('is-selected'));
                btn.classList.add('is-selected');
                if (slotInput) slotInput.value = btn.dataset.start;
                if (slotEnd)   slotEnd.value   = btn.dataset.end;
            });
        });
    }

    /* Step 3 → Submit */
    on(form, 'submit', async e => {
        e.preventDefault();
        const submitBtn = $('[type="submit"]', form);
        if (!validateBookingForm(form)) return;

        if (submitBtn) { submitBtn.classList.add('btn--loading'); submitBtn.disabled = true; }

        try {
            const payload = Object.fromEntries(new FormData(form));
            const res  = await fetch('/api/booking', {
                method: 'POST',
                headers: {
                    'Content-Type':  'application/json',
                    'X-CSRF-TOKEN':  getCsrf(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(payload),
            });
            const data = await res.json();
            if (data.success) {
                window.location.href = data.redirect ?? `/booking/confirm/${data.reference}`;
            } else {
                showFormErrors(form, data.errors ?? {});
                if (data.message) showToast(data.message, 'error');
            }
        } catch {
            showToast('A network error occurred. Please try again.', 'error');
        } finally {
            if (submitBtn) { submitBtn.classList.remove('btn--loading'); submitBtn.disabled = false; }
        }
    });

    /* Helpers */
    function toISO(d) { return d.toISOString().split('T')[0]; }
}

/* ─── 11. FORM VALIDATION ─────────────────────────────────────── */
function validateBookingForm(form) {
    let valid = true;
    clearFormErrors(form);

    [
        { name: 'patient_name',      msg: 'Full name is required.' },
        { name: 'patient_phone',     msg: 'Phone number is required.' },
        { name: 'service_id',        msg: 'Please select a service.' },
        { name: 'appointment_date',  msg: 'Please select a date.' },
        { name: 'slot_start',        msg: 'Please select a time slot.' },
    ].forEach(({ name, msg }) => {
        const el = form.querySelector(`[name="${name}"]`);
        if (!el || !el.value.trim()) { showFieldError(form, name, msg); valid = false; }
    });

    /* Validate doctor selection (radio group) */
    const doctorPicked = form.querySelector('[name="doctor_id"]:checked');
    if (!doctorPicked) {
        const wrap = form.querySelector('[name="doctor_id"]')?.closest('.form-group');
        if (wrap) {
            const err = document.createElement('span');
            err.className = 'form-error';
            err.textContent = 'Please select a doctor.';
            wrap.appendChild(err);
        }
        valid = false;
    }

    /* Egyptian phone */
    const phone = form.querySelector('[name="patient_phone"]')?.value ?? '';
    if (phone && !/^(\+2)?01[0125]\d{8}$/.test(phone.replace(/[\s\-]/g, ''))) {
        showFieldError(form, 'patient_phone', 'Please enter a valid Egyptian phone number.');
        valid = false;
    }

    /* Scroll to first error */
    if (!valid) {
        form.querySelector('.form-error')?.closest('.form-group')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    return valid;
}

function showFieldError(form, name, message) {
    const el = form.querySelector(`[name="${name}"]`);
    if (!el) return;
    el.classList.add('form-control--error');
    const group = el.closest('.form-group');
    if (group && !group.querySelector('.form-error')) {
        const span = document.createElement('span');
        span.className   = 'form-error';
        span.textContent = message;
        group.appendChild(span);
    }
}

function showFormErrors(form, errors) {
    Object.entries(errors).forEach(([name, msg]) => showFieldError(form, name, Array.isArray(msg) ? msg[0] : msg));
}

function clearFormErrors(form) {
    form.querySelectorAll('.form-control--error').forEach(el => el.classList.remove('form-control--error'));
    form.querySelectorAll('.form-error').forEach(el => el.remove());
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

/* ─── 15. BOOKING EXTRA STYLES (injected once) ────────────────── */
function injectBookingStyles() {
    if (document.getElementById('booking-dyn-styles')) return;
    const style = document.createElement('style');
    style.id = 'booking-dyn-styles';
    style.textContent = `
        .doctor-radio-grid { display: flex; flex-direction: column; gap: 10px; }
        .doctor-radio { display: block; cursor: pointer; }
        .doctor-radio input { display: none; }
        .doctor-radio__inner {
            display: flex; align-items: center; gap: 14px;
            padding: 14px 16px;
            border: 1.5px solid var(--gray-300);
            border-radius: var(--r);
            transition: var(--t-fast);
        }
        .doctor-radio input:checked + .doctor-radio__inner {
            border-color: var(--gold);
            background: rgba(201,168,76,0.05);
        }
        .doctor-radio__inner:hover { border-color: var(--gold); }
        .doctor-radio__avatar {
            width: 44px; height: 44px; border-radius: 50%;
            background: var(--navy); color: var(--gold);
            display: flex; align-items: center; justify-content: center;
            font-family: var(--font-serif); font-size: 1.125rem; font-weight: 700;
            flex-shrink: 0;
        }
        .doctor-radio__name  { font-size: 14px; font-weight: 600; color: var(--navy); }
        .doctor-radio__spec  { font-size: 12px; color: var(--text-muted); margin-top: 2px; }

        .time-slots-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
            gap: 8px;
        }
        .time-slot {
            padding: 9px 6px;
            text-align: center;
            font-size: 13px;
            font-family: var(--font-mono);
            border: 1.5px solid var(--gray-300);
            border-radius: var(--r-sm);
            cursor: pointer;
            transition: var(--t-fast);
            background: var(--white);
            color: var(--text-mid);
        }
        .time-slot:hover { border-color: var(--gold); color: var(--gold-dark); }
        .time-slot.is-selected {
            background: var(--navy); border-color: var(--navy);
            color: var(--gold); font-weight: 700;
        }
        .time-slot[disabled] { opacity: 0.35; cursor: not-allowed; }
        .slots-loading {
            text-align: center; padding: 24px; font-size: 14px;
            color: var(--text-muted);
        }
    `;
    document.head.appendChild(style);
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
    initBookingForm();
    injectBookingStyles();
    initMapEmbed();
    initAnchorScrolling();
});

/* Expose utility globally for inline usage */
window.SolyClinic = { showToast, getCsrf };
