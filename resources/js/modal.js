window.openEventCreateModal = function(startDateIso) {
    if (window.CALENDAR_DEBUG) console.info('[modal] openEventCreateModal called', startDateIso);
    const el = document.getElementById('admin-event-modal');
    if (!el) return;

    let dt = startDateIso;
    if (/^\d{4}-\d{2}-\d{2}$/.test(dt)) dt = dt + 'T09:00';

    const setVal = (id, val) => { const e = document.getElementById(id); if (e) e.value = val; };
    const setChecked = (id, val) => { const e = document.getElementById(id); if (e) e.checked = val; };

    setVal('ae-title', '');
    setVal('ae-description', '');
    setVal('ae-start_at', dt);

    const endTime = new Date(dt);
    endTime.setHours(endTime.getHours() + 1);
    setVal('ae-end_at', endTime.toISOString().slice(0, 16));

    setChecked('ae-is_all_day', false);
    setVal('ae-location', '');
    setVal('ae-type', 'autre');

    el.style.display = 'flex';
    el.setAttribute('data-open', '1');
    el.setAttribute('role', 'dialog');
    el.setAttribute('aria-modal', 'true');
    el.removeAttribute('aria-hidden');
};

if (!window.__adminModalListenersAttached) {
    document.addEventListener('click', function(ev){
        const target = ev.target;
        if (!target) return;
        if (target.matches('[data-modal-close], #admin-event-modal .modal-backdrop')) {
            const modal = document.getElementById('admin-event-modal');
            if (modal && modal.getAttribute('data-open') === '1') {
                modal.style.display = 'none';
                modal.setAttribute('data-open', '0');
                modal.setAttribute('aria-hidden', 'true');
            }
        }
    });

    document.addEventListener('keydown', function(ev){
        if (ev.key === 'Escape') {
            const modal = document.getElementById('admin-event-modal');
            if (modal && modal.getAttribute('data-open') === '1') {
                modal.style.display = 'none';
                modal.setAttribute('data-open', '0');
                modal.setAttribute('aria-hidden', 'true');
            }
        }
    });

    window.__adminModalListenersAttached = true;
}

document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('admin-event-create-form');
    if (!form) return;
    const errorsEl = document.getElementById('admin-event-errors');
    if (errorsEl) {
        errorsEl.setAttribute('role', 'alert');
        errorsEl.setAttribute('aria-live', 'polite');
    }
    form.addEventListener('submit', async function(e){
        e.preventDefault();
        const action = form.action;
        const data = new FormData(form);
        if (!data.get('is_all_day')) data.delete('is_all_day');
        const payload = {};
        data.forEach((v, k) => { payload[k] = v; });
        const tokenEl = document.querySelector('meta[name="csrf-token"]');
        const token = tokenEl ? tokenEl.getAttribute('content') : '';
        try {
            const res = await fetch(action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify(payload)
            });
            if (res.status === 201 || res.ok) {
                const modal = document.getElementById('admin-event-modal');
                if (modal) {
                    modal.style.display = 'none';
                    modal.setAttribute('data-open', '0');
                    modal.setAttribute('aria-hidden', 'true');
                }
            } else if (res.status === 422) {
                const json = await res.json();
                const errs = json.errors || {};
                const el = document.getElementById('admin-event-errors');
                if (el) {
                    el.style.display = 'block';
                    el.textContent = '';
                    for (const msg of Object.values(errs).flat()) {
                        const div = document.createElement('div');
                        div.textContent = msg;
                        el.appendChild(div);
                    }
                }
            } else {
                const el = document.getElementById('admin-event-errors');
                if (el) { el.style.display = 'block'; el.textContent = 'Error creating event'; }
            }
        } catch (err) {
            const el = document.getElementById('admin-event-errors');
            if (el) { el.style.display = 'block'; el.textContent = 'Network error'; }
        }
    });
});
