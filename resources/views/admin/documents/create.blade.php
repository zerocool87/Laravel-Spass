<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900">{{ __('Upload Document') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="glass p-4">
                <form action="{{ route('admin.documents.store') }}" method="POST" enctype="multipart/form-data">
                    @include('admin.documents.form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
