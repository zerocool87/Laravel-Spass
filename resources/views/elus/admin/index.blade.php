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

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-[#faa21b] rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-white/80 mb-2">{{ __('Total utilisateurs') }}</p>
                            <p class="text-4xl font-bold">{{ $stats['total_users'] }}</p>
                        </div>
                        <div class="p-4 rounded-full bg-white/20">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-[#faa21b] rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-white/80 mb-2">{{ __('Élus') }}</p>
                            <p class="text-4xl font-bold">{{ $stats['total_elus'] }}</p>
                        </div>
                        <div class="p-4 rounded-full bg-white/20">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-[#faa21b] rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-white/80 mb-2">{{ __('Administrateurs') }}</p>
                            <p class="text-4xl font-bold">{{ $stats['total_admins'] }}</p>
                        </div>
                        <div class="p-4 rounded-full bg-white/20">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Quick Actions --}}
                <div class="widget-container">
                    <x-widget-header title="⚡ {{ __('Actions rapides') }}" />
                    <div class="p-5 space-y-3">
                        <a href="{{ route('elus.admin.users') }}" class="flex items-center p-3 border-2 border-[#faa21b]/20 rounded-xl hover:bg-[#faa21b]/5 hover:border-[#faa21b]/40 transition group">
                            <div class="p-2 bg-[#faa21b]/10 rounded-lg group-hover:bg-[#faa21b]/20 transition">
                                <svg class="w-6 h-6 text-[#faa21b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="font-semibold text-gray-900 group-hover:text-[#faa21b] transition">{{ __('Gérer les utilisateurs') }}</p>
                                <p class="text-sm text-gray-500">{{ __('Ajouter/Retirer des élus, gérer les rôles') }}</p>
                            </div>
                            <svg class="w-5 h-5 text-[#faa21b]/40 ml-auto group-hover:text-[#faa21b] transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>

                        <a href="{{ route('admin.instances.index') }}" class="flex items-center p-3 border-2 border-[#faa21b]/20 rounded-xl hover:bg-[#faa21b]/5 hover:border-[#faa21b]/40 transition group">
                            <div class="p-2 bg-[#faa21b]/10 rounded-lg group-hover:bg-[#faa21b]/20 transition">
                                <svg class="w-6 h-6 text-[#faa21b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="font-semibold text-gray-900 group-hover:text-[#faa21b] transition">{{ __('Gestion des instances') }}</p>
                                <p class="text-sm text-gray-500">{{ __('Gérer les comités, bureaux et commissions') }}</p>
                            </div>
                            <svg class="w-5 h-5 text-[#faa21b]/40 ml-auto group-hover:text-[#faa21b] transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>

                        <a href="{{ route('admin.documents.index') }}" class="flex items-center p-3 border-2 border-[#faa21b]/20 rounded-xl hover:bg-[#faa21b]/5 hover:border-[#faa21b]/40 transition group">
                            <div class="p-2 bg-[#faa21b]/10 rounded-lg group-hover:bg-[#faa21b]/20 transition">
                                <svg class="w-6 h-6 text-[#faa21b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="font-semibold text-gray-900 group-hover:text-[#faa21b] transition">{{ __('Gestion des documents') }}</p>
                                <p class="text-sm text-gray-500">{{ __('Créer, modifier et supprimer les documents') }}</p>
                            </div>
                            <svg class="w-5 h-5 text-[#faa21b]/40 ml-auto group-hover:text-[#faa21b] transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Recent Documents --}}
                <div class="widget-container">
                    <x-widget-header
                        title="📄 {{ __('Documents récents') }}"
                        :link="route('admin.documents.index')"
                        linkText="{{ __('Voir tous') }}"
                    />
                    <div class="divide-y divide-[#faa21b]/10">
                        @forelse($recentDocuments as $document)
                            <div class="px-6 py-3 flex items-center justify-between hover:bg-[#faa21b]/5 transition">
                                <div class="flex items-center">
                                    <div class="mr-3">
                                        <x-category-icon category="{{ $document->category }}" class="w-5 h-5" />
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ Str::limit($document->title, 30) }}</p>
                                        <p class="text-sm text-gray-500">{{ $document->original_name }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-500">{{ $document->created_at->format('d/m/Y') }}</p>
                                    @if($document->created_at->diffInDays() < 7)
                                        <span class="inline-flex items-center px-2 py-1 mt-1 rounded-full text-xs font-bold bg-green-100 text-green-700">Nouveau</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-8 text-center text-gray-500">
                                {{ __('Aucun document') }}
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
