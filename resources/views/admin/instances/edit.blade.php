<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Modifier l\'instance') }}"
            subtitle="{{ $instance->name }}"
            icon="🏛️"
            :backRoute="route('admin.instances.index')"
            :backLabel="__('Retour aux instances')"
            activeSection="admin"
        />
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-lg border border-[#faa21b]/20 p-8">
                <form method="POST" action="{{ route('admin.instances.update', $instance) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Nom -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-900 mb-2">
                            {{ __('Nom de l\'instance') }} <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="{{ old('name', $instance->name) }}"
                            required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('name') border-red-300 @enderror"
                            placeholder="{{ __('Ex: Conseil Municipal') }}"
                        />
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type -->
                    <div>
                        <label for="type" class="block text-sm font-semibold text-gray-900 mb-2">
                            {{ __('Type d\'instance') }} <span class="text-red-500">*</span>
                        </label>
                        <select
                            name="type"
                            id="type"
                            required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('type') border-red-300 @enderror"
                        >
                            <option value="">{{ __('Sélectionnez un type') }}</option>
                            @foreach($types as $key => $label)
                                <option value="{{ $key }}" {{ old('type', $instance->type) === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Territoire -->
                    <div>
                        <label for="territory" class="block text-sm font-semibold text-gray-900 mb-2">
                            {{ __('Territoire') }}
                        </label>
                        <input
                            type="text"
                            name="territory"
                            id="territory"
                            value="{{ old('territory', $instance->territory) }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('territory') border-red-300 @enderror"
                            placeholder="{{ __('Ex: Commune de Paris') }}"
                        />
                        @error('territory')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-900 mb-2">
                            {{ __('Description') }}
                        </label>
                        <textarea
                            name="description"
                            id="description"
                            rows="4"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('description') border-red-300 @enderror"
                            placeholder="{{ __('Décrivez le rôle et les objectifs de cette instance...') }}"
                        >{{ old('description', $instance->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Membres -->
                    <div x-data="membersManager()">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">
                            {{ __('Membres') }}
                        </label>
                        <p class="text-sm text-gray-600 mb-3">
                            {{ __('Ajoutez les membres de cette instance') }}
                        </p>

                        <div class="space-y-2 mb-3">
                            <template x-for="(member, index) in members" :key="index">
                                <div class="flex gap-2">
                                    <input
                                        type="text"
                                        :name="'members[' + index + ']'"
                                        x-model="members[index]"
                                        class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]"
                                        :placeholder="__('Nom du membre')"
                                    />
                                    <button
                                        type="button"
                                        @click="removeMember(index)"
                                        class="px-3 py-2 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <button
                            type="button"
                            @click="addMember()"
                            class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium text-sm hover:bg-gray-200 transition"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            {{ __('Ajouter un membre') }}
                        </button>

                        @error('members')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Informations supplémentaires -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-blue-900 mb-1">{{ __('Informations') }}</h4>
                                <p class="text-sm text-blue-800">
                                    {{ __('Cette instance contient') }} <strong>{{ $instance->reunions()->count() }}</strong> {{ __('réunion(s)') }}.
                                </p>
                                <p class="text-sm text-blue-800 mt-1">
                                    {{ __('Créée le') }} {{ $instance->created_at->format('d/m/Y à H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <div>
                            @if($instance->reunions()->count() === 0)
                                <form
                                    method="POST"
                                    action="{{ route('admin.instances.destroy', $instance) }}"
                                    onsubmit="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer cette instance ?') }}')"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="px-6 py-2.5 bg-red-50 text-red-700 rounded-xl font-semibold text-sm hover:bg-red-100 transition"
                                    >
                                        {{ __('Supprimer l\'instance') }}
                                    </button>
                                </form>
                            @else
                                <button
                                    type="button"
                                    disabled
                                    class="px-6 py-2.5 bg-gray-100 text-gray-400 rounded-xl font-semibold text-sm cursor-not-allowed"
                                    title="{{ __('Impossible de supprimer une instance avec des réunions') }}"
                                >
                                    {{ __('Supprimer l\'instance') }}
                                </button>
                            @endif
                        </div>

                        <div class="flex items-center gap-4">
                            <a
                                href="{{ route('admin.instances.index') }}"
                                class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-200 transition"
                            >
                                {{ __('Annuler') }}
                            </a>
                            <button
                                type="submit"
                                class="px-6 py-2.5 bg-[#faa21b] text-white rounded-xl font-semibold text-sm shadow-lg hover:shadow-xl hover:-translate-y-0.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#faa21b] transition"
                            >
                                {{ __('Mettre à jour') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function membersManager() {
            return {
                members: @json(old('members', $instance->members ?? [])),

                addMember() {
                    this.members.push('');
                },

                removeMember(index) {
                    this.members.splice(index, 1);
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
