<<<<<<< HEAD
// Event detail modal: accessible, fetches JSON and maps to DOM, progressive enhancement friendly
(function(){
    const SELECTOR = 'body';

    function createModalIfMissing(){
        if (document.getElementById('event-detail-modal')) return;
        const wrapper = document.createElement('div');
        wrapper.id = 'event-detail-modal';
        wrapper.className = 'fixed inset-0 z-50 hidden items-center justify-center p-4';
        wrapper.setAttribute('role','dialog');
        wrapper.setAttribute('aria-modal','true');
        wrapper.setAttribute('aria-hidden','true');
        wrapper.innerHTML = `
            <div class="fixed inset-0 bg-black/50" data-modal-backdrop></div>
            <div class="bg-white rounded-lg shadow-lg max-w-3xl w-full mx-auto overflow-hidden">
                <div class="flex items-start justify-between p-4 border-b">
                    <h2 id="event-detail-title" class="text-xl font-semibold text-gray-900">&nbsp;</h2>
                    <button type="button" data-modal-close class="ml-4 text-gray-500 hover:text-gray-700">Fermer</button>
                </div>
                <div id="event-detail-body" class="p-4 max-h-[70vh] overflow-auto text-gray-800"></div>
                <div id="event-detail-footer" class="p-4 border-t text-sm text-gray-600"></div>
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
        bodyEl.innerHTML = '<div class="py-12 text-center text-gray-500">Chargement…</div>';
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
            bodyEl.innerHTML = '<div class="py-12 text-center text-red-500">Erreur lors du chargement de l\'événement.</div>';
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

    // allow programmatic close
    window.closeEventDetailModal = function(){ closeModal(); };
})();
=======
// Event detail modal helper
// Event detail modal helper
window.openEventDetailModal = async function(id, url, triggerEl) {
    if (window.CALENDAR_DEBUG) console.info('[event-detail] open', id, url, triggerEl);

    // Define close function at module level
    function closeModal() {
        try {
            const modal = document.getElementById('event-detail-modal');
            if (modal) {
                modal.classList.add('hidden');
                modal.setAttribute('data-open', '0');
            }
        } catch (e) {}
    }

    // ensure modal container exists
    let modal = document.getElementById('event-detail-modal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'event-detail-modal';
        modal.className = 'fixed inset-0 flex items-center justify-center z-50 hidden';
        modal.style.background = 'rgba(0,0,0,0.5)';
        modal.innerHTML = '<div class="event-detail-modal-panel bg-white rounded-2xl shadow-2xl max-w-3xl w-full mx-4 p-8 border border-gray-100 relative overflow-hidden" role="dialog" aria-modal="true"></div>';
        document.body.appendChild(modal);

        // close on backdrop click
        modal.addEventListener('click', function(e){
            if (e.target === modal) {
                closeModal();
            }
        });

        // Attach escape key listener only once
        if (!modal.dataset.escapeAttached) {
            document.addEventListener('keydown', function(e){
                if (e.key === 'Escape') {
                    const modal = document.getElementById('event-detail-modal');
                    if (modal && modal.getAttribute('data-open') === '1') {
                        closeModal();
                    }
                }
            });
            modal.dataset.escapeAttached = 'true';
        }
    }

    const panel = modal.querySelector('.event-detail-modal-panel');
    if (!panel) return;

    // show a loading state
    panel.innerHTML = `
        <div class="p-12 text-center">
            <div class="calendar-spinner mx-auto mb-4"></div>
            <p class="text-gray-500 font-medium">Chargement des détails...</p>
        </div>
    `;
    modal.classList.remove('hidden');
    modal.setAttribute('data-open', '1');

    try {
        const res = await fetch(url, { method: 'GET', credentials: 'same-origin', headers: { 'Accept': 'application/json,text/html' } });
        const ct = res.headers.get('content-type') || '';

        if (ct.includes('application/json')) {
            const json = await res.json();
            // render simple detail UI
            const start = json.start ? new Date(json.start).toLocaleString() : '';
            const end = json.end ? new Date(json.end).toLocaleString() : '';
            const desc = json.description ? escapeHtml(json.description).replace(/\n/g, '<br/>') : '';
            panel.innerHTML = `
                <div class="event-detail-modal-content space-y-6">
                    <div class="border-b border-gray-100 pb-4 flex justify-between items-start gap-4">
                        <h2 class="text-3xl font-bold text-gray-900 leading-tight">${escapeHtml(json.title || '')}</h2>
                        <button aria-label="Fermer" data-event-detail-close class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center p-3 rounded-xl bg-gray-50 border border-gray-100 group">
                            <div class="p-2.5 rounded-lg bg-cyan-100 text-cyan-600 mr-4">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Date et Heure</p>
                                <p class="text-gray-900 font-medium">${start} ${end ? ' → ' + end : ''}</p>
                            </div>
                        </div>
                        <div class="flex items-center p-3 rounded-xl bg-gray-50 border border-gray-100">
                            <div class="p-2.5 rounded-lg bg-emerald-100 text-emerald-600 mr-4">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 12.414a4 4 0 10-1.414 1.414l4.243 4.243a1 1 0 001.414-1.414z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Lieu</p>
                                <p class="text-gray-900 font-medium">${escapeHtml(json.location || 'Non spécifié')}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-4 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                            Description
                        </h3>
                        <div class="prose prose-amber max-w-none text-gray-700 leading-relaxed">${desc}</div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="button" data-event-detail-close class="px-6 py-2.5 bg-white border-2 border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all active:scale-95 shadow-sm">
                            Fermer
                        </button>
                    </div>
                </div>
            `;
        } else {
            // assume HTML partial
            const text = await res.text();
            panel.innerHTML = `
                <div class="absolute top-4 right-4 z-10">
                    <button aria-label="Fermer" data-event-detail-close class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors shadow-sm bg-white border border-gray-100">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                ${text}
            `;
        }
    } catch (err) {
        console.warn('[event-detail] fetch failed', err);
        panel.innerHTML = '<div class="p-6 text-center text-red-500">Erreur de chargement</div>';
    }

    // attach close handlers
    Array.from(panel.querySelectorAll('[data-event-detail-close]')).forEach(function(btn){
        btn.addEventListener('click', closeModal);
    });

    function escapeHtml(s) {
        if (!s) return '';
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }
};
