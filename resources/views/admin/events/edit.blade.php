<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900">{{ __('Edit Event') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="glass p-4">
                <form method="POST" action="{{ route('admin.events.update', $event) }}">
                    @csrf
                    @method('PATCH')
                    @include('admin.events.form', ['event' => $event])
                </form>
                <form id="delete-event-form" method="POST" action="{{ route('admin.events.destroy', $event) }}" class="mt-4">
                    @csrf
                    @method('DELETE')
                    <x-danger-button type="button" onclick="if(confirm('{{ __('Are you sure you want to delete this event?') }}')) document.getElementById('delete-event-form').submit();">
                        {{ __('Delete') }}
                    </x-danger-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>