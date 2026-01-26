<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gradient-to-bl from-gray-900 to-slate-900 glass overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-100">
                    {{ __("You're logged in!") }}
                </div>
            </div>

            @php
                // Documents visible to the user: either visible_to_all or assigned to user
                $documents = \App\Models\Document::where('visible_to_all', true)
                    ->orWhereHas('users', function($q){ $q->where('users.id', auth()->id()); })
                    ->latest()
                    ->limit(10)->get();
            @endphp

            @include('dashboard.documents', ['documents' => $documents])
        </div>
    </div>
</x-app-layout>
