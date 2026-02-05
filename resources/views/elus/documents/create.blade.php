<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Téléverser un document') }}"
            subtitle="{{ __('Ajouter un nouveau document à la bibliothèque') }}"
            icon="📄"
            :backRoute="route('elus.documents.index')"
            :backLabel="__('Retour aux documents')"
            activeSection="documents"
        />
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
