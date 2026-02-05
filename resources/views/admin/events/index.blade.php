<x-app-layout>
    <x-slot name="header">
        <x-admin-header
            title="{{ __('Events Calendar') }}"
            icon="📅"
        />
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="glass p-4">
                {{-- Debug flag and embedded calendar for admin users --}}
                <script>window.CALENDAR_DEBUG = {{ config('app.debug') ? 'true' : 'false' }};</script>
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-2">Admin Calendar <span class="text-sm font-normal">{{ __('(Full management)') }}</span></h3>
                    <div id="admin-events-calendar"
                         data-feed-url="{{ route('events.json') }}"
                         data-mode="full"
                         data-can-edit="1"
                         data-create-url="{{ route('admin.events.create') }}"
                         data-edit-base="{{ route('admin.events.index') }}"
                         ></div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full cyber-table">
                        <thead>
                            <tr>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Start') }}</th>
                                <th>{{ __('End') }}</th>
                                <th>{{ __('Location') }}</th>
                                <th class="w-48">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($events as $event)
                                <tr>
                                    <td>{{ $event->title }}</td>
                                    <td>{{ $event->start_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $event->end_at?->format('Y-m-d H:i') }}</td>
                                    <td>{{ $event->location }}</td>
                                    <td class="flex items-center gap-2">
                                        <x-secondary-button href="{{ route('admin.events.edit', $event) }}">{{ __('Edit') }}</x-secondary-button>
                                        <form action="{{ route('admin.events.destroy', $event) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <x-danger-button type="submit">{{ __('Delete') }}</x-danger-button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">{{ __('No events found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($events->hasPages())
                    <div class="mt-4">
                        {{ $events->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('events._admin_create_modal')
</x-app-layout>
