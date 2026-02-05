<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('elus.documents.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h2 class="font-semibold text-xl text-gray-900">{{ __('Téléverser un document') }}</h2>
                <p class="text-sm text-gray-600">{{ __('Ajouter un nouveau document à la bibliothèque') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6">
                <form action="{{ route('elus.documents.store') }}" method="POST" enctype="multipart/form-data">
                    @include('elus.documents.form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>