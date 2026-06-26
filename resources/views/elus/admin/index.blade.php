<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Administration') }}"
            subtitle="{{ __('Gestion des utilisateurs et paramètres') }}"
            icon="⚙️"
            :backRoute="route('elus.dashboard')"
            :backLabel="__('Retour au tableau de bord')"
            activeSection="admin"
        />
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => __('Accueil'), 'url' => route('elus.dashboard')], ['label' => __('Administration')]]" />
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                <a href="{{ route('elus.admin.users') }}" class="bg-white rounded-xl shadow border-2 border-[#faa21b]/20 hover:border-[#faa21b]/60 hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200 group">
                    <div class="p-6 flex flex-col items-center text-center gap-3">
                        <div class="p-3 bg-[#faa21b]/10 rounded-xl group-hover:bg-[#faa21b]/20 transition-colors">
                            <svg class="w-7 h-7 text-[#faa21b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 group-hover:text-[#faa21b] transition-colors">{{ __('Gérer les utilisateurs') }}</p>
                            <p class="text-sm text-gray-500 mt-1">{{ __('Ajouter/Retirer des élus, gérer les rôles') }}</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('elus.admin.users.import.form') }}" class="bg-white rounded-xl shadow border-2 border-green-200 hover:border-green-500 hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200 group">
                    <div class="p-6 flex flex-col items-center text-center gap-3">
                        <div class="p-3 bg-green-100 rounded-xl group-hover:bg-green-200 transition-colors">
                            <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 group-hover:text-green-700 transition-colors">{{ __('Importer des élus (CSV)') }}</p>
                            <p class="text-sm text-gray-500 mt-1">{{ __('Import en masse depuis un fichier CSV tabulé') }}</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.documents.index') }}" class="bg-white rounded-xl shadow border-2 border-[#faa21b]/20 hover:border-[#faa21b]/60 hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200 group">
                    <div class="p-6 flex flex-col items-center text-center gap-3">
                        <div class="p-3 bg-[#faa21b]/10 rounded-xl group-hover:bg-[#faa21b]/20 transition-colors">
                            <svg class="w-7 h-7 text-[#faa21b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 group-hover:text-[#faa21b] transition-colors">{{ __('Gestion des documents') }}</p>
                            <p class="text-sm text-gray-500 mt-1">{{ __('Créer, modifier et supprimer les documents') }}</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.reunions.index') }}" class="bg-white rounded-xl shadow border-2 border-[#faa21b]/20 hover:border-[#faa21b]/60 hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200 group">
                    <div class="p-6 flex flex-col items-center text-center gap-3">
                        <div class="p-3 bg-[#faa21b]/10 rounded-xl group-hover:bg-[#faa21b]/20 transition-colors">
                            <svg class="w-7 h-7 text-[#faa21b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 group-hover:text-[#faa21b] transition-colors">{{ __('Gestion des réunions') }}</p>
                            <p class="text-sm text-gray-500 mt-1">{{ __('Créer, modifier et supprimer les réunions') }}</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.projects.index') }}" class="bg-white rounded-xl shadow border-2 border-[#faa21b]/20 hover:border-[#faa21b]/60 hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200 group">
                    <div class="p-6 flex flex-col items-center text-center gap-3">
                        <div class="p-3 bg-[#faa21b]/10 rounded-xl group-hover:bg-[#faa21b]/20 transition-colors">
                            <svg class="w-7 h-7 text-[#faa21b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 group-hover:text-[#faa21b] transition-colors">{{ __('Gestion des projets') }}</p>
                            <p class="text-sm text-gray-500 mt-1">{{ __('Créer, modifier et suivre les projets territoriaux') }}</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.actualites.index') }}" class="bg-white rounded-xl shadow border-2 border-[#faa21b]/20 hover:border-[#faa21b]/60 hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200 group">
                    <div class="p-6 flex flex-col items-center text-center gap-3">
                        <div class="p-3 bg-[#faa21b]/10 rounded-xl group-hover:bg-[#faa21b]/20 transition-colors">
                            <svg class="w-7 h-7 text-[#faa21b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l6 6v10a2 2 0 01-2 2zM14 4v4h4M8 13h8M8 17h4"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 group-hover:text-[#faa21b] transition-colors">{{ __('Gestion des actualités') }}</p>
                            <p class="text-sm text-gray-500 mt-1">{{ __('Publier et gérer les actualités des élus') }}</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.thematiques.index') }}" class="bg-white rounded-xl shadow border-2 border-[#faa21b]/20 hover:border-[#faa21b]/60 hover:-translate-y-0.5 hover:shadow-lg transition-all duration-200 group">
                    <div class="p-6 flex flex-col items-center text-center gap-3">
                        <div class="p-3 bg-[#faa21b]/10 rounded-xl group-hover:bg-[#faa21b]/20 transition-colors">
                            <svg class="w-7 h-7 text-[#faa21b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 group-hover:text-[#faa21b] transition-colors">{{ __('Gestion des thématiques') }}</p>
                            <p class="text-sm text-gray-500 mt-1">{{ __('Gérer les thématiques du forum') }}</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
