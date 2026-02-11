import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from '@fullcalendar/interaction';
import frLocale from '@fullcalendar/core/locales/fr';


document.addEventListener('DOMContentLoaded', function () {
    const els = document.querySelectorAll('[data-feed-url]');

    function parseBool(v) {
        if (v === undefined || v === null) return false;
        return v === '1' || v === 'true' || v === true;
    }

    // Keep a lightweight listener that tracks which calendar opened the modal (for add/remove operations)
    window.addEventListener('admin:event-open', function(e){
        try {
            window.__lastCalendarId = e?.detail?.calendarId || null;
        } catch (err) {
            window.__lastCalendarId = null;
        }
    });

    // messages for i18n (French fallback)
    const isFr = (document.documentElement.lang && document.documentElement.lang.startsWith('fr'));
    const msgs = isFr ? {
        authMessage: 'Connexion requise pour afficher le calendrier.',
        deleteTitle: 'Supprimer',
        deleteAria: "Supprimer l'événement",
        deleteConfirm: "Supprimer l'événement ?",
        couldNotDelete: "Impossible de supprimer l'événement.",
        networkError: "Erreur réseau lors de la suppression.",
        initFailed: "Échec de l'initialisation du calendrier. Voir la console pour les détails.",
        notInitializedHint: "Le calendrier n'est pas encore initialisé. Assurez-vous que le bundle JS est chargé et vérifiez la console pour les erreurs."
    } : {
        authMessage: 'Login required to view the calendar.',
        deleteTitle: 'Delete',
        deleteAria: 'Delete event',
        deleteConfirm: 'Delete event?',
        couldNotDelete: 'Could not delete event.',
        networkError: 'Network error while deleting.',
        initFailed: 'Calendar failed to initialize. See console for details.',
        notInitializedHint: 'Calendar not initialized yet. Ensure JS bundle loaded and check console for errors.'
    };

    els.forEach(function (el) {
        const feedUrl = el.dataset.feedUrl || '/events/json';

        // Mark admin calendar elements so CSS can target them more easily
        if (el.id && el.id === 'admin-events-calendar') {
            el.classList.add('admin-calendar');
        }
        const mode = el.dataset.mode || 'full';
        const canEdit = parseBool(el.dataset.canEdit);
        const createUrl = el.dataset.createUrl || '';
        const editBase = el.dataset.editBase || '';

        // Helper: apply event colors and list styling
        function applyEventColors(info) {
            const props = info.event.extendedProps || {};
            let col = null;

            if (props.status) {
                const statusColors = {
                    'Planifiée': { bg: '#3b82f6', border: '#2563eb' },
                    'Confirmée': { bg: '#22c55e', border: '#16a34a' },
                    'Terminée':  { bg: '#6b7280', border: '#4b5563' },
                    'Annulée':   { bg: '#ef4444', border: '#dc2626' },
                };
                col = statusColors[props.status];
            }

            if (!col) {
                const type = props.type || 'Autre';
                const typeColors = {
                    'Reunion': { bg: '#2563eb', border: '#1d4ed8' }, // blue
                    'Bureau': { bg: '#dc2626', border: '#b91c1c' }, // red
                    'Commissions': { bg: '#059669', border: '#047857' }, // green
                    'Assemblée pleniere': { bg: '#f59e42', border: '#b45309' }, // orange
                    'Autre': { bg: '#a21caf', border: '#701a75' } // purple
                };
                col = typeColors[type] || typeColors['Autre'];
            }

            if (col) {
                try { info.el.style.setProperty('background-color', col.bg, 'important'); } catch (e) { info.el.style.backgroundColor = col.bg; }
                try { info.el.style.setProperty('border-color', col.border, 'important'); } catch (e) { info.el.style.borderColor = col.border; }
                try { info.el.style.setProperty('color', 'white', 'important'); } catch (e) { info.el.style.color = 'white'; }
                try {
                    const main = info.el.querySelector('.fc-list-event-main');
                    if (main) {
                        main.style.backgroundColor = col.bg;
                        main.style.borderColor = col.border;
                        main.style.color = 'white';
                        main.style.padding = '0.35rem 0.6rem';
                        main.style.borderRadius = '0.375rem';
                        main.style.display = 'flex';
                        main.style.alignItems = 'center';
                        main.style.gap = '0.75rem';
                    }
                } catch (e) { }
            }
        }

        // Helper: format time in event display
        function setEventTimeDisplay(info) {
            try {
                if (info.event.allDay) return;
                const startDate = info.event.start || (info.event.startStr && new Date(info.event.startStr));
                if (!startDate) return;
                const hhmm = startDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
                const timeEls = info.el.querySelectorAll('.fc-time, .fc-list-item-time');
                if (timeEls && timeEls.length) {
                    timeEls.forEach(function(te){ te.textContent = hhmm; });
                } else {
                    const possible = info.el.querySelectorAll('span, div');
                    for (let i = 0; i < possible.length; i++) {
                        const el2 = possible[i];
                        if (/\d{1,2}:\d{2}/.test(el2.textContent)) { el2.textContent = hhmm; break; }
                    }
                }
            } catch (err) { }
        }

        function showAuthMessage(containerEl) {
            if (!containerEl || containerEl.querySelector('.calendar-auth-message')) return;
            const box = document.createElement('div');
            box.className = 'calendar-auth-message text-sm text-gray-300 p-4 rounded border border-gray-700 mt-3';
            box.textContent = msgs.authMessage;
            containerEl.appendChild(box);
        }

        const defaultOptions = {
            plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
            // Use French locale when the page language starts with 'fr'
            // Force French locale
            locale: frLocale,
            // Map mode to a sensible initial view: 'mini' -> listWeek, 'compact' -> dayGridMonth, 'week' -> timeGridWeek, otherwise month
            initialView: (mode === 'mini') ? 'listWeek' : (mode === 'compact' ? 'dayGridMonth' : (mode === 'week' ? 'timeGridWeek' : 'dayGridMonth')),
            headerToolbar: (mode === 'mini') ? { left: 'prev', center: 'title', right: 'next' } : (mode === 'compact' ? { left: 'prev', center: 'title', right: 'next' } : (mode === 'week' ? { left: 'prev today', center: 'title', right: 'next timeGridWeek,dayGridMonth' } : { left: 'prev today', center: 'title', right: 'next dayGridMonth,timeGridWeek,timeGridDay,listWeek' })),

            aspectRatio: 1.6,
            dayMaxEventRows: 3,
            eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
            events: function(fetchInfo, successCallback, failureCallback){
                // Use fetch with same-origin credentials so authenticated dashboards can load events
                fetch(feedUrl, { method: 'GET', credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
                    .then(function(res){
                        if (!res.ok) throw new Error('Network response was not ok');
                        const ct = res.headers.get('content-type') || '';
                        if (!ct.includes('application/json') && !ct.includes('json')) {
                            // likely redirected to login (HTML)
                            showAuthMessage(el);
                            throw new Error('Expected JSON response (got '+ct+')');
                        }
                        return res.json();
                    })
                    .then(function(data){ successCallback(data); })
                    .catch(function(err){
                        console.warn('[calendar] failed to load events', err);
                        if (typeof failureCallback === 'function') failureCallback(err);
                    });
            },
            selectable: canEdit,

            eventClick: function(info) {
                // If admin, go to admin edit page
                if (canEdit && editBase && info.event && info.event.id) {
                    window.location = editBase + '/' + info.event.id + '/edit';
                    if (info.jsEvent && typeof info.jsEvent.preventDefault === 'function') info.jsEvent.preventDefault();
                    return;
                }

                // If event has a URL, prevent full navigation and open modal instead (progressive enhancement: href remains for non-JS)
                if (info.event && info.event.url) {
                    try {
                        if (info.jsEvent && typeof info.jsEvent.preventDefault === 'function') info.jsEvent.preventDefault();
                    } catch (err) { }

                    // open modal via a global helper that will fetch event details via JSON
                    if (typeof window.openEventDetailModal === 'function') {
                        window.openEventDetailModal(info.event.id, info.event.url, info.jsEvent && info.jsEvent.currentTarget ? info.jsEvent.currentTarget : null);
                    } else {
                        // fallback: navigate normally if helper missing
                        window.location = info.event.url;
                    }
                    return;
                }
            },
            eventDidMount: function(info) {
                // base class for subtle styling
                info.el.classList.add('fc-event-sober');

                // Apply colors and format time in a smaller, testable way
                try { applyEventColors(info); } catch (e) { /* ignore */ }
                try { setEventTimeDisplay(info); } catch (e) { /* ignore */ }

                // if event data couldn't be loaded (auth), we don't attempt further DOM tweaks
                // (auth message is handled during the fetch step)


                // Add delete button for admins
                if (canEdit && info.event && info.event.id) {
                    // ensure relative positioning
                    info.el.style.position = 'relative';

                    const del = document.createElement('button');
                    del.setAttribute('type', 'button');
                    del.setAttribute('title', msgs.deleteTitle);
                    del.setAttribute('aria-label', msgs.deleteAria);
                    del.className = 'fc-event-delete';
                    del.style.position = 'absolute';
                    del.style.top = '4px';
                    del.style.right = '4px';
                    del.style.width = '20px';
                    del.style.height = '20px';
                    del.style.borderRadius = '50%';
                    del.style.border = 'none';
                    del.style.background = 'rgba(0,0,0,0.45)';
                    del.style.color = 'white';
                    del.style.fontWeight = '700';
                    del.style.lineHeight = '20px';
                    del.style.cursor = 'pointer';
                    del.textContent = '×';

                    del.addEventListener('click', async function(e){
                        e.stopPropagation();

                        if (!confirm(msgs.deleteConfirm)) return;

                        const tokenEl = document.querySelector('meta[name="csrf-token"]');
                        const token = tokenEl ? tokenEl.getAttribute('content') : '';
                        const delUrl = editBase + '/' + info.event.id;

                        try {
                            const res = await fetch(delUrl, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': token,
                                    'Accept': 'application/json'
                                }
                            });

                            if (res.ok || res.status === 204) {
                                try { info.event.remove(); } catch (err) {}
                                window.dispatchEvent(new CustomEvent('admin:event-deleted', { detail: { id: info.event.id } }));
                            } else {
                                alert(msgs.couldNotDelete);
                            }
                        } catch (err) {
                            alert(msgs.networkError);
                        }
                    });

                    info.el.appendChild(del);
                }
            },
            height: 'auto'
        };

        // Insert a small loading spinner while the calendar initializes
        let spinner = document.createElement('div');
        spinner.className = 'calendar-spinner';
        spinner.setAttribute('aria-hidden', 'true');
        spinner.style.marginBottom = '8px';
        el.appendChild(spinner);
        // Define dateClick handler as a closed-over function (set after render to avoid Unknown option error)
        function handleDateClick(info) {
            if (!canEdit) return;
            const start = info.dateStr; // ISO date string
            const calendarId = el.id || null;

            if (window.CALENDAR_DEBUG) console.info('[calendar] dateClick', calendarId, start, info);

            // brief visual flash on the clicked cell to show clickability
            const cell = info.dayEl || (info.jsEvent && info.jsEvent.target && info.jsEvent.target.closest && info.jsEvent.target.closest('.fc-daygrid-day')) || null;
            if (cell) {
                try {
                    cell.classList.add('fc-day-clicked');
                    setTimeout(function(){ if (cell && cell.classList) cell.classList.remove('fc-day-clicked'); }, 250);
                } catch (err) { /* ignore */ }
            }

            // Notify listeners that an admin requested to open create modal for this calendar
            window.dispatchEvent(new CustomEvent('admin:event-open', { detail: { start: start, calendarId } }));

            if (window.CALENDAR_DEBUG) console.info('[calendar] trying to open modal for', start);

            // Try helper function first
            if (typeof window.openEventCreateModal === 'function') {
                if (window.CALENDAR_DEBUG) console.info('[calendar] calling openEventCreateModal', start);
                window.openEventCreateModal(start);
                if (info.jsEvent && typeof info.jsEvent.preventDefault === 'function') {
                    info.jsEvent.preventDefault();
                }
            } else if (createUrl) {
                // Fallback: redirect to create URL if modal helper not available
                if (window.CALENDAR_DEBUG) console.warn('[calendar] openEventCreateModal not found, redirecting');
                window.location = createUrl + '?start=' + encodeURIComponent(start);
                if (info.jsEvent && typeof info.jsEvent.preventDefault === 'function') {
                    info.jsEvent.preventDefault();
                }
            }
        }

        // Initialize calendar WITHOUT dateClick to avoid "Unknown option" error
        const calendar = new Calendar(el, defaultOptions);

        try {
            calendar.render();

            // Ensure container visibility after render based on data-visible
            const hasVisibleFlag = Object.prototype.hasOwnProperty.call(el.dataset, 'visible');
            if (!hasVisibleFlag || parseBool(el.dataset.visible)) {
                try { el.style.display = 'block'; } catch (e) { /* ignore */ }
                try { el.classList.remove('hidden'); } catch (e) { /* ignore */ }
            } else {
                // allow toggles to keep it hidden when explicitly requested
                try { el.style.display = 'none'; } catch (e) { /* ignore */ }
            }

            // expose instance for UI toggles
            el._fcCalendar = calendar;
            el._fcMode = mode;
            el.dataset.initialized = '1';
            if (window.CALENDAR_DEBUG) console.info('[calendar] initialized', el.id || el);

            // Attach dateClick using FullCalendar's native option (set after render to avoid unknown option errors)
            if (canEdit) {
                try {
                    calendar.setOption('dateClick', handleDateClick);
                    if (window.CALENDAR_DEBUG) console.info('[calendar] dateClick set via calendar.setOption');
                } catch (err) {
                    if (window.CALENDAR_DEBUG) console.warn('[calendar] failed to set dateClick via setOption, falling back to delegated click', err);
                    // Fallback: delegated listener
                    el.addEventListener('click', function(e){
                        const cell = e.target.closest('.fc-daygrid-day, .fc-daygrid-day-frame, .fc-day');
                        if (!cell) return;
                        const dateIso = cell.getAttribute('data-date') || (cell.dataset && cell.dataset.date) || null;
                        if (!dateIso) return;
                        handleDateClick({ dateStr: dateIso, dayEl: cell, jsEvent: e });
                    });
                    if (window.CALENDAR_DEBUG) console.info('[calendar] dateClick delegated listener attached (fallback)');
                }
            }

            // remove spinner
            try { spinner.remove(); } catch (err) {}
        } catch (err) {
            console.error('[calendar] failed to render', err, el);
            try { spinner.remove(); } catch (err) {}

            if (!el.querySelector('.calendar-init-hint')) {
                const hint = document.createElement('div');
                hint.className = 'calendar-init-hint text-sm text-red-400 mt-2';
                hint.textContent = msgs.initFailed;
                el.appendChild(hint);
            }
        }

    });

    // Optional debug check: if enabled, report any un-initialized calendar placeholders to help debugging
    if (window.CALENDAR_DEBUG) {
        setTimeout(function(){
            document.querySelectorAll('[data-feed-url]').forEach(function(el){
                if (!el.dataset.initialized) {
                    // insert a small hint box if nothing else
                    if (!el.querySelector('.calendar-init-hint')) {
                        const hint = document.createElement('div');
                        hint.className = 'calendar-init-hint text-sm text-gray-300 p-4';
                        hint.textContent = msgs.notInitializedHint;
                        el.appendChild(hint);
                    }
                    console.warn('[calendar] not initialized element found', el);
                }
            });
        }, 1000);
    }
});
