<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Modifier un utilisateur') }}"
            subtitle="{{ __('Administration des élus') }}"
            icon="✏️"
            :backRoute="route('elus.admin.users')"
            :backLabel="__('Retour à la liste des utilisateurs')"
            activeSection="admin"
            :showNav="false"
        />
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => __('Accueil'), 'url' => route('elus.dashboard')], ['label' => __('Administration'), 'url' => route('elus.admin.index')], ['label' => __('Utilisateurs'), 'url' => route('elus.admin.users')], ['label' => $user->name]]" />
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-lg border border-[#faa21b]/20" x-data="{ tab: 'general' }">
                @php $profile = $user->eluProfile; @endphp

                {{-- Quick identity bar --}}
                <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="w-10 h-10 rounded-full bg-[#faa21b]/10 flex items-center justify-center flex-shrink-0">
                            <span class="text-[#faa21b] font-bold text-sm">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-900 truncate">{{ $user->prenom ? $user->prenom.' '.$user->name : $user->name }}</p>
                            <p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        @if($user->is_admin)
                            <span class="px-2 py-0.5 text-xs font-medium bg-[#faa21b]/20 text-[#b36b00] rounded-full">Admin</span>
                        @endif
                        @if($user->is_elu)
                            <span class="px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 rounded-full">Élu</span>
                        @endif
                    </div>
                </div>

                {{-- Tabs --}}
                <div class="flex border-b border-gray-100">
                    <button type="button" @click="tab = 'general'" :class="tab === 'general' ? 'border-[#faa21b] text-[#faa21b]' : 'border-transparent text-gray-500 hover:text-gray-700'" class="flex-1 sm:flex-none px-6 py-3 text-sm font-medium border-b-2 transition">
                        {{ __('Informations générales') }}
                    </button>
                    <button type="button" @click="tab = 'profile'" :class="tab === 'profile' ? 'border-[#faa21b] text-[#faa21b]' : 'border-transparent text-gray-500 hover:text-gray-700'" class="flex-1 sm:flex-none px-6 py-3 text-sm font-medium border-b-2 transition">
                        {{ __('Profil élu') }}
                    </button>
                </div>

                <form method="POST" action="{{ route('elus.admin.users.update', $user) }}">
                    @csrf
                    @method('PATCH')

                    {{-- Tab: General --}}
                    <div x-show="tab === 'general'" class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Nom') }} *</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Email') }} *</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                                @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="prenom" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Prénom') }}</label>
                                <input type="text" name="prenom" id="prenom" value="{{ old('prenom', $user->prenom) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                            </div>
                            <div>
                                <label for="commune" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Commune') }}</label>
                                <select name="commune" id="commune" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                                    <option value="">{{ __('Sélectionner une commune') }}</option>
                                    @foreach($communes as $commune)
                                        <option value="{{ $commune }}" {{ old('commune', $user->commune) === $commune ? 'selected' : '' }}>{{ $commune }}</option>
                                    @endforeach
                                </select>
                                @error('commune')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Titres / Fonctions') }}</label>
                                @php $userTitres = old('titres', $user->titres ?? []); @endphp
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-4 gap-y-1.5">
                                    @foreach(['Président', 'Vice-président', 'Membre du bureau', 'Membre de commission', 'Représentant', 'Délégué titulaire', 'Délégué suppléant'] as $titreOption)
                                        <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                                            <input type="checkbox" name="titres[]" value="{{ $titreOption }}"
                                                class="rounded border-gray-300 text-[#faa21b] shadow-sm focus:ring-[#faa21b]"
                                                {{ in_array($titreOption, $userTitres) ? 'checked' : '' }} />
                                            {{ $titreOption }}
                                        </label>
                                    @endforeach
                                </div>
                                @error('titres')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div class="md:col-span-2 grid grid-cols-2 gap-4">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Nouveau mot de passe') }}</label>
                                    <input type="password" name="password" id="password" placeholder="{{ __('Laisser vide pour ne pas modifier') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                                    @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Confirmer') }}</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="{{ __('Confirmer le mot de passe') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                                </div>
                            </div>
                            <div class="md:col-span-2 flex items-center gap-6">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_elu" id="is_elu" value="1" {{ old('is_elu', $user->is_elu) ? 'checked' : '' }} class="h-4 w-4 text-[#faa21b] border-gray-300 rounded focus:ring-[#faa21b]">
                                    <span class="ml-2 text-sm text-gray-700">{{ __('Élu') }}</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_admin" id="is_admin" value="1" {{ old('is_admin', $user->is_admin) ? 'checked' : '' }} class="h-4 w-4 text-[#faa21b] border-gray-300 rounded focus:ring-[#faa21b]">
                                    <span class="ml-2 text-sm text-gray-700">{{ __('Administrateur') }}</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Tab: Profile --}}
                    <div x-show="tab === 'profile'" class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            {{-- Identité --}}
                            <div class="space-y-4">
                                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ __('Identité') }}</h4>
                                <div>
                                    <label for="civilite" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Civilité') }}</label>
                                    <select name="civilite" id="civilite" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                                        <option value="">{{ __('--') }}</option>
                                        <option value="Monsieur" {{ old('civilite', $profile->civilite ?? '') === 'Monsieur' ? 'selected' : '' }}>Monsieur</option>
                                        <option value="Madame" {{ old('civilite', $profile->civilite ?? '') === 'Madame' ? 'selected' : '' }}>Madame</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="date_naissance" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Date de naissance') }}</label>
                                    <input type="date" name="date_naissance" id="date_naissance" value="{{ old('date_naissance', $profile->date_naissance ? $profile->date_naissance->format('Y-m-d') : '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                                </div>
                                <div>
                                    <label for="profession" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Profession') }}</label>
                                    <input type="text" name="profession" id="profession" value="{{ old('profession', $profile->profession ?? '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                                </div>
                                <div>
                                    <label for="societe" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Société') }}</label>
                                    <input type="text" name="societe" id="societe" value="{{ old('societe', $profile->societe ?? '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                                </div>
                                <div>
                                    <label for="rt_ds_dt" class="block text-sm font-medium text-gray-700 mb-1">{{ __('RT/DS/DT') }}</label>
                                    <input type="text" name="rt_ds_dt" id="rt_ds_dt" value="{{ old('rt_ds_dt', $profile->rt_ds_dt ?? '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                                </div>
                                <div>
                                    <label for="ordre_suppleants" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Ordre suppléants') }}</label>
                                    <input type="number" name="ordre_suppleants" id="ordre_suppleants" value="{{ old('ordre_suppleants', $profile->ordre_suppleants ?? '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                                </div>
                            </div>

                            {{-- Territoire --}}
                            <div class="space-y-4">
                                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ __('Territoire') }}</h4>
                                <div>
                                    <label for="code_insee" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Code INSEE') }}</label>
                                    <input type="text" name="code_insee" id="code_insee" value="{{ old('code_insee', $profile->code_insee ?? '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                                </div>
                                <div>
                                    <label for="epci_commune" class="block text-sm font-medium text-gray-700 mb-1">{{ __('EPCI/Commune') }}</label>
                                    <input type="text" name="epci_commune" id="epci_commune" value="{{ old('epci_commune', $profile->epci_commune ?? '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                                </div>
                                <div>
                                    <label for="secteur" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Secteur') }}</label>
                                    <input type="text" name="secteur" id="secteur" value="{{ old('secteur', $profile->secteur ?? '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                                </div>
                                <div>
                                    <label for="nom_secteur" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Nom secteur') }}</label>
                                    <input type="text" name="nom_secteur" id="nom_secteur" value="{{ old('nom_secteur', $profile->nom_secteur ?? '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                                </div>
                                <div>
                                    <label for="date_deliberation" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Date délibération') }}</label>
                                    <input type="date" name="date_deliberation" id="date_deliberation" value="{{ old('date_deliberation', $profile->date_deliberation ? $profile->date_deliberation->format('Y-m-d') : '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                                </div>
                                <div>
                                    <label for="visa_prefecture" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Visa préfecture') }}</label>
                                    <input type="text" name="visa_prefecture" id="visa_prefecture" value="{{ old('visa_prefecture', $profile->visa_prefecture ?? '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                                </div>
                                <div>
                                    <label for="probleme_delib" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Problème délibération') }}</label>
                                    <textarea name="probleme_delib" id="probleme_delib" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">{{ old('probleme_delib', $profile->probleme_delib ?? '') }}</textarea>
                                </div>
                            </div>

                            {{-- Contact + Options --}}
                            <div class="space-y-4">
                                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ __('Contact') }}</h4>
                                <div>
                                    <label for="telephone" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Téléphone') }}</label>
                                    <input type="text" name="telephone" id="telephone" value="{{ old('telephone', $profile->telephone ?? '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                                </div>
                                <div>
                                    <label for="mail_personnel" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Mail personnel') }}</label>
                                    <input type="email" name="mail_personnel" id="mail_personnel" value="{{ old('mail_personnel', $profile->mail_personnel ?? '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                                </div>
                                <div>
                                    <label for="mail_2" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Mail 2') }}</label>
                                    <input type="email" name="mail_2" id="mail_2" value="{{ old('mail_2', $profile->mail_2 ?? '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                                </div>
                                <div>
                                    <label for="contact" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Contact') }}</label>
                                    <input type="text" name="contact" id="contact" value="{{ old('contact', $profile->contact ?? '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                                </div>

                                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide pt-2">{{ __('Adresse') }}</h4>
                                <div>
                                    <label for="adresse_1" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Adresse 1') }}</label>
                                    <input type="text" name="adresse_1" id="adresse_1" value="{{ old('adresse_1', $profile->adresse_1 ?? '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                                </div>
                                <div>
                                    <label for="adresse_2" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Adresse 2') }}</label>
                                    <input type="text" name="adresse_2" id="adresse_2" value="{{ old('adresse_2', $profile->adresse_2 ?? '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                                </div>
                                <div>
                                    <label for="code_postal" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Code postal') }}</label>
                                    <input type="text" name="code_postal" id="code_postal" value="{{ old('code_postal', $profile->code_postal ?? '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                                </div>

                                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide pt-2">{{ __('Options') }}</h4>
                                <div class="space-y-2">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" name="newsletter" id="newsletter" value="1" {{ old('newsletter', $profile->newsletter ?? false) ? 'checked' : '' }} class="h-4 w-4 text-[#faa21b] border-gray-300 rounded focus:ring-[#faa21b]">
                                        <span class="ml-2 text-sm text-gray-700">{{ __('Newsletter') }}</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" name="frais_route" id="frais_route" value="1" {{ old('frais_route', $profile->frais_route ?? false) ? 'checked' : '' }} class="h-4 w-4 text-[#faa21b] border-gray-300 rounded focus:ring-[#faa21b]">
                                        <span class="ml-2 text-sm text-gray-700">{{ __('Frais de route') }}</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" name="rib_fourni" id="rib_fourni" value="1" {{ old('rib_fourni', $profile->rib_fourni ?? false) ? 'checked' : '' }} class="h-4 w-4 text-[#faa21b] border-gray-300 rounded focus:ring-[#faa21b]">
                                        <span class="ml-2 text-sm text-gray-700">{{ __('RIB fourni') }}</span>
                                    </label>
                                </div>
                                <div>
                                    <label for="chevaux_fiscaux" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Chevaux fiscaux') }}</label>
                                    <input type="text" name="chevaux_fiscaux" id="chevaux_fiscaux" value="{{ old('chevaux_fiscaux', $profile->chevaux_fiscaux ?? '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Actions (sticky) --}}
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-xl flex flex-col sm:flex-row gap-3 sm:justify-between">
                        <a href="{{ route('elus.admin.users') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg font-medium text-sm hover:bg-gray-100 transition">
                            {{ __('Annuler') }}
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 bg-[#faa21b] text-white rounded-lg font-semibold text-sm shadow hover:bg-[#f39b14] transition">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Enregistrer') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
