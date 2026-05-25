/**
 * Soly Clinic — booking.js
 * Multi-step booking form with live slot availability.
 * Loaded only on the booking page via @stack('scripts').
 * Reads window.bookingI18n for localized strings (injected by Blade).
 */

'use strict';

/* ── Tiny helpers ─────────────────────────────────────────────── */
const qs  = (s, c = document) => c.querySelector(s);
const qsa = (s, c = document) => [...c.querySelectorAll(s)];
const on  = (el, ev, fn, opts) => el && el.addEventListener(ev, fn, opts);
const val = el => el ? el.value.trim() : '';
const getCsrf = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

/* ── i18n — falls back to English if bookingI18n is not injected ─ */
const i18n = window.bookingI18n ?? {};
const t = key => i18n[key] ?? key;
const isAr = (i18n.locale ?? document.documentElement.lang ?? 'en') === 'ar';

/* ── Debug logger ─────────────────────────────────────────────── */
const DBG = true; // flip to false to silence
const dbg = (...a) => DBG && console.log('[Booking]', ...a);

/* ── State ────────────────────────────────────────────────────── */
const state = {
  step:      1,
  serviceId: null,
  doctorId:  null,
  date:      null,
  slotStart: null,
  slotEnd:   null,
  labels: {
    service: '',
    doctor:  '',
    date:    '',
    slot:    '',
    price:   '',
  },
};

/* ─────────────────────────────────────────────────────────────────
   INIT
   ───────────────────────────────────────────────────────────────── */
function initBookingForm() {
  dbg('initBookingForm start, readyState=', document.readyState);
  const form = qs('#bookingForm');
  dbg('form=', form);
  if (!form) { dbg('ABORT: #bookingForm not found'); return; }

  /* ── Event delegation: catches clicks regardless of DOM timing ── */
  on(document, 'click', e => {
    const nextBtn = e.target.closest('[data-next]');
    const backBtn = e.target.closest('[data-back]');
    if (nextBtn && form.contains(nextBtn)) { e.preventDefault(); dbg('delegated next click'); goNext(form); }
    if (backBtn && form.contains(backBtn)) { e.preventDefault(); dbg('delegated back click'); goBack(form); }
  });

  const serviceSelect = qs('#service_id', form);
  on(serviceSelect, 'change', () => onServiceChange(form, serviceSelect));

  const dateInput = qs('#appointment_date', form);
  setupDatePicker(dateInput);
  on(dateInput, 'change', () => onDateChange(form, dateInput));

  on(form, 'submit', e => { e.preventDefault(); submitBooking(form); });

  initCharCounter(qs('#notes', form));

  const preServiceId = form.dataset.preServiceId;
  const preDoctorId  = form.dataset.preDoctorId;

  if (preServiceId && serviceSelect) {
    serviceSelect.value = preServiceId;
    serviceSelect.dispatchEvent(new Event('change'));
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
  dbg('goNext clicked, step=', state.step, 'state=', JSON.stringify(state));
  if (!validateCurrentStep(form)) {
    dbg('validation FAILED for step', state.step);
    shakePanel(form);
    return;
  }
  dbg('validation PASSED, advancing to step', state.step + 1);
  setStep(form, state.step + 1);
}

function shakePanel(form) {
  const panel = qs('.booking-panel.is-active', form);
  if (!panel) return;
  panel.classList.remove('shake');
  void panel.offsetWidth; // force reflow
  panel.classList.add('shake');
  setTimeout(() => panel.classList.remove('shake'), 500);
}

function goBack(form) {
  setStep(form, state.step - 1);
}

function setStep(form, step) {
  const totalSteps = qsa('.booking-panel', form).length;
  step = Math.max(1, Math.min(step, totalSteps));
  state.step = step;

  qsa('.booking-panel', form).forEach((panel, i) => {
    panel.classList.toggle('is-active', i + 1 === step);
  });

  qsa('.booking-step', form.closest('.booking-card') ?? document).forEach((el, i) => {
    el.classList.toggle('is-active', i + 1 === step);
    el.classList.toggle('is-done',   i + 1 <  step);
  });

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
  dbg('validateStep1: serviceId=', serviceId);
  if (!serviceId) {
    showError(qs('#service_id', form), t('errorService'));
    return false;
  }
  const doctorPicked = qs('input[name="doctor_id"]:checked', form);
  dbg('validateStep1: doctorPicked=', doctorPicked, 'doctorGrid=', qs('#doctorGrid', form));
  if (!doctorPicked) {
    showGroupError(qs('#doctorGrid', form), t('errorDoctor'));
    return false;
  }
  return true;
}

function validateStep2(form) {
  const date = val(qs('#appointment_date', form));
  dbg('validateStep2: date=', date, 'slotStart=', state.slotStart, 'slotEnd=', state.slotEnd);
  if (!date) {
    showError(qs('#appointment_date', form), t('errorDate'));
    return false;
  }
  if (!state.slotStart) {
    showGroupError(qs('#slotsGrid', form), t('errorSlot'));
    return false;
  }
  return true;
}

function validateStep3(form) {
  let ok = true;

  const nameEl  = qs('#patient_name', form);
  const phoneEl = qs('#patient_phone', form);

  if (!val(nameEl) || val(nameEl).length < 2) {
    showError(nameEl, t('errorName'));
    ok = false;
  }

  const phone = val(phoneEl).replace(/[\s\-\(\)]/g, '');
  if (!phone) {
    showError(phoneEl, t('errorPhone'));
    ok = false;
  } else if (!/^(\+2)?01[0125]\d{8}$/.test(phone)) {
    showError(phoneEl, t('errorPhoneFmt'));
    ok = false;
  }

  const emailEl = qs('#patient_email', form);
  if (val(emailEl) && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val(emailEl))) {
    showError(emailEl, t('errorEmail'));
    ok = false;
  }

  if (!qs('#agree_terms', form)?.checked) {
    showGroupError(qs('.booking-terms', form), t('errorTerms'));
    ok = false;
  }

  return ok;
}

/* ─────────────────────────────────────────────────────────────────
   SERVICE CHANGE → load doctors
   ───────────────────────────────────────────────────────────────── */
async function onServiceChange(form, serviceSelect) {
  const id = serviceSelect.value;
  dbg('onServiceChange: id=', id);

  state.serviceId = id || null;
  state.doctorId  = null;
  state.slotStart = null;
  state.slotEnd   = null;

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

  doctorGrid.innerHTML = loadingHtml('…');
  doctorWrap.style.display = 'block';

  try {
    const res  = await apiFetch(`/api/doctors-by-service/${id}`);
    const data = await res.json();

    if (!data.success || !data.data?.doctors?.length) {
      doctorGrid.innerHTML = emptyHtml(data.message || '');
      return;
    }

    renderDoctors(form, doctorGrid, data.data.doctors);
  } catch {
    doctorGrid.innerHTML = errorHtml(t('errorNetwork'));
  }
}

function renderDoctors(form, container, doctors) {
  container.innerHTML = doctors.map(d => {
    const dName = isAr && d.name_ar ? d.name_ar : d.name;
    const dSpec = isAr && d.specialty_ar ? d.specialty_ar : (d.specialty ?? '');
    return `
    <label class="doctor-option" data-doctor-id="${d.id}">
      <input type="radio" name="doctor_id" value="${d.id}"
             data-duration="${d.slot_duration ?? 30}"
             data-name="${escHtml(dName)}">
      <div class="doctor-option__inner">
        <div class="doctor-option__avatar">
          ${d.photo_url
            ? `<img src="${escHtml(d.photo_url || '')}" alt="${escHtml(dName)}" loading="lazy">`
            : `<span>${escHtml(dName.charAt(0))}</span>`
          }
        </div>
        <div>
          <div class="doctor-option__name">${escHtml(dName)}</div>
          <div class="doctor-option__spec">${escHtml(dSpec)}</div>
        </div>
        <div class="doctor-option__duration">${d.slot_duration ?? 30} min</div>
        <div class="doctor-option__check">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
               stroke="white" stroke-width="3" aria-hidden="true">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
        </div>
      </div>
    </label>`;
  }).join('');

  qsa('input[name="doctor_id"]', container).forEach(radio => {
    on(radio, 'change', () => {
      state.doctorId = radio.value;
      state.labels.doctor = radio.dataset.name || '';
      state.slotStart = null;
      state.slotEnd   = null;
      state.labels.slot = '';
      updateSummary();
      if (state.date) loadSlots(form);
    });
  });

  qsa('.doctor-option', container).forEach(label => {
    on(label, 'click', e => {
      e.preventDefault();
      const radio = label.querySelector('input[type="radio"]');
      if (!radio) return;
      dbg('doctor-option clicked, setting radio checked for value=', radio.value);
      radio.checked = true;
      radio.dispatchEvent(new Event('change', { bubbles: true }));
    });
  });

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
    </div>`;

  try {
    const res  = await apiFetch(`/api/available-slots?doctor_id=${state.doctorId}&date=${state.date}`);
    const data = await res.json();

    if (!data.success || !data.data?.slots?.length) {
      grid.innerHTML = `
        <div class="slots-empty">
          <div class="slots-empty__icon" aria-hidden="true">📅</div>
          <p>${escHtml(data.message || '')}</p>
        </div>`;
      return;
    }

    renderSlots(form, grid, data.data.slots);
  } catch {
    grid.innerHTML = errorHtml(t('errorNetwork'));
  }
}

function renderSlots(form, container, slots) {
  container.innerHTML = `<div class="slots-grid">${
    slots.map(s => `
      <button type="button"
              class="time-slot"
              data-start="${escHtml(s.start)}"
              data-end="${escHtml(s.end)}"
              aria-label="${fmtTime(s.start)} – ${fmtTime(s.end)}">
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
  const set = (id, v) => {
    const el = qs(id);
    if (!el) return;
    el.textContent = v || '—';
    el.classList.toggle('booking-summary__empty', !v);
  };

  const priceLabel = state.labels.price ? `${t('priceFrom')} ${state.labels.price}` : '';

  set('#summary-service', state.labels.service);
  set('#summary-doctor',  state.labels.doctor);
  set('#summary-date',    state.labels.date);
  set('#summary-slot',    state.labels.slot);
  set('#summary-price',   priceLabel);
}

/* ─────────────────────────────────────────────────────────────────
   FORM SUBMISSION
   ───────────────────────────────────────────────────────────────── */
async function submitBooking(form) {
  if (!validateCurrentStep(form)) return;

  const submitBtn  = qs('[type="submit"]', form);
  const savedHTML  = submitBtn?.innerHTML || '';

  if (submitBtn) {
    submitBtn.disabled = true;
    submitBtn.innerHTML = `
      <svg class="btn-spinner" width="18" height="18" viewBox="0 0 24 24"
           fill="none" aria-hidden="true">
        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"
                stroke-dasharray="32" stroke-dashoffset="32">
          <animate attributeName="stroke-dashoffset" dur=".7s" values="32;0" fill="freeze"/>
        </circle>
      </svg>
      ${escHtml(t('confirming'))}`;
  }

  const fd = new FormData(form);
  const payload = {};
  fd.forEach((v, k) => { payload[k] = v; });
  payload.slot_start = state.slotStart;
  payload.slot_end   = state.slotEnd;

  try {
    const res = await fetch('/api/booking', {
      method:  'POST',
      headers: {
        'Content-Type':     'application/json',
        'X-CSRF-TOKEN':     getCsrf(),
        'X-Requested-With': 'XMLHttpRequest',
        'Accept':           'application/json',
      },
      body: JSON.stringify(payload),
    });

    /* Handle non-JSON error responses before trying to parse */
    if (res.status === 419) {
      showToast(t('errorSession'), 'error');
      return;
    }
    if (res.status === 429) {
      showToast(t('errorTooMany'), 'error');
      return;
    }
    if (!res.ok && res.status >= 500) {
      showToast(t('errorNetwork'), 'error');
      return;
    }

    const data = await res.json();

    if (data.success) {
      window.location.href = data.redirect;
      return;
    }

    if (data.errors) {
      Object.entries(data.errors).forEach(([field, messages]) => {
        const el = qs(`[name="${field}"]`, form);
        if (el) showError(el, Array.isArray(messages) ? messages[0] : messages);
      });
      jumpToFirstError(form);
    }

    if (data.message) {
      showToast(data.message, 'error');
    }

  } catch {
    showToast(t('errorNetwork'), 'error');
  } finally {
    if (submitBtn) {
      submitBtn.disabled  = false;
      submitBtn.innerHTML = savedHTML;
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
  span.className   = 'form-error form-error--visible';
  span.textContent = message;
  group.appendChild(span);
  span.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function showGroupError(container, message) {
  if (!container) return;
  removeOldError(container);
  const span = document.createElement('div');
  span.className   = 'form-error form-error--visible';
  span.style.cssText = 'margin-top:8px';
  span.textContent = message;
  container.appendChild(span);
  span.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function removeOldError(parent) {
  parent.querySelectorAll('.form-error').forEach(e => e.remove());
}

function clearErrors(form) {
  form.querySelectorAll('.form-control--error').forEach(el => el.classList.remove('form-control--error'));
  form.querySelectorAll('.form-error').forEach(el => el.remove());
}

function jumpToFirstError(form) {
  if (qs('#service_id.form-control--error', form) ||
      qs('input[name="doctor_id"].form-control--error', form)) {
    setStep(form, 1); return;
  }
  if (qs('#appointment_date.form-control--error', form)) {
    setStep(form, 2); return;
  }
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

const loadingHtml = msg  => `<div class="slots-loading"><div class="slots-loading__spinner" aria-hidden="true"></div>${escHtml(msg)}</div>`;
const emptyHtml   = msg  => `<div class="slots-empty"><div class="slots-empty__icon" aria-hidden="true">🔍</div><p>${escHtml(msg)}</p></div>`;
const errorHtml   = msg  => `<p class="form-error" style="padding:12px">${escHtml(msg)}</p>`;

/* ─────────────────────────────────────────────────────────────────
   BOOT
   ───────────────────────────────────────────────────────────────── */
dbg('boot: readyState=', document.readyState);
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initBookingForm);
} else {
  initBookingForm();
}
