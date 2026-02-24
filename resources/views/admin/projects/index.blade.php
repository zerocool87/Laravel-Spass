<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Gestion des projets') }}"
            subtitle="{{ __('Administration des projets territoriaux') }}"
            icon="📋"
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

            {{-- Statistics --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl border border-[#faa21b]/20 p-5 shadow-sm">
                    <p class="text-sm text-gray-500">{{ __('Total des projets') }}</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-white rounded-xl border border-[#faa21b]/20 p-5 shadow-sm">
                    <p class="text-sm text-gray-500">{{ __('Projets actifs') }}</p>
                    <p class="text-2xl font-bold text-[#faa21b] mt-1">{{ $stats['active'] }}</p>
                </div>
                <div class="bg-white rounded-xl border border-[#faa21b]/20 p-5 shadow-sm">
                    <p class="text-sm text-gray-500">{{ __('Budget total (actifs)') }}</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format((float) $stats['total_budget'], 2, ',', ' ') }} €</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-[#faa21b]/20 p-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">{{ __('Projets') }}</h2>
                        <p class="text-sm text-gray-500">{{ __('Gérez, créez et supprimez les projets') }}</p>
                    </div>
                    <a href="{{ route('admin.projects.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-[#faa21b] text-white rounded-xl font-semibold text-sm shadow-lg hover:shadow-xl hover:-translate-y-0.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#faa21b] transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        {{ __('Nouveau projet') }}
                    </a>
                </div>

                <form method="GET" action="{{ route('admin.projects.index') }}" class="mt-6 border-t border-[#faa21b]/10 pt-6">
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-12 lg:items-end">
                        <div class="lg:col-span-4">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Rechercher') }}</label>
                            <input
                                id="search"
                                name="search"
                                type="search"
                                value="{{ request('search') }}"
                                placeholder="{{ __('Titre ou description…') }}"
                                class="w-full rounded-lg border-gray-200 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]"
                            />
                        </div>

                        <div class="lg:col-span-2">
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Type') }}</label>
                            <select id="type" name="type" class="w-full rounded-lg border-gray-200 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]">
                                <option value="">{{ __('Tous les types') }}</option>
                                @foreach($types as $key => $label)
                                    <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="lg:col-span-2">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Statut') }}</label>
                            <select id="status" name="status" class="w-full rounded-lg border-gray-200 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]">
                                <option value="">{{ __('Tous les statuts') }}</option>
                                @foreach($statuses as $key => $label)
                                    <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="lg:col-span-2">
                            <label for="commune" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Commune') }}</label>
                            <select id="commune" name="commune" class="w-full rounded-lg border-gray-200 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]">
                                <option value="">{{ __('Toutes les communes') }}</option>
                                @foreach($communes as $commune)
                                    <option value="{{ $commune }}" {{ request('commune') === $commune ? 'selected' : '' }}>{{ $commune }}</option>
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
                            @if(request()->hasAny(['search', 'type', 'status', 'commune']))
                                <a
                                    href="{{ route('admin.projects.index') }}"
                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-semibold text-sm shadow hover:shadow-md transition"
                                >
                                    {{ __('Réinitialiser') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                <div class="mt-6 border-t border-[#faa21b]/10 pt-6">
                    @if($projects->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-[#faa21b]/5">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-[#faa21b] uppercase tracking-wider">{{ __('Titre') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-[#faa21b] uppercase tracking-wider">{{ __('Type') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-[#faa21b] uppercase tracking-wider">{{ __('Statut') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-[#faa21b] uppercase tracking-wider">{{ __('Budget') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-[#faa21b] uppercase tracking-wider">{{ __('Échéance') }}</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-[#faa21b] uppercase tracking-wider">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @foreach($projects as $project)
                                        <tr class="hover:bg-orange-50/40 transition">
                                            <td class="px-6 py-4">
                                                <a href="{{ route('admin.projects.show', $project) }}" class="text-sm font-semibold text-gray-900 hover:text-[#faa21b] transition">
                                                    {{ $project->title }}
                                                </a>
                                                @if($project->commune)
                                                    <p class="text-xs text-gray-500 mt-0.5">{{ $project->commune }}</p>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600">
                                                {{ $project->type_label }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $project->status_color }}">
                                                    {{ $project->status_label }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900 font-variant-numeric tabular-nums">
                                                {{ $project->formatted_budget }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                {{ $project->end_date?->format('d/m/Y') ?? '—' }}
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <a
                                                        href="{{ route('admin.projects.show', $project) }}"
                                                        class="inline-flex items-center px-3 py-1.5 bg-gray-50 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-100 transition"
                                                        title="{{ __('Voir') }}"
                                                    >
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                    </a>
                                                    <a
                                                        href="{{ route('admin.projects.edit', $project) }}"
                                                        class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-100 transition"
                                                        title="{{ __('Modifier') }}"
                                                    >
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </a>
                                                     <button
                                                         type="button"
                                                         class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-700 rounded-lg text-sm font-medium hover:bg-red-100 transition"
                                                         title="{{ __('Supprimer') }}"
                                                         x-on:click="$dispatch('open-modal', 'confirm-delete-project-{{ $project->id }}')"
                                                     >
                                                         <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                         </svg>
                                                     </button>

                                                     <x-modal name="confirm-delete-project-{{ $project->id }}" :show="false">
                                                         <div class="p-6">
                                                             <div class="flex flex-col items-center text-center">
                                                                 <svg class="w-10 h-10 text-red-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                 </svg>
                                                                 <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Supprimer le projet') }}</h3>
                                                                 <p class="text-sm text-gray-600 mb-4">
                                                                     {{ __('Êtes-vous sûr de vouloir supprimer ce projet ?') }}<br>
                                                                     {{ __('Cette action est irréversible.') }}
                                                                 </p>
                                                                 <div class="flex items-center justify-center gap-3">
                                                                     <button type="button" class="px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition" x-on:click="$dispatch('close-modal', 'confirm-delete-project-{{ $project->id }}')">
                                                                         {{ __('Annuler') }}
                                                                     </button>
                                                                     <form method="POST" action="{{ route('admin.projects.destroy', $project) }}">
                                                                         @csrf
                                                                         @method('DELETE')
                                                                         <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg font-semibold text-sm shadow hover:bg-red-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-red-600 transition">
                                                                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                             </svg>
                                                                             {{ __('Supprimer') }}
                                                                         </button>
                                                                     </form>
                                                                 </div>
                                                             </div>
                                                         </div>
                                                     </x-modal>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            {{ $projects->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('Aucun projet') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ __('Commencez par créer un nouveau projet.') }}</p>
                            <div class="mt-6">
                                <a href="{{ route('admin.projects.create') }}" class="inline-flex items-center px-4 py-2 bg-[#faa21b] text-white rounded-lg font-semibold text-sm shadow hover:shadow-md transition">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    {{ __('Nouveau projet') }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
