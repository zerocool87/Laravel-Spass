<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Importer des élus (CSV)') }}"
            subtitle="{{ __('Import en masse depuis un fichier CSV tabulé') }}"
            icon="📥"
            :backRoute="route('elus.admin.users')"
            :backLabel="__('Retour à la gestion des utilisateurs')"
            activeSection="admin"
            :showNav="false"
        />
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => __('Accueil'), 'url' => route('elus.dashboard')], ['label' => __('Administration'), 'url' => route('elus.admin.index')], ['label' => __('Utilisateurs'), 'url' => route('elus.admin.users')], ['label' => __('Importer')]]" />
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-400 text-green-800 px-6 py-4 rounded-xl flex items-center shadow-lg">
                    <svg class="w-6 h-6 mr-3 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-gradient-to-r from-red-50 to-rose-50 border-2 border-red-400 text-red-800 px-6 py-4 rounded-xl flex items-center shadow-lg">
                    <svg class="w-6 h-6 mr-3 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-semibold">{{ session('error') }}</span>
                </div>
            @endif

            @if(session('skipped'))
                <div class="bg-white rounded-xl shadow-lg border border-amber-200 overflow-hidden">
                    <div class="bg-amber-50 px-6 py-3 border-b border-amber-200">
                        <h3 class="font-semibold text-amber-800">{{ __('Lignes ignorées') }} ({{ count(session('skipped')) }})</h3>
                    </div>
                    <div class="px-6 py-4 max-h-64 overflow-y-auto">
                        <ul class="list-disc list-inside space-y-1 text-sm text-gray-600">
                            @foreach(session('skipped') as $msg)
                                <li>{{ $msg }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-lg border border-[#faa21b]/20">
                <div class="px-6 pt-6 pb-4 border-b border-[#faa21b]/10">
                    <h2 class="text-lg font-semibold text-gray-900">{{ __('Téléverser un fichier CSV') }}</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ __('Le fichier doit être au format CSV avec tabulation comme séparateur et guillemets comme délimiteur de texte.') }}
                    </p>
                </div>

                <form method="POST" action="{{ route('elus.admin.users.import') }}" enctype="multipart/form-data" class="px-6 py-6">
                    @csrf

                    <div class="space-y-4">
                        <div>
                            <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Fichier CSV') }} *
                            </label>
                            <div x-data="{ fileName: '' }" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-[#faa21b] transition cursor-pointer"
                                 @click="$refs.fileInput.click()">
                                <div class="space-y-2 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    <div class="text-sm text-gray-600">
                                        <span class="font-medium text-[#faa21b] hover:text-[#f39b14]">{{ __('Cliquez pour sélectionner') }}</span>
                                        {{ __('ou glissez-déposez') }}
                                    </div>
                                    <p class="text-xs text-gray-500">{{ __('CSV ou TXT uniquement') }}</p>
                                    <p class="text-sm font-medium text-green-600" x-show="fileName" x-text="fileName"></p>
                                </div>
                                <input x-ref="fileInput" id="csv_file" name="csv_file" type="file" accept=".csv,.txt" required
                                       class="sr-only"
                                       @change="fileName = $event.target.files[0]?.name ?? ''">
                            </div>
                            @error('csv_file')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 bg-[#faa21b]/5 border border-[#faa21b]/20 rounded-xl p-4">
                        <h4 class="font-semibold text-[#b36b00] mb-2">{{ __('Colonnes attendues (dans l\'ordre)') }}</h4>
                        <div class="text-sm text-gray-600 grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1">
                            <div>1. CODE INSEE</div>
                            <div>2. COLLECTIVITE</div>
                            <div>3. EPCI/COMMUNE</div>
                            <div>4. SECTEUR</div>
                            <div>5. Nom secteur/CULM/CD</div>
                            <div>6. DATE DELIBERATION</div>
                            <div>7. visa Préfecture</div>
                            <div>8. Problème DELIB</div>
                            <div><strong>9. NOM</strong></div>
                            <div><strong>10. Prénom</strong></div>
                            <div>11. (colonne vide)</div>
                            <div>12. Monsieur/Madame</div>
                            <div>13. RT/DS/DT</div>
                            <div><strong>14. Titre</strong></div>
                            <div>15. ordre suppléants</div>
                            <div>16. Contact</div>
                            <div><strong>17. mail personnel</strong></div>
                            <div>18. Mail 2</div>
                            <div>19. Contact téléphonique</div>
                            <div>20. Adresse1</div>
                            <div>21. Adresse2</div>
                            <div>22. Code postal</div>
                            <div><strong>23. Commune</strong></div>
                            <div>24. Profession</div>
                            <div>25. société ou entité</div>
                            <div>26. Date de naissance</div>
                            <div>27. (colonne vide)</div>
                            <div>28. Newsletter</div>
                            <div>29. (colonne vide)</div>
                            <div>30. Frais de route</div>
                            <div>31. RIB fourni</div>
                            <div>32. Chevaux fiscaux</div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <a href="{{ route('elus.admin.users') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                            {{ __('Annuler') }}
                        </a>
                        <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-[#faa21b] text-white rounded-lg font-semibold text-sm shadow hover:bg-[#f39b14] transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                            {{ __('Importer') }}
                        </button>
                    </div>

                    <p class="mt-4 text-sm text-gray-500">
                        {{ __('Un mot de passe temporaire sera généré pour chaque élu créé. Les lignes sans email seront ignorées. Les emails déjà existants seront ignorés.') }}
                    </p>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
