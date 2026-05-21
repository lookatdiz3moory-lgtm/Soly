/**
 * Soly Clinic — app.js
 * Owns: before/after image sliders only.
 * Everything else (navbar, animations, counters, carousel, FAQ, scroll-top,
 * flash, lazy images, map embed, anchor scroll) lives in components.js.
 */

'use strict';

/* ─── Helpers ─────────────────────────────────────────────────── */
const $ = (sel, ctx = document) => ctx.querySelector(sel);
const $$ = (sel, ctx = document) => [...ctx.querySelectorAll(sel)];
const on = (el, ev, fn, opts) => el && el.addEventListener(ev, fn, opts);

/* ─── Before/After Slider ──────────────────────────────────────── */
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
        on(document, 'mouseup',  () => { active = false; });
        on(document, 'mousemove', e => { if (active) setPos(e.clientX); });
        on(handle, 'touchstart', () => { active = true; }, { passive: true });
        on(document, 'touchend', () => { active = false; });
        on(document, 'touchmove', e => { if (active) setPos(e.touches[0].clientX); }, { passive: true });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initBeforeAfterSliders();
});
