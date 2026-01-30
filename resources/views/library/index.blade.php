<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900">{{ __('Bibliothèque') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="glass p-4">
                <div class="text-center py-8 text-gray-500">
                    <p>{{ __('La liste des documents a été supprimée.') }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
