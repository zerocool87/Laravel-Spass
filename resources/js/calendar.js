import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import frLocale from '@fullcalendar/core/locales/fr';


document.addEventListener('DOMContentLoaded', function () {
    const els = document.querySelectorAll('[data-feed-url]');

    els.forEach(function (el) {
        const feedUrl = el.dataset.feedUrl || '/events/json';
        const mode = el.dataset.mode || 'full'; // 'full' or 'mini'

        const defaultOptions = {
            plugins: [dayGridPlugin, timeGridPlugin, listPlugin],
            // Use French locale when the page language starts with 'fr'
            locale: (document.documentElement.lang && document.documentElement.lang.startsWith('fr')) ? frLocale : undefined,
            initialView: mode === 'mini' ? 'listWeek' : 'dayGridMonth',
            headerToolbar: mode === 'mini' ? { left: '', center: 'title', right: '' } : { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek' },
            events: {
                url: feedUrl,
                method: 'GET'
            },
            eventDidMount: function(info) {
                info.el.classList.add('fc-event-sober');
            },
            height: mode === 'mini' ? 240 : 'auto'
        };

        const calendar = new Calendar(el, defaultOptions);
        calendar.render();

        // expose instance for UI toggles
        el._fcCalendar = calendar;
        el._fcMode = mode;

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
});
