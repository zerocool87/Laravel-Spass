import './bootstrap';

import Alpine from 'alpinejs';

import './calendar';
import './modal';
import './event-detail-modal';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.store('toasts', {
        items: [],
        nextId: 0,

        add(message, type = 'success') {
            const id = this.nextId++;
            this.items.push({ id, message, type });
            setTimeout(() => this.remove(id), 4000);
        },

        remove(id) {
            this.items = this.items.filter(t => t.id !== id);
        },
    });
});

Alpine.start();
