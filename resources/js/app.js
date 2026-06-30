import './bootstrap';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

Alpine.plugin(collapse);

import './calendar';
import './modal';
import './document-preview-modal';
import './event-detail-modal';
import initConfetti from './confetti';

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

    Alpine.store('confetti', initConfetti());
});

Alpine.start();
