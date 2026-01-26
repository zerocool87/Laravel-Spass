<div x-data="documentPreview()" x-cloak @open-document.window="open($event.detail)">
    <template x-if="showModal">
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60" @click="close()"></div>
            <div class="relative bg-gray-900 w-full max-w-5xl h-[90vh] rounded overflow-hidden flex flex-col">
                <div class="p-2 flex justify-between items-center bg-gray-800">
                    <h3 x-text="title" class="text-white"></h3>
                    <div class="flex items-center gap-2">
                        <button @click="close()" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('Close') }}</button>
                        <a :href="downloadUrl" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150" target="_blank" rel="noopener" x-show="showNotPreviewable">{{ __('Download') }}</a>
                        <a :href="embed" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150" target="_blank" rel="noopener" x-show="!showNotPreviewable">{{ __('Open in new tab') }}</a>
                    </div>
                </div>

                <div class="relative flex-1 min-h-0 min-w-0 overflow-auto">
                    <div x-show="loading" class="absolute inset-0 flex items-center justify-center z-10 bg-black/40">
                        <div class="border-4 border-t-cyan-400 rounded-full w-12 h-12 animate-spin"></div>
                    </div>

                    <div x-show="showNotPreviewable" class="p-6 text-center text-cyan-200">
                        <p class="mb-2">{{ __('Preview not available for this file type.') }}</p>
                        <p class="text-sm text-cyan-300">{{ __('You can download the file to view it on your device.') }}</p>

                        <div class="mt-4 text-left">
                            <div class="text-sm font-semibold text-cyan-100">{{ __('Previewable file types:') }}</div>
                            <ul class="list-disc list-inside text-sm text-cyan-300 mt-2">
                                <template x-for="t in previewTypes" :key="t">
                                    <li x-text="t"></li>
                                </template>
                            </ul>
                        </div>
                    </div>

                    <div class="w-full h-full min-w-0">
                        <template x-if="isPdf() && !forceIframe">
                            <div class="h-full w-full min-w-0 flex flex-col">
                                <div class="p-2 bg-gray-800 flex items-center gap-2">
                                    <button @click.prevent="prevPage()" class="inline-flex items-center px-3 py-1 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150" :disabled="currentPage<=1">{{ __('Prev') }}</button>
                                    <div class="px-2 text-cyan-200">{{ __('Page') }} <span x-text="currentPage"></span> / <span x-text="totalPages"></span></div>
                                    <button @click.prevent="nextPage()" class="inline-flex items-center px-3 py-1 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150" :disabled="currentPage>=totalPages">{{ __('Next') }}</button>

                                    <div class="border-l border-gray-700 h-6 mx-2"></div>

                                    <button @click.prevent="setPdfZoom('page-width')" class="inline-flex items-center px-3 py-1 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('Fit width') }}</button>
                                    <button @click.prevent="setPdfZoom('page-fit')" class="inline-flex items-center px-3 py-1 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('Fit page') }}</button>
                                    <button @click.prevent="setPdfZoom('100')" class="inline-flex items-center px-3 py-1 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('100%') }}</button>
                                    <button @click.prevent="rotatePdf()" class="inline-flex items-center px-3 py-1 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">Rotate</button>
                                    <a :href="downloadUrl" class="inline-flex items-center px-3 py-1 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150" target="_blank" rel="noopener">{{ __('Download') }}</a>
                                    <a :href="embed" class="inline-flex items-center px-3 py-1 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150" target="_blank" rel="noopener">Open in new tab</a>
                                    <div class="ml-4 text-sm text-cyan-200">{{ __('Zoom') }}: <span x-text="pdfZoom"></span></div> 
                                    <div class="ml-4 text-sm text-cyan-200" x-show="_progress">Loading: <span x-text="_progress+'%'"></span></div>
                                </div>
                                <div class="flex-1 min-h-0 min-w-0 modal-pdf-canvas-container overflow-auto flex items-center justify-center">
                                    <canvas id="pdf-canvas" class="w-auto inline-block mx-auto block" style="display:block;" aria-label="PDF canvas"></canvas>
                                </div>
                            </div>
                        </template>

                        <template x-if="isPdf() && forceIframe">
                            <div class="h-full w-full min-w-0">
                                    <iframe x-show="!showNotPreviewable" :src="embed" class="w-full h-full block min-w-0" style="border:0;" sandbox="allow-same-origin allow-scripts allow-popups allow-forms allow-modals" allowfullscreen @load="loading=false" ref="pdfIframe"></iframe>
                            </div>
                        </template>

                        <template x-if="!isPdf()">
                            <iframe x-show="!showNotPreviewable" :src="embed" class="w-full h-full block min-w-0" style="border:0;" sandbox="allow-same-origin allow-scripts allow-popups allow-forms allow-modals" allowfullscreen @load="loading=false"></iframe>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </template>

    @once
    <!-- PDF.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <script>
        // Configure worker: prefer same-origin worker if present to avoid cross-origin worker fetches
        if (window.pdfjsLib) {
            try {
                (function(){
                    var localWorker = null;
                    // Server-side: if a local worker file was published to public/js/pdf.worker.min.js
                    // Blade will replace the following placeholder with the proper URL when available.
                    localWorker = '{{ file_exists(public_path('js/pdf.worker.min.js')) ? asset('js/pdf.worker.min.js') : '' }}';
                    if (localWorker) {
                        window.pdfjsLib.GlobalWorkerOptions.workerSrc = localWorker;
                    } else {
                        window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
                    }
                })();
            } catch (e) {
                console.warn('pdfjs worker setup failed, worker may be unavailable', e);
            }
        }

        // Helper to dispatch a global open-document event safely from non-Alpine onclick handlers
        window.openDocument = function(detail){
            try{
                window.dispatchEvent(new CustomEvent('open-document',{detail:detail}));
            } catch(e){
                console.error('openDocument dispatch error', e);
            }
        }


        function documentPreview(){
            return {
                showModal:false,
                loading:false,
                showNotPreviewable:false,
                embed:null,
                embedWithZoom:null,
                info:null,
                downloadUrl:null,
                title:'',
                mime:null,
                previewTypes:[],
                // PDF controls
                pdfZoom:'page-width',
                rotate:0,
                pdfDoc:null,
                currentPage:1,
                totalPages:0,
                scale:1,
                forceIframe:false,
                _progress:0,

                isPdf(){ return this.mime === 'application/pdf'; },

                setPdfZoom(z){
                    this.pdfZoom = z;
                    // trigger re-render of current page
                    this.renderPage(this.currentPage);
                },

                rotatePdf(){
                    this.rotate = (this.rotate + 90) % 360;
                    this.renderPage(this.currentPage);
                },

                async loadPdf(url){
                    try{
                        this.loading = true;
                        // clear any previous error for internal only; do not expose to UI
                        // error details are logged to console for diagnostics
                        
                        if (!window.pdfjsLib) throw new Error('pdfjs not loaded');
                        // withCredentials to allow cookies for same-origin sessions
                        const loadingTask = window.pdfjsLib.getDocument({ url: url, withCredentials: true });

                        // attach progress handler if available
                        if (loadingTask.onProgress) {
                            loadingTask.onProgress = (p) => { this._progress = Math.round((p.loaded / p.total) * 100); };
                        }

                        this.pdfDoc = await loadingTask.promise;
                        this.totalPages = this.pdfDoc.numPages;
                        this.currentPage = 1;
                        await this.renderPage(this.currentPage);
                    } catch(e){
                        // Log for diagnostics; do not show internal errors in the UI
                        console.warn('PDF load failed, falling back to iframe viewer', e);
                        // fall back to browser's viewer
                        this.forceIframe = true;
                        this.showNotPreviewable = false;
                        this.loading = false;
                    }
                },

                async renderPage(pageNum){
                    if (!this.pdfDoc) return;
                    this.loading = true;
                    this._progress = 0;

                    const page = await this.pdfDoc.getPage(pageNum);
                    const container = document.querySelector('.modal-pdf-canvas-container');
                    const canvas = document.getElementById('pdf-canvas');
                    if (!canvas || !container) { this.loading = false; return; }

                    // Determine scale based on pdfZoom (use unrotated page dims for fit calculations)
                    const viewport1 = page.getViewport({ scale: 1, rotation: 0 });
                    let containerWidth = container.clientWidth;
                    let containerHeight = container.clientHeight;
                    const r = (this.rotate % 360 + 360) % 360; // normalize
                    const rotated = (r === 90 || r === 270);
                    if (rotated) {
                        [containerWidth, containerHeight] = [containerHeight, containerWidth];
                    }

                    let desiredScale = 1;
                    if (this.pdfZoom === 'page-width') {
                        desiredScale = containerWidth / viewport1.width;
                    } else if (this.pdfZoom === 'page-fit') {
                        desiredScale = Math.min(containerWidth / viewport1.width, containerHeight / viewport1.height);
                    } else if (!isNaN(parseFloat(this.pdfZoom))) {
                        desiredScale = parseFloat(this.pdfZoom) / 100;
                    }

                    // Ask for viewport with requested rotation so width/height reflect rotation
                    const viewport = page.getViewport({ scale: desiredScale, rotation: this.rotate });
                    const context = canvas.getContext('2d');
                    const dpr = window.devicePixelRatio || 1;

                    canvas.width = Math.floor(viewport.width * dpr);
                    canvas.height = Math.floor(viewport.height * dpr);
                    canvas.style.width = Math.floor(viewport.width) + 'px';
                    canvas.style.height = Math.floor(viewport.height) + 'px';
                    canvas.style.maxWidth = '100%';

                    context.setTransform(dpr, 0, 0, dpr, 0, 0);
                    context.clearRect(0, 0, canvas.width, canvas.height);

                    const renderContext = {
                        canvasContext: context,
                        viewport: viewport,
                    };

                    // Render with progress updates
                    const renderTask = page.render(renderContext);
                    if (renderTask.on) {
                        renderTask.on('progress', (p) => {
                            this._progress = Math.round((p.loaded / p.total) * 100);
                        });
                    }

                    try {
                        await renderTask.promise;
                    } catch (e) {
                        console.warn('renderPage error, falling back to iframe', e);
                        // fall back to iframe if rendering failed
                        this.forceIframe = true;
                    }

                    this.loading = false;
                },

                nextPage(){ if (this.currentPage < this.totalPages) { this.currentPage++; this.renderPage(this.currentPage); } },



                prevPage(){ if (this.currentPage > 1) { this.currentPage--; this.renderPage(this.currentPage); } },

                // Keyboard handling for accessibility (left/right to change page, Esc to close)
                _onKeyDown(e){
                    if (!this.showModal) return;
                    if (e.key === 'ArrowLeft') { this.prevPage(); }
                    if (e.key === 'ArrowRight') { this.nextPage(); }
                    if (e.key === 'Escape') { this.close(); }
                },
                open(detail){
    
                    this.showModal = true;
                    this.loading = true;
                    this.showNotPreviewable = false;
                    this.embed = detail.embed ?? detail.url ?? null;
                    this.info = detail.info ?? null;
                    this.downloadUrl = detail.download ?? null;
                    this.title = detail.title ?? '';
                    this.previewTypes = [];
                    this.mime = null;
                    this.embedWithZoom = null;
                    this.pdfZoom = 'page-width';
                    this.rotate = 0;
                    this.pdfDoc = null;
                    this.currentPage = 1;
                    this.totalPages = 0;

                    if (this.info) {
                        fetch(this.info, { credentials: 'same-origin', headers:{'Accept':'application/json'} })
                            .then(r => { if (!r.ok) throw new Error('info fetch failed '+r.status); return r.json(); })
                            .then(json => {
                                this.previewTypes = json.preview_types ?? [];
                                this.mime = json.mime ?? null;

                                if (json.previewable) {
                                    this.embed = json.embed_url ?? this.embed;
                                                    if (this.mime === 'application/pdf') {
                                        // initialize pdf viewer
                                        this.forceIframe = false;
                                        // clear previous internal error (not exposed to users)
                                        // Attach keyboard listener for navigation and close
                                        this._keyHandler = this._onKeyDown.bind(this);
                                        window.addEventListener('keydown', this._keyHandler);
                                        this.loadPdf(this.embed).catch(e => {
                                            // ensure we fall back to iframe rendering if pdf.js fails
                                            console.warn('loadPdf failed, falling back to iframe', e);
                                            this.forceIframe = true;
                                            this.loading = false;
                                        });
                                    }
                                    this.showNotPreviewable = false;
                                    // keep loading until PDF rendered
                                } else {
                                    this.showNotPreviewable = true;
                                    this.loading = false;
                                }
                            }).catch(err => {
                                console.warn('preview info error', err);
                                this.showNotPreviewable = true;
                                this.loading = false;
                            });
                    } else {
                        this.loading = false;
                    }
                },
                close(){
                    this.showModal = false;
                    this.embed = null;
                    this.embedWithZoom = null;
                    this.title = '';
                    this.loading = false;
                    this.showNotPreviewable = false;
                    this.mime = null;
                    this.rotate = 0;
                    this.pdfZoom = 'page-width';
                    // Remove keyboard listener
                    try { if (this._keyHandler) { window.removeEventListener('keydown', this._keyHandler); this._keyHandler = null; } } catch(e) { /* no-op */ }
                    if (this.pdfDoc){ try{ this.pdfDoc.destroy(); }catch(e){/*no-op*/} this.pdfDoc = null; }
                }
            }
        }
    </script>
    @endonce
</div>
