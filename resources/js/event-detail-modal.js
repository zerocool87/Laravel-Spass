// Event detail modal: accessible, fetches JSON and maps to DOM, progressive enhancement friendly
(function(){
    const SELECTOR = 'body';

    function formatDateTime(value){
        if (!value) {
            return '—';
        }

        const date = value instanceof Date ? value : new Date(value);
        if (Number.isNaN(date.getTime())) {
            return '—';
        }

        return date.toLocaleString('fr-FR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            hour12: false,
        });
    }

    function renderMinimalReunion(bodyEl, footerEl, reunion){
        const subject = reunion?.subject || '—';
        const instanceName = reunion?.instance || '—';
        const startLabel = formatDateTime(reunion?.start);
        const endLabel = formatDateTime(reunion?.end);

        bodyEl.innerHTML = `
            <div class="space-y-4 text-sm text-gray-700">
                <div class="rounded-xl border border-[#faa21b]/20 bg-gradient-to-br from-[#fff9ec] to-white p-4 shadow-sm">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-[#b36b00]">Sujet</p>
                    <p class="mt-1.5 text-base font-semibold leading-6 text-gray-900">${subject}</p>
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">Instance</p>
                        <p class="mt-1.5 font-medium leading-6 text-gray-900">${instanceName}</p>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">Horaires</p>
                        <p class="mt-1.5 font-medium leading-6 text-gray-900">${startLabel} → ${endLabel}</p>
                    </div>
                </div>
            </div>
        `;

        footerEl.innerHTML = '';
    }

    function createModalIfMissing(){
        if (document.getElementById('event-detail-modal')) return;
        const wrapper = document.createElement('div');
        wrapper.id = 'event-detail-modal';
        wrapper.className = 'fixed inset-0 z-50 hidden items-center justify-center p-4 sm:p-6';
        wrapper.setAttribute('role','dialog');
        wrapper.setAttribute('aria-modal','true');
        wrapper.setAttribute('aria-hidden','true');
        wrapper.innerHTML = `
            <div class="fixed inset-0 bg-gray-900/55 backdrop-blur-[1px]" data-modal-backdrop></div>
            <div class="relative bg-white rounded-2xl shadow-2xl ring-1 ring-black/5 max-w-xl w-full mx-auto overflow-hidden">
                <div class="flex items-start justify-between gap-3 border-b border-[#faa21b]/15 bg-[#fff9ec] px-5 py-4">
                    <h2 id="event-detail-title" class="text-base sm:text-lg font-semibold text-gray-900">&nbsp;</h2>
                    <button type="button" data-modal-close class="inline-flex items-center rounded-md border border-[#faa21b]/30 bg-white px-2.5 py-1 text-xs font-semibold text-[#b36b00] hover:bg-[#fff3da] transition">Fermer</button>
                </div>
                <div id="event-detail-body" class="max-h-[70vh] overflow-auto p-5 text-gray-800"></div>
                <div id="event-detail-footer" class="border-t border-gray-100 px-5 py-3 text-sm text-gray-600"></div>
            </div>
        `;
        document.body.appendChild(wrapper);
    }

    function trapFocus(modal){
        const focusable = 'a[href], area[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]), [tabindex]:not([tabindex="-1"])';
        const nodes = Array.from(modal.querySelectorAll(focusable)).filter(n => n.offsetParent !== null);
        if (!nodes.length) return function(){};
        let first = nodes[0];
        let last = nodes[nodes.length-1];
        function keyHandler(e){
            if (e.key !== 'Tab') return;
            if (e.shiftKey) {
                if (document.activeElement === first) { e.preventDefault(); last.focus(); }
            } else {
                if (document.activeElement === last) { e.preventDefault(); first.focus(); }
            }
        }
        document.addEventListener('keydown', keyHandler);
        return function cleanup(){ document.removeEventListener('keydown', keyHandler); };
    }

    function openModal(triggerEl){
        createModalIfMissing();
        const modal = document.getElementById('event-detail-modal');
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.setAttribute('aria-hidden','false');
        const closeBtn = modal.querySelector('[data-modal-close]');
        if (closeBtn) closeBtn.focus();
        const cleanup = trapFocus(modal);
        function onClose(){
            cleanup();
            closeModal(triggerEl);
        }
        modal.__closeHandler = onClose;
        // attach backdrop and close handlers
        modal.querySelectorAll('[data-modal-close]').forEach(btn => btn.addEventListener('click', onClose));
        const backdrop = modal.querySelector('[data-modal-backdrop]');
        if (backdrop) backdrop.addEventListener('click', onClose);
        document.addEventListener('keydown', modal.__escHandler = function(e){ if (e.key === 'Escape') onClose(); });
        modal.__trigger = triggerEl || null;
    }

    function closeModal(triggerEl){
        const modal = document.getElementById('event-detail-modal');
        if (!modal) return;
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden','true');
        // cleanup handlers
        if (modal.__closeHandler) { /* already called */ }
        if (modal.__escHandler) { document.removeEventListener('keydown', modal.__escHandler); delete modal.__escHandler; }
        // return focus
        const trigger = modal.__trigger || triggerEl;
        if (trigger && typeof trigger.focus === 'function') trigger.focus();
        // clear content after delay to reduce layout jank
        setTimeout(()=>{
            const title = modal.querySelector('#event-detail-title'); if (title) title.textContent = '';
            const body = modal.querySelector('#event-detail-body'); if (body) body.innerHTML = '';
            const footer = modal.querySelector('#event-detail-footer'); if (footer) footer.innerHTML = '';
        }, 200);
    }

    async function fetchAndShow(id, url, trigger){
        createModalIfMissing();
        const modal = document.getElementById('event-detail-modal');
        const titleEl = modal.querySelector('#event-detail-title');
        const bodyEl = modal.querySelector('#event-detail-body');
        const footerEl = modal.querySelector('#event-detail-footer');

        // show loading
        titleEl.textContent = '...';
        bodyEl.innerHTML = '<div class="py-12 text-center text-sm font-medium text-gray-500">Chargement…</div>';
        footerEl.innerHTML = '';

        openModal(trigger);

        try {
            // Try HTML partial first (server-rendered Blade partial). Fallback to JSON mapping.
            // Prefer server-rendered HTML partial by requesting text/html and marking as XHR.
            const res = await fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'text/html', 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) throw new Error('Network');
            const ct = res.headers.get('content-type') || '';

            if (ct.includes('text/html')) {
                // Insert server-side partial HTML (server-side template should already escape content where needed)
                const html = await res.text();
                bodyEl.innerHTML = html;

                // Derive a sensible title from rendered HTML if available
                try {
                    const tmp = document.createElement('div'); tmp.innerHTML = html;
                    const h1 = tmp.querySelector('h1'); if (h1) titleEl.textContent = h1.textContent.trim();
                } catch (e) { /* ignore */ }

                // If the server included attachments/footer inside the partial, leave them; otherwise clear footer
                const footerPresent = bodyEl.querySelector('[data-event-attachments], [data-event-footer]');
                if (!footerPresent) footerEl.innerHTML = '';

                return;
            }

            // Fallback to JSON
            const data = await res.json();

            // map JSON safely to DOM (avoid innerHTML when possible)
            titleEl.textContent = data.title || '';

            const meta = document.createElement('div');
            meta.className = 'flex flex-wrap items-center gap-4 text-gray-700 mb-4';
            const dateSpan = document.createElement('span');
            dateSpan.className = 'inline-flex items-center px-3 py-1 rounded-full bg-cyan-100 text-cyan-800 text-sm font-medium';
            dateSpan.textContent = (data.start_at ? (new Date(data.start_at)).toLocaleString() : '');
            if (data.end_at) dateSpan.textContent += ' → ' + (new Date(data.end_at)).toLocaleString();
            meta.appendChild(dateSpan);
            if (data.location) {
                const loc = document.createElement('span'); loc.className = 'inline-flex items-center px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-sm font-medium'; loc.textContent = data.location; meta.appendChild(loc);
            }
            if (data.type) {
                const t = document.createElement('span'); t.className = 'inline-flex items-center px-3 py-1 rounded-full bg-purple-100 text-purple-800 text-sm font-medium'; t.textContent = data.type; meta.appendChild(t);
            }

            const desc = document.createElement('div'); desc.className = 'prose max-w-none text-gray-800 bg-white/80 rounded-lg p-4';
            desc.textContent = data.description || '';

            bodyEl.innerHTML = '';
            bodyEl.appendChild(meta);
            bodyEl.appendChild(desc);

            // attachments
            footerEl.innerHTML = '';
            if (data.attachments && data.attachments.length) {
                const att = document.createElement('div'); att.className = 'space-y-2';
                data.attachments.forEach(a => {
                    const link = document.createElement('a');
                    link.href = a.url; link.textContent = a.name || a.url; link.className = 'text-cyan-600 hover:underline block';
                    att.appendChild(link);
                });
                footerEl.appendChild(att);
            }

        } catch (err) {
            bodyEl.innerHTML = '<div class="rounded-xl border border-red-200 bg-red-50 p-4 text-center text-sm font-medium text-red-600">Erreur lors du chargement de l\'événement.</div>';
            titleEl.textContent = 'Erreur';
            footerEl.innerHTML = '';
            console.warn('[event-modal] failed to load event', err);
        }
    }

    // expose global helper
    window.openEventDetailModal = function(id, url, trigger){
        // Accept either a full URL or an ID; prefer URL when provided
        if (typeof url === 'string' && url.length) {
            return fetchAndShow(id, url, trigger);
        }
        if (typeof id !== 'undefined') {
            return fetchAndShow(id, '/events/'+encodeURIComponent(id), trigger);
        }
    };

    window.openReunionDetailModal = function(reunion, trigger){
        createModalIfMissing();
        const modal = document.getElementById('event-detail-modal');
        if (!modal) {
            return;
        }

        const titleEl = modal.querySelector('#event-detail-title');
        const bodyEl = modal.querySelector('#event-detail-body');
        const footerEl = modal.querySelector('#event-detail-footer');

        titleEl.textContent = 'Détails de la réunion';
        renderMinimalReunion(bodyEl, footerEl, reunion || {});
        openModal(trigger || null);
    };

    // allow programmatic close
    window.closeEventDetailModal = function(){ closeModal(); };
})();
