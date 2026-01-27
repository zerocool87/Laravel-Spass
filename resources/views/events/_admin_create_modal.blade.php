@can('admin')
<div x-data="{ open: false }" @admin:event-open.window="open = true" x-show="open" x-cloak id="admin-event-modal" class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/50" @click="open = false"></div>
    <div class="bg-gray-900 rounded p-6 z-10 w-full max-w-xl">
        <h3 class="text-lg font-semibold mb-4">{{ __('Create Event') }}</h3>

        <div id="admin-event-errors" class="text-sm text-red-400 mb-3" style="display:none"></div>

        <form id="admin-event-create-form" method="POST" action="{{ route('admin.events.store') }}">
            @csrf
            <div class="mb-3">
                <label class="block text-sm">{{ __('Title') }}</label>
                <input id="ae-title" name="title" class="form-control w-full" required />
            </div>

            <div class="mb-3">
                <label class="block text-sm">{{ __('Description') }}</label>
                <textarea id="ae-description" name="description" class="form-control w-full"></textarea>
            </div>

            <div class="mb-3 grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm">{{ __('Start') }}</label>
                    <input id="ae-start_at" name="start_at" type="datetime-local" class="form-control w-full" required />
                </div>
                <div>
                    <label class="block text-sm">{{ __('End') }}</label>
                    <input id="ae-end_at" name="end_at" type="datetime-local" class="form-control w-full" />
                </div>
            </div>

            <div class="mb-3 flex items-center gap-3">
                <input id="ae-is_all_day" type="checkbox" name="is_all_day" value="1" class="form-check-input" />
                <label for="ae-is_all_day" class="text-sm">{{ __('All day') }}</label>
            </div>

            <div class="mb-3">
                <label class="block text-sm">{{ __('Location') }}</label>
                <input id="ae-location" name="location" class="form-control w-full" />
            </div>

            <div class="flex items-center gap-3">
                <x-primary-button type="submit">{{ __('Create') }}</x-primary-button>
                <x-secondary-button type="button" @click="open = false">{{ __('Cancel') }}</x-secondary-button>
            </div>
        </form>
    </div>
</div>

<script>
// Expose a helper to open modal and prefill values
window.openEventCreateModal = function(startDateIso) {
    let el = document.getElementById('admin-event-modal');
    if (!el) return;

    // compute datetime-local value
    let dt = startDateIso;
    if (/^\d{4}-\d{2}-\d{2}$/.test(dt)) {
        dt = dt + 'T09:00'; // default 9am
    }

    // Pre-fill form
    document.getElementById('ae-title').value = '';
    document.getElementById('ae-description').value = '';
    document.getElementById('ae-start_at').value = dt;
    // Set end to 1 hour later
    let endTime = new Date(dt);
    endTime.setHours(endTime.getHours() + 1);
    document.getElementById('ae-end_at').value = endTime.toISOString().slice(0, 16);
    document.getElementById('ae-is_all_day').checked = false;
    document.getElementById('ae-location').value = '';

    // Dispatch event to open Alpine modal
    window.dispatchEvent(new CustomEvent('admin:event-open'));
};

// handle form submit via AJAX
document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('admin-event-create-form');
    if (!form) return;

    form.addEventListener('submit', async function(e){
        e.preventDefault();
        const action = form.action;
        const data = new FormData(form);
        // normalize is_all_day
        if (!data.get('is_all_day')) data.delete('is_all_day');

        const payload = {};
        data.forEach((v, k) => { payload[k] = v; });
        // fetch with JSON
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        try {
            const res = await fetch(action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify(payload)
            });

            if (res.status === 201 || res.ok) {
                const json = await res.json();
                // Dispatch a custom event so any calendar on the page can react (decoupled)
                window.dispatchEvent(new CustomEvent('admin:event-created', { detail: json }));
                // hide modal
                const modal = document.getElementById('admin-event-modal');
                if (modal && modal.__x && modal.__x.$data) {
                    modal.__x.$data.open = false;
                }
            } else if (res.status === 422) {
                const json = await res.json();
                const errs = json.errors || {};
                const el = document.getElementById('admin-event-errors');
                if (el) {
                    el.style.display = 'block';
                    el.innerHTML = Object.values(errs).flat().map(s => '<div>'+s+'</div>').join('');
                }
            } else {
                const el = document.getElementById('admin-event-errors');
                if (el) { el.style.display = 'block'; el.textContent = 'Error creating event'; }
            }
        } catch (err) {
            const el = document.getElementById('admin-event-errors');
            if (el) { el.style.display = 'block'; el.textContent = 'Network error'; }
        }
    });
});
</script>
@endcan