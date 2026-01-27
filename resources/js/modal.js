// Modal logic for admin event create
window.openEventCreateModal = function(startDateIso) {
    if (window.CALENDAR_DEBUG) console.info('[modal] openEventCreateModal called', startDateIso);
    let el = document.getElementById('admin-event-modal');
    if (!el) return;
    let dt = startDateIso;
    if (/^\d{4}-\d{2}-\d{2}$/.test(dt)) dt = dt + 'T09:00';
    try { document.getElementById('ae-title').value = ''; } catch(e){}
    try { document.getElementById('ae-description').value = ''; } catch(e){}
    try { document.getElementById('ae-start_at').value = dt; } catch(e){}
    try {
        let endTime = new Date(dt);
        endTime.setHours(endTime.getHours() + 1);
        document.getElementById('ae-end_at').value = endTime.toISOString().slice(0, 16);
    } catch(e){}
    try { document.getElementById('ae-is_all_day').checked = false; } catch(e){}
    try { document.getElementById('ae-location').value = ''; } catch(e){}
    try {
        el.style.display = 'flex';
        el.setAttribute('data-open', '1');
    } catch (err) {
        console.warn('[modal] show failed', err);
    }
    window.dispatchEvent(new CustomEvent('admin:event-open'));
};
window.addEventListener('admin:event-open', function(e){
    if (window.CALENDAR_DEBUG) console.info('[modal] global listener received admin:event-open', e && e.detail);
    const modal = document.getElementById('admin-event-modal');
    if (!modal) return;
    modal.style.display = 'flex';
    modal.setAttribute('data-open', '1');
});
document.addEventListener('click', function(ev){
    const target = ev.target;
    if (!target) return;
    if (target.matches('[data-modal-close]')) {
        const modal = document.getElementById('admin-event-modal');
        if (modal) {
            modal.style.display = 'none';
            modal.setAttribute('data-open', '0');
        }
    }
});
document.addEventListener('keydown', function(ev){
    if (ev.key === 'Escape') {
        const modal = document.getElementById('admin-event-modal');
        if (modal) {
            modal.style.display = 'none';
            modal.setAttribute('data-open', '0');
        }
    }
});
document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('admin-event-create-form');
    if (!form) return;
    form.addEventListener('submit', async function(e){
        e.preventDefault();
        const action = form.action;
        const data = new FormData(form);
        if (!data.get('is_all_day')) data.delete('is_all_day');
        const payload = {};
        data.forEach((v, k) => { payload[k] = v; });
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
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
                const json = await res.json();
                window.dispatchEvent(new CustomEvent('admin:event-created', { detail: json }));
                const modal = document.getElementById('admin-event-modal');
                if (modal) {
                    modal.style.display = 'none';
                    modal.setAttribute('data-open', '0');
                }
            } else if (res.status === 422) {
                const json = await res.json();
                const errs = json.errors || {};
                const el = document.getElementById('admin-event-errors');
                if (el) {
                    el.style.display = 'block';
                    el.innerHTML = Object.values(errs).flat().map(s => '<div>'+s+'</div>').join('');
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
