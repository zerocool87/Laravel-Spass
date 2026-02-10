<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Collaboratif') }}"
            subtitle="{{ __('Messagerie securisee entre elus') }}"
            icon="MSG"
            :backRoute="route('elus.dashboard')"
            :backLabel="__('Retour au tableau de bord')"
            activeSection="collab"
        />
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                    <div class="widget-container">
                        <x-widget-header title="{{ __('Conversations') }}" />
                        <div class="divide-y divide-[#faa21b]/20">
                            @forelse($conversations as $conversation)
                                @php($other = $conversation->otherParticipant($currentUser))
                                <a href="{{ route('elus.collab.show', $conversation) }}" class="block px-6 py-4 hover:bg-[#faa21b]/5 transition">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex-1 flex flex-col gap-1">
                                            <div class="flex items-center gap-2">
                                                <p class="font-semibold text-gray-900">
                                                    {{ $other?->name ?? __('Participant') }}
                                                </p>
                                                @if($conversation->unread_count > 0)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-[#faa21b]/20 text-[#faa21b]">
                                                        {{ $conversation->unread_count }}
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-gray-500">
                                                {{ $conversation->latestMessage?->body ?? __('Aucun message pour le moment.') }}
                                            </p>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $conversation->latestMessage?->created_at?->diffForHumans() ?? __('Nouveau') }}
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="widget-empty">
                                    <svg class="widget-empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h6m2 8H7a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v8l-4 4z"></path>
                                    </svg>
                                    <h3 class="widget-empty-title">{{ __('Aucune conversation') }}</h3>
                                    <p class="widget-empty-description">{{ __('Demarrez un echange avec un autre elu.') }}</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div>
                    <div class="widget-container">
                        <x-widget-header title="{{ __('Nouvelle conversation') }}" />
                        <form method="POST" action="{{ route('elus.collab.store') }}" class="p-6 flex flex-col gap-4">
                            @csrf
                            <div class="flex flex-col gap-2">
                                <label for="recipient_id" class="text-sm font-semibold text-gray-700">
                                    {{ __('Destinataire') }}
                                </label>
                                <select id="recipient_id" name="recipient_id" class="w-full select-orange" required>
                                    <option value="">{{ __('Choisir un elu') }}</option>
                                    @foreach($recipients as $recipient)
                                        <option value="{{ $recipient->id }}" {{ old('recipient_id') == $recipient->id ? 'selected' : '' }}>
                                            {{ $recipient->name }}
                                            @if($recipient->commune)
                                                - {{ $recipient->commune }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('recipient_id')" />
                            </div>
                            <button type="submit" class="btn-primary-orange">
                                {{ __('Creer la conversation') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
