import { describe, it, expect, vi } from 'vitest';
import { attachDayClickProxy } from '../../resources/js/calendar-fallback';

function makeDayCell(date) {
    const div = document.createElement('div');
    div.className = 'fc-daygrid-day';
    div.setAttribute('data-date', date);
    // FullCalendar nests a frame element; include it
    const frame = document.createElement('div');
    frame.className = 'fc-daygrid-day-frame';
    frame.appendChild(document.createTextNode(date));
    div.appendChild(frame);
    return div;
}

describe('attachDayClickProxy', () => {
    it('calls handler with the date when a day cell is clicked', () => {
        const root = document.createElement('div');
        document.body.appendChild(root);

        const cell = makeDayCell('2026-01-27');
        root.appendChild(cell);

        const handler = vi.fn();
        const dispose = attachDayClickProxy(root, handler);

        // simulate click on inner frame
        const frame = cell.querySelector('.fc-daygrid-day-frame');
        frame.dispatchEvent(new MouseEvent('click', { bubbles: true }));

        expect(handler).toHaveBeenCalledTimes(1);
        expect(handler).toHaveBeenCalledWith('2026-01-27', expect.any(Object));

        dispose();
        root.remove();
    });

    it('does not call handler when clicking outside day cell', () => {
        const root = document.createElement('div');
        document.body.appendChild(root);

        const inner = document.createElement('div');
        inner.className = 'not-a-day';
        root.appendChild(inner);

        const handler = vi.fn();
        const dispose = attachDayClickProxy(root, handler);

        inner.dispatchEvent(new MouseEvent('click', { bubbles: true }));

        expect(handler).not.toHaveBeenCalled();

        dispose();
        root.remove();
    });
});
