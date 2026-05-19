/**
 * Soly Clinic — booking.js  (Phase 4)
 * Multi-step booking form with live slot availability.
 * Loaded only on the booking page via @stack('scripts').
 */

'use strict';

/* ── Tiny helpers ─────────────────────────────────────────────── */
const qs  = (s, c = document) => c.querySelector(s);
const qsa = (s, c = document) => [...c.querySelectorAll(s)];
const on  = (el, ev, fn, opts) => el && el.addEventListener(ev, fn, opts);
const val = el => el ? el.value.trim() : '';
const getCsrf = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

/* ── State ────────────────────────────────────────────────────── */
const state = {
  step:      1,          // current visible panel (1–4)
  serviceId: null,
  doctorId:  null,
  date:      null,
  slotStart: null,
  slotEnd:   null,
  /* Displayed labels for the sidebar summary */
  labels: {
    service:  '',
    doctor:   '',
    date:     '',
    slot:     '',
    price:    '',
  },
};

/* ─────────────────────────────────────────────────────────────────
   INIT — called once DOM is ready
   ───────────────────────────────────────────────────────────────── */
function initBookingForm() {
  const form = qs('#bookingForm');
  if (!form) return;

  /* Wire step navigation */
  qsa('[data-next]', form).forEach(btn => on(btn, 'click', () => goNext(form)));
  qsa('[data-back]', form).forEach(btn => on(btn, 'click', () => goBack(form)));

  /* Service selector */
  const serviceSelect = qs('#service_id', form);
  on(serviceSelect, 'change', () => onServiceChange(form, serviceSelect));

  /* Date picker */
  const dateInput = qs('#appointment_date', form);
  setupDatePicker(dateInput);
  on(dateInput, 'change', () => onDateChange(form, dateInput));

  /* Form submit */
  on(form, 'submit', e => { e.preventDefault(); submitBooking(form); });

  /* Notes character counter */
  initCharCounter(qs('#notes', form));

  /* Restore pre-selected values injected by PHP (data attributes on form) */
  const preServiceId = form.dataset.preServiceId;
  const preDoctorId  = form.dataset.preDoctorId;

  if (preServiceId && serviceSelect) {
    serviceSelect.value = preServiceId;
    serviceSelect.dispatchEvent(new Event('change'));

    // After doctors load, pre-select doctor
    if (preDoctorId) {
      form.dataset.pendingDoctorId = preDoctorId;
    }
  }

  updateSummary();
}

/* ─────────────────────────────────────────────────────────────────
   STEP NAVIGATION
   ───────────────────────────────────────────────────────────────── */
function goNext(form) {
  if (!validateCurrentStep(form)) return;
  setStep(form, state.step + 1);
}

function goBack(form) {
  setStep(form, state.step - 1);
}

function setStep(form, step) {
  const totalSteps = qsa('.booking-panel', form).length;
  step = Math.max(1, Math.min(step, totalSteps));
  state.step = step;

  /* Show/hide panels */
  qsa('.booking-panel', form).forEach((panel, i) => {
    panel.classList.toggle('is-active', i + 1 === step);
  });

  /* Update progress circles */
  qsa('.booking-step', form).forEach((el, i) => {
    el.classList.toggle('is-active', i + 1 === step);
    el.classList.toggle('is-done',   i + 1 <  step);
  });

  /* Scroll form top into view on mobile */
  form.closest('.booking-card')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

/* ─────────────────────────────────────────────────────────────────
   PER-STEP VALIDATION
   ───────────────────────────────────────────────────────────────── */
function validateCurrentStep(form) {
  clearErrors(form);

  switch (state.step) {
    case 1: return validateStep1(form);
    case 2: return validateStep2(form);
    case 3: return validateStep3(form);
    default: return true;
  }
}

function validateStep1(form) {
  const serviceId = val(qs('#service_id', form));
  if (!serviceId) {
    showError(qs('#service_id', form), 'Please select a service.');
    return false;
  }
  const doctorPicked = qs('input[name="doctor_id"]:checked', form);
  if (!doctorPicked) {
    showGroupError(qs('#doctorGrid', form), 'Please select a doctor.');
    return false;
  }
  return true;
}

function validateStep2(form) {
  const date = val(qs('#appointment_date', form));
  if (!date) {
    showError(qs('#appointment_date', form), 'Please select a date.');
    return false;
  }
  if (!state.slotStart) {
    showGroupError(qs('#slotsGrid', form), 'Please select a time slot.');
    return false;
  }
  return true;
}

function validateStep3(form) {
  let ok = true;

  const nameEl  = qs('#patient_name', form);
  const phoneEl = qs('#patient_phone', form);

  if (!val(nameEl) || val(nameEl).length < 2) {
    showError(nameEl, 'Please enter your full name.');
    ok = false;
  }

  const phone = val(phoneEl).replace(/[\s\-\(\)]/g, '');
  if (!phone) {
    showError(phoneEl, 'A mobile number is required.');
    ok = false;
  } else if (!/^(\+2)?01[0125]\d{8}$/.test(phone)) {
    showError(phoneEl, 'Please enter a valid Egyptian mobile number (01x xxxx xxxx).');
    ok = false;
  }

  const emailEl = qs('#patient_email', form);
  if (val(emailEl) && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val(emailEl))) {
    showError(emailEl, 'Please enter a valid email address.');
    ok = false;
  }

  if (!qs('#agree_terms', form)?.checked) {
    showGroupError(qs('.booking-terms', form), 'You must agree to the terms to continue.');
    ok = false;
  }

  return ok;
}

/* ─────────────────────────────────────────────────────────────────
   SERVICE CHANGE → load doctors
   ───────────────────────────────────────────────────────────────── */
async function onServiceChange(form, serviceSelect) {
  const id = serviceSelect.value;

  /* Reset downstream state */
  state.serviceId = id || null;
  state.doctorId  = null;
  state.slotStart = null;
  state.slotEnd   = null;

  /* Update summary label */
  const opt = serviceSelect.options[serviceSelect.selectedIndex];
  state.labels.service = id ? opt.text.replace(/\s+—.*/, '') : '';
  state.labels.doctor  = '';
  state.labels.date    = '';
  state.labels.slot    = '';
  state.labels.price   = opt.dataset.price || '';
  updateSummary();

  const doctorGrid = qs('#doctorGrid', form);
  const doctorWrap = qs('#doctorSection', form);
  if (!doctorGrid || !doctorWrap) return;

  if (!id) {
    doctorGrid.innerHTML = '';
    doctorWrap.style.display = 'none';
    return;
  }

  doctorGrid.innerHTML = loadingHtml('Loading doctors…');
  doctorWrap.style.display = 'block';

  try {
    const res  = await apiFetch(`/api/doctors-by-service/${id}`);
    const data = await res.json();

    if (!data.success || !data.data?.doctors?.length) {
      doctorGrid.innerHTML = emptyHtml('No doctors available for this service.');
      return;
    }

    renderDoctors(form, doctorGrid, data.data.doctors);
  } catch {
    doctorGrid.innerHTML = errorHtml('Could not load doctors. Please refresh.');
  }
}

function renderDoctors(form, container, doctors) {
  container.innerHTML = doctors.map(d => `
    <label class="doctor-option" data-doctor-id="${d.id}">
      <input type="radio" name="doctor_id" value="${d.id}"
             data-duration="${d.slot_duration ?? 30}"
             data-name="${escHtml(d.name)}">
      <div class="doctor-option__inner">
        <div class="doctor-option__avatar">
          ${d.photo_url
            ? `<img src="${escHtml(d.photo_url)}" alt="${escHtml(d.name)}" loading="lazy">`
            : `<span>${escHtml(d.name.charAt(0))}</span>`
          }
        </div>
        <div>
          <div class="doctor-option__name">${escHtml(d.name)}</div>
          <div class="doctor-option__spec">${escHtml(d.specialty ?? '')}</div>
        </div>
        <div class="doctor-option__duration">${d.slot_duration ?? 30} min</div>
        <div class="doctor-option__check">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
               stroke="white" stroke-width="3" aria-hidden="true">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
        </div>
      </div>
    </label>`).join('');

  /* Listen for selection */
  qsa('input[name="doctor_id"]', container).forEach(radio => {
    on(radio, 'change', () => {
      state.doctorId = radio.value;
      state.labels.doctor = radio.dataset.name || '';
      state.slotStart = null;
      state.slotEnd   = null;
      state.labels.slot = '';
      updateSummary();
      /* Reset slots if date already chosen */
      if (state.date) loadSlots(form);
    });
  });

  /* Auto-select single doctor or pending pre-selection */
  const pending = form.dataset.pendingDoctorId;
  if (doctors.length === 1) {
    const r = qs('input[name="doctor_id"]', container);
    if (r) { r.checked = true; r.dispatchEvent(new Event('change')); }
  } else if (pending) {
    const r = qs(`input[name="doctor_id"][value="${pending}"]`, container);
    if (r) { r.checked = true; r.dispatchEvent(new Event('change')); }
    delete form.dataset.pendingDoctorId;
  }
}

/* ─────────────────────────────────────────────────────────────────
   DATE SETUP & CHANGE → load slots
   ───────────────────────────────────────────────────────────────── */
function setupDatePicker(input) {
  if (!input) return;
  const today  = new Date();
  const maxDay = new Date();
  maxDay.setDate(today.getDate() + 60);
  input.min = toISO(today);
  input.max = toISO(maxDay);
}

function toISO(d) {
  return d.toISOString().split('T')[0];
}

function onDateChange(form, dateInput) {
  const date = dateInput.value;
  state.date = date || null;
  state.slotStart = null;
  state.slotEnd   = null;
  state.labels.date = date ? formatDate(date) : '';
  state.labels.slot = '';
  updateSummary();

  if (date && state.doctorId) loadSlots(form);
}

function formatDate(iso) {
  const d = new Date(iso + 'T00:00:00');
  return d.toLocaleDateString('en-GB', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });
}

async function loadSlots(form) {
  const grid = qs('#slotsGrid', form);
  if (!grid || !state.doctorId || !state.date) return;

  grid.innerHTML = `
    <div class="slots-loading" aria-live="polite">
      <div class="slots-loading__spinner" aria-hidden="true"></div>
      Loading available times…
    </div>`;

  try {
    const res  = await apiFetch(`/api/available-slots?doctor_id=${state.doctorId}&date=${state.date}`);
    const data = await res.json();

    if (!data.success || !data.data?.slots?.length) {
      grid.innerHTML = `
        <div class="slots-empty">
          <div class="slots-empty__icon" aria-hidden="true">📅</div>
          <p>No available slots on this date.<br>Please try another day.</p>
        </div>`;
      return;
    }

    renderSlots(form, grid, data.data.slots);
  } catch {
    grid.innerHTML = errorHtml('Could not load time slots. Please try again.');
  }
}

function renderSlots(form, container, slots) {
  container.innerHTML = `<div class="slots-grid">${
    slots.map(s => `
      <button type="button"
              class="time-slot"
              data-start="${escHtml(s.start)}"
              data-end="${escHtml(s.end)}"
              aria-label="${fmtTime(s.start)} to ${fmtTime(s.end)}">
        ${fmtTime(s.start)}
      </button>`).join('')
  }</div>`;

  qsa('.time-slot', container).forEach(btn => {
    on(btn, 'click', () => {
      qsa('.time-slot', container).forEach(b => b.classList.remove('is-selected'));
      btn.classList.add('is-selected');
      state.slotStart = btn.dataset.start;
      state.slotEnd   = btn.dataset.end;
      state.labels.slot = `${fmtTime(btn.dataset.start)} – ${fmtTime(btn.dataset.end)}`;

      /* Write to hidden inputs */
      setHidden(form, 'slot_start', state.slotStart);
      setHidden(form, 'slot_end',   state.slotEnd);
      updateSummary();
    });
  });
}

function fmtTime(t) {
  const [h, m] = t.split(':').map(Number);
  return `${h % 12 || 12}:${String(m).padStart(2,'0')} ${h < 12 ? 'AM' : 'PM'}`;
}

/* ─────────────────────────────────────────────────────────────────
   SIDEBAR SUMMARY
   ───────────────────────────────────────────────────────────────── */
function updateSummary() {
  const set = (id, val) => {
    const el = qs(id);
    if (!el) return;
    el.textContent = val || '—';
    el.classList.toggle('booking-summary__empty', !val);
  };

  set('#summary-service',  state.labels.service);
  set('#summary-doctor',   state.labels.doctor);
  set('#summary-date',     state.labels.date);
  set('#summary-slot',     state.labels.slot);
  set('#summary-price',    state.labels.price ? `From ${state.labels.price}` : '');
}

/* ─────────────────────────────────────────────────────────────────
   FORM SUBMISSION
   ───────────────────────────────────────────────────────────────── */
async function submitBooking(form) {
  if (!validateCurrentStep(form)) return;

  const submitBtn = qs('[type="submit"]', form);
  const originalText = submitBtn?.innerHTML || '';
  if (submitBtn) {
    submitBtn.disabled = true;
    submitBtn.innerHTML = `
      <svg class="btn-spinner" width="18" height="18" viewBox="0 0 24 24"
           fill="none" aria-hidden="true">
        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" stroke-dasharray="32" stroke-dashoffset="32">
          <animate attributeName="stroke-dashoffset" dur=".7s" values="32;0" fill="freeze"/>
        </circle>
      </svg>
      Confirming…`;
  }

  /* Build payload from form */
  const fd = new FormData(form);
  const payload = {};
  fd.forEach((v, k) => { payload[k] = v; });

  /* Ensure slot values are in payload */
  payload.slot_start = state.slotStart;
  payload.slot_end   = state.slotEnd;

  try {
    const res  = await fetch('/api/booking', {
      method:  'POST',
      headers: {
        'Content-Type':     'application/json',
        'X-CSRF-TOKEN':     getCsrf(),
        'X-Requested-With': 'XMLHttpRequest',
        'Accept':           'application/json',
      },
      body: JSON.stringify(payload),
    });

    const data = await res.json();

    if (data.success) {
      window.location.href = data.redirect;
      return;
    }

    /* Show server validation errors */
    if (data.errors) {
      Object.entries(data.errors).forEach(([field, messages]) => {
        const el = qs(`[name="${field}"]`, form);
        if (el) showError(el, Array.isArray(messages) ? messages[0] : messages);
      });
      /* Jump back to the step that has the first error */
      jumpToFirstError(form);
    }

    if (data.message) {
      showToast(data.message, 'error');
    }

  } catch {
    showToast('A network error occurred. Please try again.', 'error');
  } finally {
    if (submitBtn) {
      submitBtn.disabled  = false;
      submitBtn.innerHTML = originalText;
    }
  }
}

/* ─────────────────────────────────────────────────────────────────
   ERROR HELPERS
   ───────────────────────────────────────────────────────────────── */
function showError(el, message) {
  if (!el) return;
  el.classList.add('form-control--error');

  const group = el.closest('.form-group');
  if (!group) return;

  removeOldError(group);
  const span = document.createElement('span');
  span.className   = 'form-error';
  span.textContent = message;
  group.appendChild(span);

  if (!el._scrolled) {
    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
    el._scrolled = true;
    setTimeout(() => { el._scrolled = false; }, 800);
  }
}

function showGroupError(container, message) {
  if (!container) return;
  removeOldError(container);
  const span = document.createElement('div');
  span.className   = 'form-error';
  span.style.cssText = 'margin-top:8px';
  span.textContent = message;
  container.appendChild(span);
  container.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function removeOldError(parent) {
  parent.querySelectorAll('.form-error').forEach(e => e.remove());
}

function clearErrors(form) {
  form.querySelectorAll('.form-control--error').forEach(el => el.classList.remove('form-control--error'));
  form.querySelectorAll('.form-error').forEach(el => el.remove());
}

function jumpToFirstError(form) {
  /* Step 1 fields */
  if (qs('#service_id.form-control--error', form) ||
      qs('input[name="doctor_id"].form-control--error', form)) {
    setStep(form, 1); return;
  }
  /* Step 2 fields */
  if (qs('#appointment_date.form-control--error', form)) {
    setStep(form, 2); return;
  }
  /* Step 3 fields */
  if (qs('#patient_name.form-control--error,#patient_phone.form-control--error', form)) {
    setStep(form, 3);
  }
}

/* ─────────────────────────────────────────────────────────────────
   MINOR UTILITIES
   ───────────────────────────────────────────────────────────────── */
function setHidden(form, name, value) {
  let el = qs(`input[name="${name}"][type="hidden"]`, form);
  if (!el) {
    el = document.createElement('input');
    el.type = 'hidden';
    el.name = name;
    form.appendChild(el);
  }
  el.value = value ?? '';
}

function initCharCounter(textarea) {
  if (!textarea) return;
  const max     = parseInt(textarea.maxLength || 1000, 10);
  const counter = document.createElement('span');
  counter.className = 'form-char-count';
  counter.textContent = `0 / ${max}`;
  textarea.parentNode.appendChild(counter);

  on(textarea, 'input', () => {
    const len = textarea.value.length;
    counter.textContent = `${len} / ${max}`;
    counter.classList.toggle('is-near',  len > max * 0.8);
    counter.classList.toggle('is-limit', len >= max);
  });
}

function showToast(message, type = 'info') {
  /* Reuse existing flash structure from app.css */
  let container = qs('.flash-container');
  if (!container) {
    container = document.createElement('div');
    container.className = 'flash-container';
    document.body.appendChild(container);
  }
  const el = document.createElement('div');
  el.className = `flash flash--${type}`;
  el.innerHTML = `<span>${escHtml(message)}</span>
                  <button class="flash__close" onclick="this.parentElement.remove()">×</button>`;
  container.appendChild(el);

  setTimeout(() => {
    Object.assign(el.style, { transition:'opacity .35s,transform .35s', opacity:'0', transform:'translateX(110%)' });
    setTimeout(() => el.remove(), 350);
  }, 5000);
}

function escHtml(str) {
  return String(str ?? '')
    .replace(/&/g,'&amp;')
    .replace(/</g,'&lt;')
    .replace(/>/g,'&gt;')
    .replace(/"/g,'&quot;')
    .replace(/'/g,'&#039;');
}

/* Generic fetch — GET requests have no body */
function apiFetch(url, opts = {}) {
  return fetch(url, {
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept':           'application/json',
      'X-CSRF-TOKEN':     getCsrf(),
      ...opts.headers,
    },
    ...opts,
  });
}

const loadingHtml = msg  => `<div class="slots-loading"><div class="slots-loading__spinner" aria-hidden="true"></div>${msg}</div>`;
const emptyHtml   = msg  => `<div class="slots-empty"><div class="slots-empty__icon" aria-hidden="true">🔍</div><p>${msg}</p></div>`;
const errorHtml   = msg  => `<p class="form-error" style="padding:12px">${msg}</p>`;

/* ─────────────────────────────────────────────────────────────────
   BOOT
   ───────────────────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', initBookingForm);
