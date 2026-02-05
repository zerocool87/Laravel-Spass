<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('elus.dashboard') }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl leading-tight">{{ __('Instances') }}</h2>
            </div>
            @can('admin')
            <a href="{{ route('elus.instances.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                + {{ __('Nouvelle instance') }}
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Filters --}}
            <div class="bg-white rounded-lg shadow mb-6 p-4">
                <form method="GET" action="{{ route('elus.instances.index') }}" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Rechercher...') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <select name="type" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">{{ __('Tous les types') }}</option>
                            @foreach($types as $key => $label)
                                <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                            {{ __('Filtrer') }}
                        </button>
                        @if(request()->hasAny(['search', 'type', 'territory']))
                            <a href="{{ route('elus.instances.index') }}" class="ml-2 text-sm text-gray-600 hover:text-gray-800">{{ __('Réinitialiser') }}</a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Instances Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($instances as $instance)
                    <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $instance->type_label }}
                                    </span>
                                    <h3 class="mt-2 text-lg font-medium text-gray-900">{{ $instance->name }}</h3>
                                </div>
                                @can('admin')
                                <div class="flex space-x-2">
                                    <a href="{{ route('elus.instances.edit', $instance) }}" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                </div>
                                @endcan
                            </div>
                            @if($instance->description)
                                <p class="mt-2 text-sm text-gray-500 line-clamp-2">{{ $instance->description }}</p>
                            @endif
                            @if($instance->territory)
                                <p class="mt-2 text-xs text-gray-500">📍 {{ $instance->territory }}</p>
                            @endif
                            <div class="mt-4 flex items-center justify-between">
                                <span class="text-sm text-gray-500">{{ $instance->reunions_count }} {{ __('réunion(s)') }}</span>
                                <a href="{{ route('elus.instances.show', $instance) }}" class="text-sm text-blue-600 hover:text-blue-800">{{ __('Voir détails') }} →</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 bg-white rounded-lg shadow p-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('Aucune instance') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ __('Commencez par créer une nouvelle instance.') }}</p>
                        @can('admin')
                        <div class="mt-6">
                            <a href="{{ route('elus.instances.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                + {{ __('Nouvelle instance') }}
                            </a>
                        </div>
                        @endcan
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $instances->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
