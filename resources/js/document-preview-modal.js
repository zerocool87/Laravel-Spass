(function () {
    window.openDocument = function (detail) {
        try {
            window.dispatchEvent(new CustomEvent('open-document', { detail: detail }));
        } catch (e) {
            console.error('openDocument dispatch error', e);
        }
    };

    document.addEventListener('alpine:init', () => {
        Alpine.data('documentPreview', () => ({
            showModal: false,
            loading: false,
            showNotPreviewable: false,
            errorMessage: '',
            embed: null,
            info: null,
            downloadUrl: null,
            title: '',
            mime: null,
            category: null,
            previewTypes: [],
            pdfZoom: 'page-width',
            rotate: 0,
            pdfDoc: null,
            currentPage: 1,
            totalPages: 0,
            scale: 1,
            forceIframe: false,
            _progress: 0,
            _keyHandler: null,
            __focusHandler: null,

            isPdf() {
                return this.mime === 'application/pdf';
            },

            setPdfZoom(z) {
                this.pdfZoom = z;
                this.renderPage(this.currentPage);
            },

            rotatePdf() {
                this.rotate = (this.rotate + 90) % 360;
                this.renderPage(this.currentPage);
            },

            async loadPdf(url) {
                try {
                    this.loading = true;

                    if (!window.pdfjsLib) {
                        throw new Error('PDF.js library not loaded');
                    }

                    const loadingTask = window.pdfjsLib.getDocument({ url: url, withCredentials: true });

                    if (loadingTask.onProgress) {
                        loadingTask.onProgress = (p) => {
                            this._progress = Math.round((p.loaded / p.total) * 100);
                        };
                    }

                    this.pdfDoc = await loadingTask.promise;
                    this.totalPages = this.pdfDoc.numPages;
                    this.currentPage = 1;
                    await this.renderPage(this.currentPage);
                } catch (e) {
                    console.warn('PDF load failed, falling back to iframe viewer', e);
                    this.forceIframe = true;
                    this.showNotPreviewable = false;
                    this.loading = false;
                }
            },

            async renderPage(pageNum) {
                if (!this.pdfDoc) return;
                this.loading = true;
                this._progress = 0;
                await this.$nextTick();

                const page = await this.pdfDoc.getPage(pageNum);
                const container = this.$refs.pdfContainer;
                const canvas = this.$refs.pdfCanvas;

                if (!canvas || !container) {
                    this.loading = false;
                    return;
                }

                const viewport1 = page.getViewport({ scale: 1, rotation: 0 });
                let containerWidth = container.clientWidth;
                let containerHeight = container.clientHeight;
                const r = (this.rotate % 360 + 360) % 360;
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
                    this.forceIframe = true;
                }

                this.loading = false;
            },

            nextPage() {
                if (this.currentPage < this.totalPages) {
                    this.currentPage++;
                    this.renderPage(this.currentPage);
                }
            },

            prevPage() {
                if (this.currentPage > 1) {
                    this.currentPage--;
                    this.renderPage(this.currentPage);
                }
            },

            _onKeyDown(e) {
                if (!this.showModal) return;
                if (e.key === 'ArrowLeft') this.prevPage();
                if (e.key === 'ArrowRight') this.nextPage();
                if (e.key === 'Escape') this.close();
            },

            __onFocusTrap(e) {
                if (!this.showModal || e.key !== 'Tab') return;
                const modal = this.$el.querySelector('[role="dialog"]');
                if (!modal) return;
                const focusable = modal.querySelectorAll(
                    'a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])'
                );
                if (!focusable.length) return;
                const first = focusable[0];
                const last = focusable[focusable.length - 1];
                if (e.shiftKey && document.activeElement === first) {
                    e.preventDefault();
                    last.focus();
                } else if (!e.shiftKey && document.activeElement === last) {
                    e.preventDefault();
                    first.focus();
                }
            },

            async open(detail) {
                this.showModal = true;
                this.loading = true;
                this.showNotPreviewable = false;
                this.errorMessage = '';
                this.embed = detail.embed ?? detail.url ?? null;
                this.info = detail.info ?? null;
                this.downloadUrl = detail.download ?? null;
                this.title = detail.title ?? '';
                this.category = detail.category ?? null;
                this.previewTypes = [];
                this.mime = null;
                this.pdfZoom = 'page-width';
                this.rotate = 0;
                this.pdfDoc = null;
                this.currentPage = 1;
                this.totalPages = 0;
                this.forceIframe = false;

                this._keyHandler = this._onKeyDown.bind(this);
                this.__focusHandler = this.__onFocusTrap.bind(this);
                window.addEventListener('keydown', this._keyHandler);
                window.addEventListener('keydown', this.__focusHandler);

                if (!this.info) {
                    this.loading = false;
                    return;
                }

                try {
                    const res = await fetch(this.info, {
                        credentials: 'same-origin',
                        headers: { 'Accept': 'application/json' },
                    });

                    if (!res.ok) {
                        throw new Error('Erreur lors du chargement des informations du document');
                    }

                    const json = await res.json();
                    this.previewTypes = json.preview_types ?? [];
                    this.mime = json.mime ?? null;
                    this.category = json.category ?? this.category;

                    if (!json.previewable) {
                        this.showNotPreviewable = true;
                        this.loading = false;
                        return;
                    }

                    this.embed = json.embed_url ?? this.embed;

                    if (this.mime === 'application/pdf') {
                        this.forceIframe = false;
                        try {
                            await this.loadPdf(this.embed);
                        } catch (e) {
                            console.warn('loadPdf failed, falling back to iframe', e);
                            this.forceIframe = true;
                            this.loading = false;
                        }
                    } else {
                        this.loading = false;
                    }
                } catch (err) {
                    this.errorMessage = err.message || 'Impossible de charger le document.';
                    this.loading = false;
                    this.showNotPreviewable = true;
                    console.warn('preview info error', err);
                }
            },

            close() {
                this.showModal = false;
                this.errorMessage = '';
                this.embed = null;
                this.title = '';
                this.loading = false;
                this.showNotPreviewable = false;
                this.mime = null;
                this.rotate = 0;
                this.pdfZoom = 'page-width';

                if (this._keyHandler) {
                    window.removeEventListener('keydown', this._keyHandler);
                    this._keyHandler = null;
                }

                if (this.__focusHandler) {
                    window.removeEventListener('keydown', this.__focusHandler);
                    this.__focusHandler = null;
                }

                if (this.pdfDoc) {
                    try { this.pdfDoc.destroy(); } catch (e) { /* no-op */ }
                    this.pdfDoc = null;
                }
            },
        }));
    });
})();
