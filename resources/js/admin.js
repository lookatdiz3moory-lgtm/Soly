/**
 * Soly Clinic — admin.js  (Phase 5)
 * Admin panel interactions.
 * No jQuery. Loaded only on admin pages via the admin layout.
 */

'use strict';

const qs  = (s, c = document) => c.querySelector(s);
const qsa = (s, c = document) => [...c.querySelectorAll(s)];
const on  = (el, ev, fn, opts) => el && el.addEventListener(ev, fn, opts);
const db  = (fn, ms = 120) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };
const getCsrf = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

/* ─── JSON fetch helper ───────────────────────────────────────── */
async function adminFetch(url, body = null, method = 'POST') {
  const opts = {
    method,
    headers: {
      'X-CSRF-TOKEN':     getCsrf(),
      'X-Requested-With': 'XMLHttpRequest',
      'Accept':           'application/json',
    },
  };
  if (body) {
    opts.headers['Content-Type'] = 'application/json';
    opts.body = JSON.stringify(body);
  }
  const res = await fetch(url, opts);
  return res.json();
}

/* ═══════════════════════════════════════════════════════════════
   1. SIDEBAR — toggle, overlay, active link
   ═══════════════════════════════════════════════════════════════ */
function initSidebar() {
  const sidebar  = qs('#admSidebar');
  const overlay  = qs('#admOverlay');
  const hamburger= qs('#admHamburger');
  if (!sidebar) return;

  const open  = () => { sidebar.classList.add('is-open');  overlay?.classList.add('is-visible');  document.body.style.overflow = 'hidden'; };
  const close = () => { sidebar.classList.remove('is-open'); overlay?.classList.remove('is-visible'); document.body.style.overflow = ''; };
  const toggle= () => sidebar.classList.contains('is-open') ? close() : open();

  on(hamburger, 'click', toggle);
  on(overlay,   'click', close);
  on(document,  'keydown', e => { if (e.key === 'Escape') close(); });

  /* Mark active nav item by comparing pathname.
     Require at least one non-slash character after href to avoid /admin matching everything. */
  const path = window.location.pathname;
  qsa('.adm-nav-item', sidebar).forEach(link => {
    const href = link.getAttribute('href') || '';
    if (!href || href === '#') return;
    // Exact match OR href is a proper prefix (followed by / or end-of-string)
    const isExact  = path === href;
    const isPrefix = path.startsWith(href + '/') || path.startsWith(href + '?');
    if (isExact || isPrefix) link.classList.add('is-active');
  });
}

/* ═══════════════════════════════════════════════════════════════
   2. FLASH MESSAGES — auto dismiss
   ═══════════════════════════════════════════════════════════════ */
function initFlash() {
  qsa('.adm-flash').forEach(el => {
    setTimeout(() => {
      el.style.transition = 'opacity .35s, transform .35s';
      el.style.opacity    = '0';
      el.style.transform  = 'translateY(-6px)';
      setTimeout(() => el.remove(), 350);
    }, 5500);
  });
}

/* ═══════════════════════════════════════════════════════════════
   3. STATUS BADGE UPDATE — AJAX, no page reload
   ═══════════════════════════════════════════════════════════════ */
function initStatusActions() {
  qsa('[data-status-action]').forEach(btn => {
    on(btn, 'click', async () => {
      const id     = btn.dataset.appointmentId;
      const status = btn.dataset.statusAction;
      if (!id || !status) return;

      let reason = '';
      if (status === 'cancelled') {
        reason = prompt('Cancellation reason (optional):') ?? '';
      }

      if (!confirm(`Change status to "${status.replace('_', ' ')}"?`)) return;

      btn.classList.add('adm-btn--loading');
      btn.disabled = true;

      try {
        const data = await adminFetch(
          `/admin/appointments/${id}/status`,
          { status, reason }
        );

        if (data.success) {
          showToast(`Status updated to "${data.status_label}".`, 'success');
          /* Update badge in place */
          const badge = qs(`[data-status-badge="${id}"]`);
          if (badge) {
            badge.className  = `adm-badge adm-badge--${status}`;
            badge.textContent = data.status_label;
          }
          /* Disable action buttons that no longer make sense */
          setTimeout(() => location.reload(), 900);
        } else {
          showToast('Failed to update status. Please try again.', 'error');
        }
      } catch {
        showToast('Network error.', 'error');
      } finally {
        btn.classList.remove('adm-btn--loading');
        btn.disabled = false;
      }
    });
  });
}

/* ═══════════════════════════════════════════════════════════════
   4. WHATSAPP QUICK OPEN
   ═══════════════════════════════════════════════════════════════ */
function initWhatsApp() {
  qsa('[data-wa-action]').forEach(btn => {
    on(btn, 'click', async () => {
      const id       = btn.dataset.appointmentId;
      const template = btn.dataset.waAction || 'confirmation';
      if (!id) return;

      btn.classList.add('adm-btn--loading');
      btn.disabled = true;

      try {
        const data = await adminFetch(`/admin/appointments/${id}/whatsapp`, { template });
        if (data.success && data.url) {
          window.open(data.url, '_blank', 'noopener,noreferrer');
        } else {
          showToast('Could not generate WhatsApp link.', 'error');
        }
      } catch {
        showToast('Network error.', 'error');
      } finally {
        btn.classList.remove('adm-btn--loading');
        btn.disabled = false;
      }
    });
  });
}

/* ═══════════════════════════════════════════════════════════════
   5. NOTES AUTO-SAVE
   ═══════════════════════════════════════════════════════════════ */
function initNotesAutoSave() {
  const form = qs('#adminNotesForm');
  if (!form) return;

  const textarea  = qs('textarea[name="admin_notes"]', form);
  const indicator = qs('#notesSaveStatus');
  const id        = form.dataset.appointmentId;
  if (!textarea || !id) return;

  const save = db(async () => {
    if (indicator) indicator.textContent = 'Saving…';
    try {
      const data = await adminFetch(`/admin/appointments/${id}/notes`, {
        admin_notes: textarea.value,
      });
      if (indicator) {
        indicator.textContent = data.success ? '✓ Saved' : '✗ Error';
        setTimeout(() => { if (indicator) indicator.textContent = ''; }, 2500);
      }
    } catch {
      if (indicator) indicator.textContent = '✗ Failed';
    }
  }, 1400);

  on(textarea, 'input', save);
}

/* ═══════════════════════════════════════════════════════════════
   6. WEEKLY BAR CHART
   ═══════════════════════════════════════════════════════════════ */
function initWeeklyChart() {
  const wrap = qs('#weeklyChart');
  if (!wrap) return;

  const data = window.__weeklyChart || [];
  if (!data.length) return;

  const max = Math.max(...data.map(d => d.count), 1);

  wrap.innerHTML = `
    <div class="adm-chart-bar-wrap">
      ${data.map(d => {
        const pct = Math.round((d.count / max) * 100);
        return `
          <div class="adm-chart-bar-col">
            <div class="adm-chart-bar-count">${d.count > 0 ? d.count : ''}</div>
            <div class="adm-chart-bar" style="height:${Math.max(pct, 4)}%" title="${d.date}: ${d.count} bookings"></div>
            <div class="adm-chart-bar-label">${d.label}</div>
          </div>`;
      }).join('')}
    </div>`;
}

/* ═══════════════════════════════════════════════════════════════
   7. STAT COUNTERS — count up on load
   ═══════════════════════════════════════════════════════════════ */
function initStatCounters() {
  qsa('[data-count-to]').forEach(el => {
    const target = parseInt(el.dataset.countTo, 10);
    if (isNaN(target)) return;

    const duration = 900;
    const start    = performance.now();

    const step = now => {
      const pct = Math.min((now - start) / duration, 1);
      const eased = 1 - Math.pow(1 - pct, 3);
      el.textContent = Math.round(eased * target).toLocaleString();
      if (pct < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
  });
}

/* ═══════════════════════════════════════════════════════════════
   8. LIVE STATS REFRESH (poll every 60s)
   ═══════════════════════════════════════════════════════════════ */
function initStatsRefresh() {
  const map = {
    'stat-total':    'total_appointments',
    'stat-today':    'today_upcoming',
    'stat-pending':  'pending',
    'stat-month':    'month_count',
    'stat-confirmed':'confirmed',
    'stat-completed':'completed',
    'stat-cancelled':'cancelled',
    'stat-patients': 'total_patients',
  };

  async function refresh() {
    try {
      const res  = await fetch('/admin/dashboard/stats', {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
      });
      const data = await res.json();

      Object.entries(map).forEach(([id, key]) => {
        const el = document.getElementById(id);
        if (el && data[key] !== undefined) {
          el.textContent = Number(data[key]).toLocaleString();
        }
      });
    } catch { /* silent */ }
  }

  setInterval(refresh, 60_000);
}

/* ═══════════════════════════════════════════════════════════════
   9. CLICKABLE TABLE ROWS
   ═══════════════════════════════════════════════════════════════ */
function initTableRowLinks() {
  qsa('tr[data-href]').forEach(row => {
    row.style.cursor = 'pointer';
    on(row, 'click', e => {
      if (e.target.closest('button, a, input, select, label')) return;
      window.location.href = row.dataset.href;
    });
  });
}

/* ═══════════════════════════════════════════════════════════════
   10. CONFIRM BEFORE DELETE / DESTRUCTIVE ACTIONS
   ═══════════════════════════════════════════════════════════════ */
function initConfirmActions() {
  qsa('[data-confirm]').forEach(el => {
    on(el, 'click', e => {
      if (!confirm(el.dataset.confirm || 'Are you sure?')) e.preventDefault();
    });
  });
}

/* ═══════════════════════════════════════════════════════════════
   11. TOAST UTILITY
   ═══════════════════════════════════════════════════════════════ */
function showToast(message, type = 'info') {
  let wrap = qs('.adm-toast-wrap');
  if (!wrap) {
    wrap = Object.assign(document.createElement('div'), {
      className: 'adm-toast-wrap',
    });
    Object.assign(wrap.style, {
      position: 'fixed', bottom: '24px', right: '24px',
      zIndex: '9999', display: 'flex',
      flexDirection: 'column', gap: '8px',
    });
    document.body.appendChild(wrap);
  }

  const colours = {
    success: { bg: '#F0FDF4', color: '#15803D', border: '#BBF7D0' },
    error:   { bg: '#FEF2F2', color: '#B91C1C', border: '#FECACA' },
    info:    { bg: '#EFF6FF', color: '#1D4ED8', border: '#BFDBFE' },
  };
  const c = colours[type] || colours.info;

  const toast = document.createElement('div');
  Object.assign(toast.style, {
    background: c.bg, color: c.color,
    border: `1px solid ${c.border}`,
    borderRadius: '8px', padding: '11px 18px',
    fontSize: '13.5px', fontWeight: '500',
    boxShadow: '0 4px 16px rgba(0,0,0,.1)',
    display: 'flex', alignItems: 'center', gap: '10px',
    maxWidth: '360px', animation: 'adm-slide-in .25s ease',
  });
  toast.innerHTML = `<span>${message}</span>
    <button onclick="this.parentElement.remove()"
            style="margin-left:auto;opacity:.5;font-size:16px;cursor:pointer;background:none;border:none">×</button>`;
  wrap.appendChild(toast);

  setTimeout(() => {
    toast.style.transition = 'opacity .3s, transform .3s';
    toast.style.opacity    = '0';
    toast.style.transform  = 'translateX(110%)';
    setTimeout(() => toast.remove(), 310);
  }, 4500);
}

/* Expose globally for inline use */
window.adminShowToast = showToast;

/* ═══════════════════════════════════════════════════════════════
   INIT
   ═══════════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
  initSidebar();
  initFlash();
  initStatusActions();
  initWhatsApp();
  initNotesAutoSave();
  initWeeklyChart();
  initStatCounters();
  initStatsRefresh();
  initTableRowLinks();
  initConfirmActions();
});
