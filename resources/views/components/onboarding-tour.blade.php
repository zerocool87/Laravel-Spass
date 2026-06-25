@props(['show' => false])

<div
    x-data="onboardingTour()"
    x-init="init()"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-[200]"
    role="dialog"
    aria-modal="true"
    aria-label="{{ __('Guide de démarrage') }}"
>
    {{-- Backdrop --}}
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/40 backdrop-blur-sm"
        @click="skip()"
    ></div>

    {{-- Tour card --}}
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none"
    >
        <div class="bg-white rounded-2xl shadow-2xl border-2 border-[#faa21b]/30 max-w-lg w-full pointer-events-auto overflow-hidden">
            {{-- Progress bar --}}
            <div class="h-2 bg-gray-100">
                <div
                    class="h-full bg-gradient-to-r from-[#faa21b] to-[#f59e0b] transition-all duration-500 ease-out"
                    :style="{ width: ((currentStep + 1) / steps.length * 100) + '%' }"
                ></div>
            </div>

            {{-- Content --}}
            <div class="p-6 sm:p-8">
                <template x-for="(step, index) in steps" :key="index">
                    <div x-show="currentStep === index">
                        {{-- Icon --}}
                        <div class="w-16 h-16 mx-auto rounded-2xl flex items-center justify-center text-3xl mb-4"
                            :style="{ backgroundColor: step.color + '20' }"
                        >
                            <span x-text="step.icon"></span>
                        </div>

                        {{-- Title --}}
                        <h3 class="text-xl sm:text-2xl font-bold text-center text-gray-900 mb-2" x-text="step.title"></h3>

                        {{-- Description --}}
                        <p class="text-base sm:text-lg text-center text-gray-600 mb-6 leading-relaxed" x-text="step.description"></p>
                    </div>
                </template>

                {{-- Navigation --}}
                <div class="flex items-center justify-between pt-2">
                    <div class="flex gap-1.5">
                        <template x-for="(step, index) in steps" :key="'dot-' + index">
                            <button
                                class="w-2.5 h-2.5 rounded-full transition-all duration-300"
                                :class="index === currentStep ? 'bg-[#faa21b] w-6' : 'bg-gray-300 hover:bg-gray-400'"
                                @click="goTo(index)"
                                :aria-label="'{{ __('Étape') }} ' + (index + 1)"
                            ></button>
                        </template>
                    </div>

                    <div class="flex gap-3">
                        <button
                            x-show="currentStep > 0"
                            @click="prev()"
                            class="px-4 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-100 transition"
                        >
                            {{ __('Précédent') }}
                        </button>

                        <button
                            x-show="currentStep < steps.length - 1"
                            @click="next()"
                            class="px-5 py-2.5 text-sm font-bold text-white rounded-xl transition shadow-md"
                            :style="{ backgroundColor: steps[currentStep].color }"
                            @mouseover="$el.style.backgroundColor = steps[currentStep].color + 'cc'"
                            @mouseout="$el.style.backgroundColor = steps[currentStep].color"
                        >
                            {{ __('Suivant') }}
                        </button>

                        <button
                            x-show="currentStep === steps.length - 1"
                            @click="finish()"
                            class="px-5 py-2.5 text-sm font-bold text-white bg-[#faa21b] hover:bg-[#e89315] rounded-xl transition shadow-md"
                        >
                            {{ __('C\'est parti !') }} 🚀
                        </button>
                    </div>
                </div>

                {{-- Skip link --}}
                <div class="text-center mt-4">
                    <button @click="skip()" class="text-sm text-gray-400 hover:text-gray-600 transition underline">
                        {{ __('Passer le guide') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function onboardingTour() {
        return {
            show: false,
            currentStep: 0,
            steps: [
                {
                    icon: '👋',
                    title: '{{ __('Bienvenue sur votre espace Élus') }}',
                    description: '{{ __('Ce tableau de bord est votre centre de pilotage. Vous y trouverez toutes les informations essentielles pour suivre la vie de votre commune.') }}',
                    color: '#faa21b',
                },
                {
                    icon: '📊',
                    title: '{{ __('Les indicateurs clés') }}',
                    description: '{{ __('Les cartes en haut vous donnent un aperçu rapide : instances, projets en cours, réunions à venir et documents disponibles.') }}',
                    color: '#10b981',
                },
                {
                    icon: '📅',
                    title: '{{ __('Vos réunions et actualités') }}',
                    description: '{{ __('La section centrale affiche vos prochaines réunions, les dernières actualités, les instances et les documents récents. Tout est à portée de clic.') }}',
                    color: '#3b82f6',
                },
                {
                    icon: '🏛️',
                    title: '{{ __('Explorez les rubriques') }}',
                    description: '{{ __('Utilisez le menu en haut pour naviguer entre Instances, Projets, Réunions, Documents, Actualités et le Forum. Chaque rubrique est conçue pour être simple et intuitive.') }}',
                    color: '#8b5cf6',
                },

            ],

            init() {
                if ({{ $show ? 'true' : 'false' }}) {
                    this.show = true;
                }
            },

            next() {
                if (this.currentStep < this.steps.length - 1) {
                    this.currentStep++;
                }
            },

            prev() {
                if (this.currentStep > 0) {
                    this.currentStep--;
                }
            },

            goTo(index) {
                this.currentStep = index;
            },

            finish() {
                this.show = false;
                fetch('{{ route('elus.onboarding.complete') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
            },

            skip() {
                if (confirm('{{ __('Voulez-vous vraiment passer le guide ? Vous pourrez le retrouver dans vos paramètres.') }}')) {
                    this.finish();
                }
            },
        };
    }
</script>
