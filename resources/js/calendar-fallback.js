// Helper to attach a delegated click handler to a calendar container that proxies clicks on day cells
// This is intentionally small and side-effect free so it's easy to test with Vitest/jsdom.
export function attachDayClickProxy(rootEl, handler) {
    if (!rootEl || typeof handler !== 'function') return () => {};

    function onClick(e) {
        const cell = e.target.closest('.fc-daygrid-day, .fc-daygrid-day-frame, .fc-day');
        if (!cell) return;
        // prefer data-date attribute used by FullCalendar
        let dateIso = cell.getAttribute('data-date') || (cell.dataset && cell.dataset.date) || null;
        // if clicked element is an inner frame, try to find parent day element
        if (!dateIso) {
            const parentDay = cell.closest('.fc-daygrid-day');
            if (parentDay) dateIso = parentDay.getAttribute('data-date') || (parentDay.dataset && parentDay.dataset.date) || null;
        }
        if (!dateIso) return;
        handler(dateIso, { dayEl: cell, jsEvent: e });
    }

    rootEl.addEventListener('click', onClick);

    // return a disposer for convenience
    return function dispose() { rootEl.removeEventListener('click', onClick); };
}
