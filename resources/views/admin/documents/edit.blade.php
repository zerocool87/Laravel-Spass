<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Modifier le document') }}"
            subtitle="{{ __('Mettre a jour les informations') }}"
            icon="✏️"
            :backRoute="route('admin.documents.index')"
            :backLabel="__('Retour aux documents')"
            activeSection="admin"
        />
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6">
                <form action="{{ route('admin.documents.update', $document) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                    @include('elus.documents.form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
