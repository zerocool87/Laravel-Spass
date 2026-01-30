<div id="admin-event-modal"
    class="fixed inset-0 flex items-center justify-center"
    style="display: none; z-index: 9999;">
    <div class="absolute inset-0 bg-black/60" data-modal-close></div>
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

            <div class="mb-3">
                <label class="block text-sm">{{ __('Type') }}</label>
                <select id="ae-type" name="type" class="form-control w-full">
                    <option value="">-- {{ __('Choose a type') }} --</option>
                    @foreach(config('events.types', []) as $type)
                        <option value="{{ $type }}" {{ $type === 'Autre' ? 'selected' : '' }}>{{ __($type) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-3">
                <x-primary-button type="submit">{{ __('Create') }}</x-primary-button>
                <x-secondary-button type="button" data-modal-close>{{ __('Cancel') }}</x-secondary-button>
            </div>
        </form>
    </div>
</div>

{{-- Modal logic moved to resources/js/modal.js — do not add inline JS here. --}}
