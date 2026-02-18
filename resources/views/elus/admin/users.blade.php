<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Gestion des utilisateurs') }}"
            subtitle="{{ __('Administration des élus') }}"
            icon="👥"
            :backRoute="route('elus.admin.index')"
            :backLabel="__('Retour à l\'administration')"
            activeSection="admin"
            :showNav="false"
        >

        </x-elus-header>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('temporaryPassword'))
                <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border-2 border-yellow-400 text-yellow-900 px-6 py-4 rounded-xl shadow-lg">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 mr-3 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <div class="flex-1">
                            <h4 class="font-bold text-lg mb-2">Mot de passe temporaire pour {{ session('newUserName') }}</h4>
                            <p class="mb-3">Copiez ce mot de passe maintenant, il ne sera plus affiché :</p>
                            <div class="bg-white border-2 border-yellow-300 rounded-lg p-4 font-mono text-lg font-bold text-gray-900 select-all break-all">
                                {{ session('temporaryPassword') }}
                            </div>
                            <p class="mt-3 text-sm text-yellow-800">
                                ⚠️ Ce mot de passe doit être changé lors de la première connexion.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-400 text-green-800 px-6 py-4 rounded-xl flex items-center shadow-lg">
                    <svg class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-lg border border-[#faa21b]/20">
                <div class="flex flex-col gap-4 px-6 pt-6 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">{{ __('Gestion des utilisateurs') }}</h2>
                        <p class="text-sm text-gray-500">{{ __('Filtrer, modifier et gérer les rôles') }}</p>
                    </div>
                    <button type="button" onclick="document.getElementById('create-elu-modal').classList.remove('hidden')" class="inline-flex items-center justify-center px-5 py-2.5 bg-[#faa21b] text-white rounded-xl font-semibold text-sm shadow-lg hover:shadow-xl transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        {{ __('Nouvel élu') }}
                    </button>
                </div>

                {{-- Filters --}}
                <form method="GET" action="{{ route('elus.admin.users') }}" class="mt-6 border-t border-[#faa21b]/10 px-6 py-6">
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-[1.5fr_0.7fr_auto] items-end">
                        <div class="min-w-[200px]">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Recherche') }}</label>
                            <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Nom, email, commune...') }}" class="w-full rounded-lg border-gray-200 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]">
                        </div>
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Rôle') }}</label>
                            <select id="role" name="role" class="w-full rounded-lg border-gray-200 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]">
                            <option value="">{{ __('Tous les rôles') }}</option>
                            <option value="elu" {{ request('role') == 'elu' ? 'selected' : '' }}>{{ __('Élus uniquement') }}</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>{{ __('Administrateurs') }}</option>
                            <option value="standard" {{ request('role') == 'standard' ? 'selected' : '' }}>{{ __('Utilisateurs standard') }}</option>
                            </select>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-[#faa21b] border border-transparent rounded-lg font-semibold text-sm text-white shadow hover:bg-[#f39b14] transition">
                                {{ __('Filtrer') }}
                            </button>
                            @if(request()->hasAny(['search', 'role']))
                                <a href="{{ route('elus.admin.users') }}" class="text-sm font-medium text-gray-600 hover:text-gray-800">{{ __('Réinitialiser') }}</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            {{-- Users Table --}}
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Liste des utilisateurs') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('Mettez à jour les rôles et informations') }}</p>
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ __('Total') }}: {{ $users->total() }}
                    </div>
                </div>
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-[#faa21b]/10">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[#a85f00] uppercase tracking-wider">{{ __('Utilisateur') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[#a85f00] uppercase tracking-wider">{{ __('Rôles') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[#a85f00] uppercase tracking-wider">{{ __('Fonction') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[#a85f00] uppercase tracking-wider">{{ __('Commune') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-[#a85f00] uppercase tracking-wider">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-[#faa21b]/5 transition">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @if($user->is_admin)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-[#faa21b]/20 text-[#b36b00]">Admin</span>
                                        @endif
                                        @if($user->is_elu)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Élu</span>
                                        @endif
                                        @if(!$user->is_admin && !$user->is_elu)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Standard</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $user->fonction ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $user->commune ?? '-' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('elus.admin.users.edit', $user) }}" class="inline-flex items-center px-3 py-1 border border-[#faa21b] rounded-full text-xs font-semibold text-[#faa21b] hover:bg-[#faa21b]/10">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                            </svg>
                                            {{ __('Modifier') }}
                                        </a>
                                        <form method="POST" action="{{ route('elus.admin.users.toggle-elu', $user) }}">
                                            @csrf
                                            @method('PATCH')
                                            @if($user->is_elu)
                                                <button type="submit" class="inline-flex items-center px-3 py-1 border border-red-200 rounded-full text-xs font-semibold text-red-600 hover:bg-red-50" onclick="return confirm('{{ __('Retirer le statut élu à cet utilisateur ?') }}')">
                                                    {{ __('Retirer élu') }}
                                                </button>
                                            @else
                                                <button type="submit" class="inline-flex items-center px-3 py-1 border border-green-200 rounded-full text-xs font-semibold text-green-600 hover:bg-green-50">
                                                    {{ __('Ajouter élu') }}
                                                </button>
                                            @endif
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    {{ __('Aucun utilisateur trouvé') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    {{-- Create Élu Modal --}}
    <div id="create-elu-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('create-elu-modal').classList.add('hidden')"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form method="POST" action="{{ route('elus.admin.users.store') }}">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Créer un nouvel élu') }}</h3>

                        <div class="space-y-4">
                            <div>
                                <label for="modal-name" class="block text-sm font-medium text-gray-700">{{ __('Nom') }} *</label>
                                <input type="text" name="name" id="modal-name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]">
                            </div>

                            <div>
                                <label for="modal-email" class="block text-sm font-medium text-gray-700">{{ __('Email') }} *</label>
                                <input type="email" name="email" id="modal-email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]">
                            </div>

                            <div>
                                <label for="modal-fonction" class="block text-sm font-medium text-gray-700">{{ __('Fonction') }}</label>
                                <input type="text" name="fonction" id="modal-fonction" placeholder="{{ __('Ex: Maire, Conseiller municipal...') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]">
                            </div>

                            <div>
                                <label for="modal-commune" class="block text-sm font-medium text-gray-700">{{ __('Commune') }}</label>
                                <select name="commune" id="modal-commune" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]">
                                    <option value="">{{ __('Sélectionner une commune') }}</option>
                                    @foreach($communes as $commune)
                                        <option value="{{ $commune }}" {{ old('commune') === $commune ? 'selected' : '' }}>{{ $commune }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <p class="mt-4 text-sm text-gray-500">
                            {{ __('Un mot de passe temporaire sera généré et affiché après la création.') }}
                        </p>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#faa21b] text-base font-medium text-white hover:bg-[#f39b14] focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            {{ __('Créer l\'élu') }}
                        </button>
                        <button type="button" onclick="document.getElementById('create-elu-modal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            {{ __('Annuler') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
