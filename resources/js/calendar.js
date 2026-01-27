import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
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

    els.forEach(function (el) {
        const feedUrl = el.dataset.feedUrl || '/events/json';
        const mode = el.dataset.mode || 'full'; // 'full' or 'mini'

        const canEdit = parseBool(el.dataset.canEdit);
        const createUrl = el.dataset.createUrl || '';
        const editBase = el.dataset.editBase || '';

        const defaultOptions = {
            plugins: [dayGridPlugin, timeGridPlugin, listPlugin],
            // Use French locale when the page language starts with 'fr'
            locale: (document.documentElement.lang && document.documentElement.lang.startsWith('fr')) ? frLocale : undefined,
            initialView: mode === 'mini' ? 'listWeek' : 'dayGridMonth',
            headerToolbar: mode === 'mini' ? { left: '', center: 'title', right: '' } : { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek' },
            aspectRatio: 1.6,
            dayMaxEventRows: 3,
            eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
            events: {
                url: feedUrl,
                method: 'GET'
            },
            selectable: canEdit,
            dateClick: function(info) {
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

                if (window.openEventCreateModal) {
                    if (window.CALENDAR_DEBUG) console.info('[calendar] calling openEventCreateModal', start);
                    // Open the modal (prefill handled by the modal helper)
                    window.openEventCreateModal(start);
                    if (info.jsEvent && typeof info.jsEvent.preventDefault === 'function') {
                        info.jsEvent.preventDefault();
                    }
                } else if (createUrl) {
                    // Fallback: redirect to create page
                    window.location = createUrl + '?start=' + encodeURIComponent(start);
                }
            },
            eventClick: function(info) {
                // If admin, go to admin edit page; otherwise follow event url
                if (canEdit && editBase && info.event && info.event.id) {
                    window.location = editBase + '/' + info.event.id + '/edit';
                    if (info.jsEvent && typeof info.jsEvent.preventDefault === 'function') info.jsEvent.preventDefault();
                    return;
                }
                if (info.event && info.event.url) {
                    // default behavior
                    return;
                }
            },
            eventDidMount: function(info) {
                // base class for subtle styling
                info.el.classList.add('fc-event-sober');

                // color customization: all-day events use softer color
                try {
                    if (info.event.allDay) {
                        info.el.style.backgroundColor = '#0ea5a6'; // teal-500
                        info.el.style.borderColor = '#0891b2';
                    } else {
                        info.el.style.backgroundColor = '#0369a1'; // blue-700
                        info.el.style.borderColor = '#075985';
                    }
                    info.el.style.color = 'white';
                } catch (err) {
                    // ignore styling errors
                }

                // Add delete button for admins
                if (canEdit && info.event && info.event.id) {
                    // ensure relative positioning
                    info.el.style.position = 'relative';

                    const del = document.createElement('button');
                    del.setAttribute('type', 'button');
                    del.setAttribute('title', 'Delete');
                    del.setAttribute('aria-label', 'Delete event');
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

                        if (!confirm('Delete event?')) return;

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
                                alert('Could not delete event.');
                            }
                        } catch (err) {
                            alert('Network error while deleting.');
                        }
                    });

                    info.el.appendChild(del);
                }
            },
            height: mode === 'mini' ? 240 : 'auto'
        };

        // Insert a small loading spinner while the calendar initializes
        let spinner = document.createElement('div');
        spinner.className = 'calendar-spinner';
        spinner.setAttribute('aria-hidden', 'true');
        spinner.style.marginBottom = '8px';
        el.appendChild(spinner);

        const calendar = new Calendar(el, defaultOptions);
        try {
            calendar.render();
            // expose instance for UI toggles
            el._fcCalendar = calendar;
            el._fcMode = mode;
            el.dataset.initialized = '1';
            if (window.CALENDAR_DEBUG) console.info('[calendar] initialized', el.id || el);
            // remove spinner
            try { spinner.remove(); } catch (err) {}
        } catch (err) {
            console.error('[calendar] failed to render', err, el);
            // remove spinner and show inline hint for users (non-invasive)
            try { spinner.remove(); } catch (err) {}

            // Fallback for FullCalendar builds that reject unknown options (e.g. 'dateClick')
            try {
                if (err && /Unknown option\s+'?dateClick'?/i.test(err.message || '')) {
                    if (window.CALENDAR_DEBUG) console.warn('[calendar] unknown option dateClick detected, retrying without it');
                    const opts = Object.assign({}, defaultOptions);
                    delete opts.dateClick;
                    const fallback = new Calendar(el, opts);
                    // Attach delegated click handler to day cells to emulate dateClick
                    el.addEventListener('click', function(e){
                        const cell = e.target.closest('.fc-daygrid-day, .fc-daygrid-day-frame, .fc-day');
                        if (!cell) return;
                        const dateIso = cell.getAttribute('data-date') || cell.dataset.date || null;
                        if (!dateIso) return;
                        if (window.CALENDAR_DEBUG) console.info('[calendar] delegated dateClick', dateIso);
                        // emulate the original info object
                        const info = { dateStr: dateIso, dayEl: cell, jsEvent: e };
                        try { if (typeof defaultOptions.dateClick === 'function') defaultOptions.dateClick(info); } catch (err) { /* ignore */ }
                    });

                    fallback.render();
                    el._fcCalendar = fallback;
                    el._fcMode = mode;
                    el.dataset.initialized = '1';
                    if (!el.querySelector('.calendar-init-hint')) {
                        const hint = document.createElement('div');
                        hint.className = 'calendar-init-hint text-sm text-yellow-300 mt-2';
                        hint.textContent = 'Calendar initialized with fallback mode; day clicks are proxied.';
                        el.appendChild(hint);
                    }
                    if (window.CALENDAR_DEBUG) console.info('[calendar] fallback initialized', el.id || el);
                    // nothing more to do
                    return;
                }
            } catch (err2) {
                console.error('[calendar] fallback init failed', err2, el);
            }

            if (!el.querySelector('.calendar-init-hint')) {
                const hint = document.createElement('div');
                hint.className = 'calendar-init-hint text-sm text-red-400 mt-2';
                hint.textContent = 'Calendar failed to initialize. See console for details.';
                el.appendChild(hint);
            }
        }

        el._fcToggle = function(newMode) {
            if (!el._fcCalendar) return;
            if (newMode === el._fcMode) return;
            if (newMode === 'mini') {
                el._fcCalendar.setOption('headerToolbar', { left: '', center: 'title', right: '' });
                el._fcCalendar.changeView('listWeek');
                el._fcCalendar.setOption('height', 240);
            } else {
                el._fcCalendar.setOption('headerToolbar', { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek' });
                el._fcCalendar.changeView('dayGridMonth');
                el._fcCalendar.setOption('height', 'auto');
            }
            el._fcMode = newMode;
        };
    });

    // When an admin creates an event via modal, add it to the calendar(s)
    window.addEventListener('admin:event-created', function(e) {
        const json = e.detail || {};
        const targetId = window.__lastCalendarId || null;

        if (targetId) {
            const targetEl = document.getElementById(targetId);
            if (targetEl && targetEl._fcCalendar) {
                targetEl._fcCalendar.addEvent(json);
                return;
            }
        }

        // Fallback: add to all calendars on the page
        els.forEach(function(el) {
            if (el._fcCalendar) {
                try { el._fcCalendar.addEvent(json); } catch (err) { /* ignore */ }
            }
        });
    });

    // global helper for simple toggles
    window.toggleCalendarView = function(id, mode) {
        const el = document.getElementById(id);
        if (!el) return;
        if (el._fcToggle) {
            el._fcToggle(mode);
        } else {
            // save desired mode to dataset to be applied when initialized
            el.dataset.mode = mode;
        }
    };

    // Optional debug check: if enabled, report any un-initialized calendar placeholders to help debugging
    if (window.CALENDAR_DEBUG) {
        setTimeout(function(){
            document.querySelectorAll('[data-feed-url]').forEach(function(el){
                if (!el.dataset.initialized) {
                    // insert a small hint box if nothing else
                    if (!el.querySelector('.calendar-init-hint')) {
                        const hint = document.createElement('div');
                        hint.className = 'calendar-init-hint text-sm text-gray-300 p-4';
                        hint.textContent = 'Calendar not initialized yet. Ensure JS bundle loaded and check console for errors.';
                        el.appendChild(hint);
                    }
                    console.warn('[calendar] not initialized element found', el);
                }
            });
        }, 1000);
    }
});
