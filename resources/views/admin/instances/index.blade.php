<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Gestion des instances') }}"
            subtitle="{{ __('Administration des instances') }}"
            icon="🏛️"
            :backRoute="route('elus.dashboard')"
            :backLabel="__('Retour au tableau de bord')"
            activeSection="admin"
        />
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-2xl shadow-sm flex items-start gap-3">
                    <div class="mt-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-2xl shadow-sm flex items-start gap-3">
                    <div class="mt-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <span class="font-semibold">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-lg border border-[#faa21b]/20 p-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">{{ __('Instances') }}</h2>
                        <p class="text-sm text-gray-500">{{ __('Gérez, créez et supprimez les instances') }}</p>
                    </div>
                    <a href="{{ route('admin.instances.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-[#faa21b] text-white rounded-xl font-semibold text-sm shadow-lg hover:shadow-xl hover:-translate-y-0.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#faa21b] transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        {{ __('Nouvelle instance') }}
                    </a>
                </div>

                <form method="GET" action="{{ route('admin.instances.index') }}" class="mt-6 border-t border-[#faa21b]/10 pt-6">
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-12 lg:items-end">
                        <div class="lg:col-span-4">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Rechercher') }}</label>
                            <input
                                id="search"
                                name="search"
                                type="search"
                                value="{{ request('search') }}"
                                placeholder="{{ __('Nom ou description...') }}"
                                class="w-full rounded-lg border-gray-200 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]"
                            />
                        </div>

                        <div class="lg:col-span-3">
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Type') }}</label>
                            <select
                                id="type"
                                name="type"
                                class="w-full rounded-lg border-gray-200 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]"
                            >
                                <option value="">{{ __('Tous les types') }}</option>
                                @foreach($types as $key => $label)
                                    <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="lg:col-span-3">
                            <label for="territory" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Territoire') }}</label>
                            <select
                                id="territory"
                                name="territory"
                                class="w-full rounded-lg border-gray-200 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]"
                            >
                                <option value="">{{ __('Tous les territoires') }}</option>
                                @foreach($territories as $territory)
                                    <option value="{{ $territory }}" {{ request('territory') === $territory ? 'selected' : '' }}>
                                        {{ $territory }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="lg:col-span-2 flex gap-2">
                            <button
                                type="submit"
                                class="flex-1 px-4 py-2 bg-[#faa21b] text-white rounded-lg font-semibold text-sm shadow hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#faa21b] transition"
                            >
                                {{ __('Filtrer') }}
                            </button>
                            @if(request()->hasAny(['search', 'type', 'territory']))
                                <a
                                    href="{{ route('admin.instances.index') }}"
                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-semibold text-sm shadow hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-gray-400 transition"
                                >
                                    {{ __('Réinitialiser') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                <div class="mt-6 border-t border-[#faa21b]/10 pt-6">
                    @if($instances->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($instances as $instance)
                                <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl border border-gray-200 p-5 hover:shadow-lg transition-all duration-200">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                                {{ $instance->name }}
                                            </h3>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#faa21b]/10 text-[#faa21b]">
                                                {{ $instance->type_label }}
                                            </span>
                                        </div>
                                    </div>

                                    @if($instance->territory)
                                        <div class="flex items-center text-sm text-gray-600 mb-2">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            {{ $instance->territory }}
                                        </div>
                                    @endif

                                    @if($instance->description)
                                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                            {{ $instance->description }}
                                        </p>
                                    @endif

                                    <div class="flex items-center text-sm text-gray-500 mb-4">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ $instance->reunions_count }} {{ __('réunion(s)') }}
                                    </div>

                                    <div class="flex gap-2 pt-3 border-t border-gray-200">
                                        <a
                                            href="{{ route('admin.instances.edit', $instance) }}"
                                            class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-blue-50 text-blue-700 rounded-lg font-medium text-sm hover:bg-blue-100 transition"
                                        >
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            {{ __('Modifier') }}
                                        </a>
                                        <form
                                            method="POST"
                                            action="{{ route('admin.instances.destroy', $instance) }}"
                                            onsubmit="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer cette instance ?') }}')"
                                            class="flex-1"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="w-full inline-flex items-center justify-center px-3 py-2 bg-red-50 text-red-700 rounded-lg font-medium text-sm hover:bg-red-100 transition"
                                            >
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                {{ __('Supprimer') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $instances->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('Aucune instance') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ __('Commencez par créer une nouvelle instance.') }}</p>
                            <div class="mt-6">
                                <a href="{{ route('admin.instances.create') }}" class="inline-flex items-center px-4 py-2 bg-[#faa21b] text-white rounded-lg font-semibold text-sm shadow hover:shadow-md transition">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    {{ __('Nouvelle instance') }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
