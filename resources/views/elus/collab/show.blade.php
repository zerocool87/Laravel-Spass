<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Conversation') }}"
            subtitle="{{ $otherUser?->name ?? __('Messagerie') }}"
            icon="MSG"
            :backRoute="route('elus.collab.index')"
            :backLabel="__('Retour aux conversations')"
            activeSection="collab"
        />
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                    <div class="widget-container">
                        <x-widget-header title="{{ $otherUser?->name ?? __('Conversation') }}" />
                        <div class="p-6 flex flex-col gap-4 max-h-[560px] overflow-y-auto">
                            @forelse($messages as $message)
                                <div class="flex {{ $message->user_id === $currentUser->id ? 'justify-end' : 'justify-start' }}">
                                    <div class="max-w-xl rounded-lg px-4 py-3 flex flex-col gap-2 {{ $message->user_id === $currentUser->id ? 'bg-[#faa21b] text-white' : 'bg-[#faa21b]/10 text-gray-800' }}">
                                        <p class="text-sm leading-relaxed">{{ $message->body }}</p>
                                        <div class="text-xs {{ $message->user_id === $currentUser->id ? 'text-white/80 text-right' : 'text-gray-500' }}">
                                            {{ $message->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="widget-empty">
                                    <svg class="widget-empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h6m2 8H7a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v8l-4 4z"></path>
                                    </svg>
                                    <h3 class="widget-empty-title">{{ __('Aucun message') }}</h3>
                                    <p class="widget-empty-description">{{ __('Envoyez le premier message pour demarrer la discussion.') }}</p>
                                </div>
                            @endforelse
                        </div>
                        <div class="px-6 pb-4">
                            {{ $messages->links() }}
                        </div>
                        <div class="border-t border-[#faa21b]/20 p-4">
                            <form method="POST" action="{{ route('elus.collab.messages.store', $conversation) }}" class="flex flex-col gap-3">
                                @csrf
                                <textarea name="body" rows="3" class="w-full input-orange" placeholder="{{ __('Ecrire un message...') }}" required>{{ old('body') }}</textarea>
                                <x-input-error :messages="$errors->get('body')" />
                                <div class="flex items-center justify-between gap-4">
                                    <p class="text-xs text-gray-500">
                                        {{ __('Messages visibles uniquement par les elus participants.') }}
                                    </p>
                                    <button type="submit" class="btn-primary-orange">
                                        {{ __('Envoyer') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="widget-container">
                        <x-widget-header title="{{ __('Participants') }}" />
                        <div class="p-6 flex flex-col gap-3">
                            @foreach($conversation->users as $participant)
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $participant->name }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $participant->fonction ?? __('Elu') }}
                                            @if($participant->commune)
                                                - {{ $participant->commune }}
                                            @endif
                                        </p>
                                    </div>
                                    @if($participant->id === $currentUser->id)
                                        <span class="text-xs font-semibold text-[#faa21b]">{{ __('Vous') }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
