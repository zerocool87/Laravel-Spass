{{-- Simple visual story for admin create modal --}}
<x-layout>
    <div class="py-12">
        <div class="max-w-3xl mx-auto">
            @include('events._admin_create_modal')
            <div class="mt-6">
                <button class="btn-primary" onclick="openEventCreateModal();">Open modal (story)</button>
            </div>
        </div>
    </div>
</x-layout>
