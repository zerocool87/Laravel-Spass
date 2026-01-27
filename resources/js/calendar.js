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

    els.forEach(function (el) {
        const feedUrl = el.dataset.feedUrl || '/events/json';
        const mode = el.dataset.mode || 'full'; // 'full' or 'mini'

        const canEdit = parseBool(el.dataset.canEdit);
        const createUrl = el.dataset.createUrl || '';
        const editBase = el.dataset.editBase || '';

        const defaultOptions = {
            plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
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

                // color customization by event type
                try {
                    const type = (info.event.extendedProps && info.event.extendedProps.type) || 'autre';
                    const typeColors = {
                        'assemblee': { bg: '#7c3aed', border: '#6d28d9' }, // violet
                        'bureau': { bg: '#dc2626', border: '#b91c1c' },    // red
                        'commissions': { bg: '#059669', border: '#047857' }, // green
                        'autre': { bg: '#0369a1', border: '#075985' }      // blue
                    };
                    const col = typeColors[type] || typeColors['autre'];
                    try { info.el.style.setProperty('background-color', col.bg, 'important'); } catch (e) { info.el.style.backgroundColor = col.bg; }
                    try { info.el.style.setProperty('border-color', col.border, 'important'); } catch (e) { info.el.style.borderColor = col.border; }
                    try { info.el.style.setProperty('color', 'white', 'important'); } catch (e) { info.el.style.color = 'white'; }
                } catch (err) {
                    // ignore styling errors
                }

                // Ensure only the time (HH:MM) is shown for event time displays
                try {
                    if (!info.event.allDay) {
                        const startDate = info.event.start || (info.event.startStr && new Date(info.event.startStr));
                        if (startDate) {
                            const hhmm = startDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
                            // FullCalendar uses .fc-time for time display in many views
                            const timeEls = info.el.querySelectorAll('.fc-time, .fc-list-item-time');
                            if (timeEls && timeEls.length) {
                                timeEls.forEach(function(te){ te.textContent = hhmm; });
                            } else {
                                // Fallback: try to find any text node that looks like a time and replace it
                                // (best-effort, avoid breaking structure)
                                const possible = info.el.querySelectorAll('span, div');
                                for (let i = 0; i < possible.length; i++) {
                                    const el = possible[i];
                                    if (/\d{1,2}:\d{2}/.test(el.textContent)) {
                                        el.textContent = hhmm;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                } catch (err) {
                    // ignore formatting errors
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
            } else {
                // Fallback: manually open modal via Alpine or style
                if (window.CALENDAR_DEBUG) console.warn('[calendar] openEventCreateModal not found, using fallback');
                const modal = document.getElementById('admin-event-modal');
                if (modal) {
                    // Pre-fill form
                    let dt = start;
                    if (/^\d{4}-\d{2}-\d{2}$/.test(dt)) dt = dt + 'T09:00';
                    try { document.getElementById('ae-title').value = ''; } catch(e){}
                    try { document.getElementById('ae-start_at').value = dt; } catch(e){}
                    try {
                        let endTime = new Date(dt);
                        endTime.setHours(endTime.getHours() + 1);
                        document.getElementById('ae-end_at').value = endTime.toISOString().slice(0, 16);
                    } catch(e){}
                    
                    // Open modal
                    if (modal.__x && modal.__x.$data) {
                        modal.__x.$data.open = true;
                        if (window.CALENDAR_DEBUG) console.info('[calendar] opened via Alpine');
                    } else {
                        modal.style.display = 'flex';
                        if (window.CALENDAR_DEBUG) console.info('[calendar] opened via style');
                    }
                } else if (createUrl) {
                    // Last resort: redirect
                    window.location = createUrl + '?start=' + encodeURIComponent(start);
                }
                
                if (info.jsEvent && typeof info.jsEvent.preventDefault === 'function') {
                    info.jsEvent.preventDefault();
                }
            }
        }

        // Initialize calendar WITHOUT dateClick to avoid "Unknown option" error
        const calendar = new Calendar(el, defaultOptions);
        
        try {
            calendar.render();
            
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
