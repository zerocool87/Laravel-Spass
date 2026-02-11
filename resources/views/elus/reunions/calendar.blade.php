<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Calendrier') }}"
            subtitle="{{ __('Vue calendrier des réunions') }}"
            icon="📅"
            :backRoute="route('elus.dashboard')"
            :backLabel="__('Retour au tableau de bord')"
            activeSection="reunions"
        >

        </x-elus-header>
    </x-slot>

    <div class="py-8">
         <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
             @can('admin')
             <div class="mb-4 flex justify-end">
                 <a href="{{ route('elus.reunions.create') }}" class="inline-flex items-center px-6 py-3 bg-white text-[#faa21b] border border-transparent rounded-xl font-bold text-sm hover:bg-white/90 transition shadow-lg hover:shadow-xl transform hover:scale-105">
                     <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                     </svg>
                     {{ __('Nouvelle réunion') }}
                 </a>
             </div>
             @endcan
              <div class="bg-white rounded-lg shadow-lg border-2 border-[#faa21b]/20 p-6">
                   <div id="reunions-calendar" class="hidden" data-feed-url="{{ route('elus.reunions.json') }}"></div>
              </div>

            {{-- Legend --}}
            <div class="mt-6 bg-white rounded-xl shadow-md border border-[#ffe6b8] p-5">
                <h4 class="text-sm font-bold text-[#b36b00] mb-3 uppercase tracking-wide">{{ __('Légende') }}</h4>
                <div class="flex flex-wrap gap-5">
                    <div class="flex items-center">
                        <span class="w-3.5 h-3.5 rounded-full bg-[#faa21b] mr-2 ring-2 ring-[#faa21b]/20"></span>
                        <span class="text-sm font-medium text-gray-700">{{ __('Planifiée') }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-3.5 h-3.5 rounded-full bg-green-500 mr-2 ring-2 ring-green-500/20"></span>
                        <span class="text-sm font-medium text-gray-700">{{ __('Confirmée') }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-3.5 h-3.5 rounded-full bg-gray-500 mr-2 ring-2 ring-gray-500/20"></span>
                        <span class="text-sm font-medium text-gray-700">{{ __('Terminée') }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-3.5 h-3.5 rounded-full bg-red-500 mr-2 ring-2 ring-red-500/20"></span>
                        <span class="text-sm font-medium text-gray-700">{{ __('Annulée') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
