<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Gestion des utilisateurs') }}"
            subtitle="{{ __('Administration des élus et territoires') }}"
            icon="👥"
            :backRoute="route('elus.admin.index')"
            :backLabel="__('Retour à l\'administration')"
            activeSection="admin"
            :showNav="false"
        >
            <x-slot name="actions">
                <button type="button" onclick="document.getElementById('create-elu-modal').classList.remove('hidden')" class="inline-flex items-center px-6 py-3 bg-white text-[#faa21b] border border-transparent rounded-xl font-bold text-sm hover:bg-white/90 transition shadow-lg hover:shadow-xl transform hover:scale-105">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    {{ __('Nouvel élu') }}
                </button>
            </x-slot>
        </x-elus-header>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-400 text-green-800 px-6 py-4 rounded-xl flex items-center shadow-lg">
                    <svg class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Filters --}}
            <div class="bg-white rounded-xl shadow-lg border-2 border-[#faa21b]/20 mb-6 p-6">
                <form method="GET" action="{{ route('elus.admin.users') }}" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Rechercher par nom, email, commune...') }}" class="w-full rounded-lg border-[#faa21b]/30 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]">
                    </div>
                    <div>
                        <select name="role" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">{{ __('Tous les rôles') }}</option>
                            <option value="elu" {{ request('role') == 'elu' ? 'selected' : '' }}>{{ __('Élus uniquement') }}</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>{{ __('Administrateurs') }}</option>
                            <option value="standard" {{ request('role') == 'standard' ? 'selected' : '' }}>{{ __('Utilisateurs standard') }}</option>
                        </select>
                    </div>
                    <div>
                        <select name="territory" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">{{ __('Tous les territoires') }}</option>
                            @foreach($territories as $territory)
                                <option value="{{ $territory }}" {{ request('territory') == $territory ? 'selected' : '' }}>{{ $territory }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                            {{ __('Filtrer') }}
                        </button>
                        @if(request()->hasAny(['search', 'role', 'territory']))
                            <a href="{{ route('elus.admin.users') }}" class="ml-2 text-sm text-gray-600 hover:text-gray-800">{{ __('Réinitialiser') }}</a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Users Table --}}
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Utilisateur') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Rôles') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Fonction') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Commune') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Territoire') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @if($user->is_admin)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">Admin</span>
                                        @endif
                                        @if($user->is_elu)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Élu</span>
                                        @endif
                                        @if(!$user->is_admin && !$user->is_elu)
                                            <span class="text-gray-400 text-sm">Standard</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $user->fonction ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $user->commune ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    <form method="POST" action="{{ route('elus.admin.users.territory', $user) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="text" name="territory" value="{{ $user->territory }}" placeholder="{{ __('Territoire...') }}" class="text-sm rounded border-gray-300 w-32 focus:border-blue-500 focus:ring-blue-500">
                                        <button type="submit" class="text-blue-600 hover:text-blue-800">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <form method="POST" action="{{ route('elus.admin.users.toggle-elu', $user) }}">
                                            @csrf
                                            @method('PATCH')
                                            @if($user->is_elu)
                                                <button type="submit" class="inline-flex items-center px-3 py-1 border border-red-300 rounded text-xs font-medium text-red-600 hover:bg-red-50" onclick="return confirm('{{ __('Retirer le statut élu à cet utilisateur ?') }}')">
                                                    {{ __('Retirer élu') }}
                                                </button>
                                            @else
                                                <button type="submit" class="inline-flex items-center px-3 py-1 border border-green-300 rounded text-xs font-medium text-green-600 hover:bg-green-50">
                                                    {{ __('Ajouter élu') }}
                                                </button>
                                            @endif
                                        </form>
                                        <a href="{{ route('admin.users.edit', $user) }}" class="text-gray-600 hover:text-gray-900">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
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
                                <input type="text" name="name" id="modal-name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="modal-email" class="block text-sm font-medium text-gray-700">{{ __('Email') }} *</label>
                                <input type="email" name="email" id="modal-email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="modal-fonction" class="block text-sm font-medium text-gray-700">{{ __('Fonction') }}</label>
                                <input type="text" name="fonction" id="modal-fonction" placeholder="{{ __('Ex: Maire, Conseiller municipal...') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="modal-commune" class="block text-sm font-medium text-gray-700">{{ __('Commune') }}</label>
                                <input type="text" name="commune" id="modal-commune" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="modal-territory" class="block text-sm font-medium text-gray-700">{{ __('Territoire') }}</label>
                                <input type="text" name="territory" id="modal-territory" placeholder="{{ __('Ex: Zone Nord, Centre...') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <p class="mt-4 text-sm text-gray-500">
                            {{ __('Un mot de passe temporaire sera généré et affiché après la création.') }}
                        </p>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
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
