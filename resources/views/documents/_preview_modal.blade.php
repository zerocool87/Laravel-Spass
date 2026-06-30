<div
    x-data="documentPreview()"
    x-cloak
    @open-document.window="open($event.detail)"
>
    <template x-if="showModal">
        <div
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="close()"></div>

            <div
                class="relative bg-white w-full max-w-5xl h-[90vh] rounded-xl shadow-lg border-2 border-[#faa21b]/20 overflow-hidden flex flex-col"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-8 scale-95"
            >
                {{-- Category banner — colored background with category name --}}
                <template x-if="category">
                    <div class="w-full px-4 py-2 shrink-0 flex items-center gap-2" :class="{
                        'bg-amber-600': category === 'Convocations',
                        'bg-amber-500': category === 'Ordres du jour',
                        'bg-emerald-600': category === 'Comptes rendus',
                        'bg-cyan-600': category === 'Rapports',
                        'bg-rose-600': category === 'Délibérations',
                        'bg-sky-600': category === 'Guides',
                        'bg-gray-400': !['Convocations','Ordres du jour','Comptes rendus','Rapports','Délibérations','Guides'].includes(category)
                    }">
                        <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-white text-xs font-bold uppercase tracking-wider" x-text="category"></span>
                    </div>
                </template>

                {{-- Header toolbar — pattern widget-header --}}
                <div class="px-3 py-2 flex justify-between items-center bg-[#faa21b]/15 border-b-2 border-[#faa21b]/20">
                    <h3 x-text="title" class="text-[#faa21b] font-bold truncate mr-2"></h3>
                    <div class="flex items-center gap-2 shrink-0">
                        <x-secondary-button @click="close()">
                            {{ __('Close') }}
                        </x-secondary-button>
                        <x-primary-button
                            x-bind:href="downloadUrl"
                            target="_blank"
                            rel="noopener"
                            x-show="showNotPreviewable"
                        >
                            {{ __('Download') }}
                        </x-primary-button>
                    </div>
                </div>

                {{-- Content area --}}
                <div class="relative flex-1 min-h-0 min-w-0 overflow-auto">
                    {{-- Loading spinner --}}
                    <div
                        x-show="loading"
                        class="absolute inset-0 flex items-center justify-center z-10 bg-black/40"
                    >
                        <div class="flex flex-col items-center gap-3">
                            <div class="border-4 border-t-[#faa21b] rounded-full w-12 h-12 animate-spin"></div>
                            <span x-show="_progress > 0" class="text-white text-sm" x-text="'{{ __('Loading') }}: ' + _progress + '%'"></span>
                        </div>
                    </div>

                    {{-- Error message --}}
                    <div
                        x-show="errorMessage"
                        class="absolute inset-0 flex items-center justify-center z-10 bg-black/40"
                    >
                        <div class="bg-red-50 border border-red-200 rounded-xl p-6 max-w-md text-center">
                            <p class="text-red-700 font-medium" x-text="errorMessage"></p>
                            <x-secondary-button @click="close()" class="mt-4">
                                {{ __('Close') }}
                            </x-secondary-button>
                        </div>
                    </div>

                    {{-- Not previewable --}}
                    <div x-show="showNotPreviewable && !errorMessage" class="p-6 text-center text-gray-600">
                        <p class="mb-2">{{ __('Preview not available for this file type.') }}</p>
                        <p class="text-sm text-gray-500">{{ __('You can download the file to view it on your device.') }}</p>

                        <div class="mt-4 text-left inline-block">
                            <p class="text-sm font-semibold text-[#b36b00]">{{ __('Previewable file types:') }}</p>
                            <ul class="list-disc list-inside text-sm text-gray-500 mt-2">
                                <template x-for="t in previewTypes" :key="t">
                                    <li x-text="t"></li>
                                </template>
                            </ul>
                        </div>
                    </div>

                    {{-- PDF via canvas --}}
                    <template x-if="isPdf() && !forceIframe">
                        <div class="h-full w-full min-w-0 flex flex-col">
                            <div class="px-3 py-2 bg-[#faa21b]/15 border-b-2 border-[#faa21b]/20 flex items-center gap-2 flex-wrap">
                                <x-secondary-button @click.prevent="prevPage()" x-bind:disabled="currentPage <= 1">
                                    {{ __('Prev') }}
                                </x-secondary-button>
                                <span class="px-2 text-[#b36b00] text-sm font-medium">
                                    {{ __('Page') }} <span x-text="currentPage"></span> / <span x-text="totalPages"></span>
                                </span>
                                <x-secondary-button @click.prevent="nextPage()" x-bind:disabled="currentPage >= totalPages">
                                    {{ __('Next') }}
                                </x-secondary-button>

                                <span class="border-l border-[#faa21b]/30 h-6 mx-1"></span>

                                <x-secondary-button @click.prevent="setPdfZoom('page-width')">
                                    {{ __('Fit width') }}
                                </x-secondary-button>
                                <x-secondary-button @click.prevent="setPdfZoom('page-fit')">
                                    {{ __('Fit page') }}
                                </x-secondary-button>
                                <x-secondary-button @click.prevent="setPdfZoom('100')">
                                    {{ __('100%') }}
                                </x-secondary-button>
                                <x-secondary-button @click.prevent="rotatePdf()">
                                    {{ __('Rotate') }}
                                </x-secondary-button>

                                <span class="border-l border-[#faa21b]/30 h-6 mx-1"></span>

                                <x-primary-button x-bind:href="downloadUrl" target="_blank" rel="noopener">
                                    {{ __('Download') }}
                                </x-primary-button>

                                <span class="text-sm text-[#b36b00] font-medium ml-auto whitespace-nowrap">
                                    {{ __('Zoom') }}: <span x-text="pdfZoom"></span>
                                </span>
                            </div>
                            <div
                                x-ref="pdfContainer"
                                class="flex-1 min-h-0 min-w-0 overflow-auto flex items-center justify-center bg-[#fffbe9]"
                            >
                                <canvas x-ref="pdfCanvas" class="block" aria-label="PDF canvas"></canvas>
                            </div>
                        </div>
                    </template>

                    {{-- PDF iframe fallback --}}
                    <template x-if="isPdf() && forceIframe">
                        <div class="h-full w-full min-w-0">
                            <iframe
                                x-bind:src="embed"
                                class="w-full h-full block min-w-0"
                                style="border:0;"
                                sandbox="allow-same-origin allow-scripts allow-popups allow-forms allow-modals"
                                allowfullscreen
                                @load="loading = false"
                            ></iframe>
                        </div>
                    </template>

                    {{-- Non-PDF (images, text) --}}
                    <template x-if="!isPdf()">
                        <iframe
                            x-show="!showNotPreviewable && !errorMessage"
                            x-bind:src="embed"
                            class="w-full h-full block min-w-0"
                            style="border:0;"
                            sandbox="allow-same-origin allow-scripts allow-popups allow-forms allow-modals"
                            allowfullscreen
                            @load="loading = false"
                        ></iframe>
                    </template>
                </div>
            </div>
        </div>
    </template>

    @once
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
        <script>
            if (window.pdfjsLib) {
                try {
                    (function () {
                        var localWorker = '{{ file_exists(public_path('js/pdf.worker.min.js')) ? asset('js/pdf.worker.min.js') : '' }}';
                        if (localWorker) {
                            window.pdfjsLib.GlobalWorkerOptions.workerSrc = localWorker;
                        } else {
                            window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
                        }
                    })();
                } catch (e) {
                    console.warn('pdfjs worker setup failed', e);
                }
            }
        </script>
    @endonce
</div>
