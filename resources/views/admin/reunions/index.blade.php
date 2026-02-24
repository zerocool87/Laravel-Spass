<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Gestion des réunions') }}"
            subtitle="{{ __('Administration des réunions') }}"
            icon="📅"
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

            <!-- Filtres -->
            <div class="bg-white rounded-2xl shadow-lg border border-[#faa21b]/20 p-6">
                <form method="GET" action="{{ route('admin.reunions.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                        <div class="lg:col-span-2">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Titre, description..." class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]">
                        </div>
                        <div>
                            <label for="instance_id" class="block text-sm font-medium text-gray-700 mb-1">Instance</label>
                            <select name="instance_id" id="instance_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]">
                                <option value="">Toutes les instances</option>
                                @foreach($instances as $instance)
                                    <option value="{{ $instance->id }}" {{ request('instance_id') == $instance->id ? 'selected' : '' }}>
                                        {{ $instance->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                            <select name="status" id="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]">
                                <option value="">Tous les statuts</option>
                                @foreach($statuses as $key => $label)
                                    <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="from_date" class="block text-sm font-medium text-gray-700 mb-1">Du</label>
                            <input type="date" name="from_date" id="from_date" value="{{ request('from_date') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]">
                        </div>
                        <div>
                            <label for="to_date" class="block text-sm font-medium text-gray-700 mb-1">Au</label>
                            <input type="date" name="to_date" id="to_date" value="{{ request('to_date') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]">
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#faa21b] text-white rounded-lg font-semibold text-sm shadow hover:shadow-md transition">
                            Filtrer
                        </button>
                        <a href="{{ route('admin.reunions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-semibold text-sm shadow hover:bg-gray-200 transition">
                            Réinitialiser
                        </a>
                    </div>
                </form>
            </div>

            <!-- Actions -->
            <div class="flex justify-end">
                <a href="{{ route('admin.reunions.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-[#faa21b] text-white rounded-xl font-semibold text-sm shadow-lg hover:shadow-xl hover:-translate-y-0.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#faa21b] transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nouvelle réunion
                </a>
            </div>

            <!-- Liste des réunions -->
            <div class="bg-white rounded-2xl shadow-lg border border-[#faa21b]/20 overflow-hidden">
                <table class="min-w-full divide-y divide-[#faa21b]/10">
                    <thead class="bg-[#faa21b]/5">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lieu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-[#faa21b]/10">
                        @forelse($reunions as $reunion)
                            <tr class="hover:bg-[#faa21b]/5 transition">
                                 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $reunion->start_time ? $reunion->start_time->format('d/m/Y') : '-' }}
                                    <span class="text-gray-500 text-xs block">{{ $reunion->start_time ? $reunion->start_time->format('H:i') : '-' }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $reunion->title }}
                                    @if($reunion->description)
                                        <p class="text-gray-500 text-xs mt-1 truncate max-w-xs">{{ $reunion->description }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $reunion->instance->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $reunion->location ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $reunion->status_color }}">
                                        {{ $reunion->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.reunions.edit', $reunion) }}" class="inline-flex items-center gap-1 px-3 py-1 border border-gray-200 rounded-lg text-xs font-semibold text-gray-700 hover:border-gray-300 hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#faa21b] transition">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Modifier
                                        </a>
                                        <form method="POST" action="{{ route('admin.reunions.destroy', $reunion) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette réunion ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-1 border border-red-200 rounded-lg text-xs font-semibold text-red-700 hover:bg-red-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-red-500 transition">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    Aucune réunion trouvée.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $reunions->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
