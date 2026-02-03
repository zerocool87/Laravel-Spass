// Event detail modal helper
window.openEventDetailModal = async function(id, url, triggerEl) {
    if (window.CALENDAR_DEBUG) console.info('[event-detail] open', id, url, triggerEl);

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
        document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeModal(); });
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
    Array.from(panel.querySelectorAll('[data-event-detail-close]')).forEach(function(btn){ btn.addEventListener('click', closeModal); });

    function closeModal() {
        try { modal.classList.add('hidden'); modal.setAttribute('data-open', '0'); } catch (e) {}
    }

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
