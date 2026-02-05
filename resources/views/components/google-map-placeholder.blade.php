@props([
    'title' => 'Localisation du projet',
    'location' => 'Paris, France',
    'width' => '100%',
    'height' => '400px',
    'zoom' => 12
])

<div class="widget-container">
    <x-widget-header 
        title="🗺️ {{ $title }}" 
        linkText="{{ __('Voir sur Google Maps') }}"
        linkIcon="🔗"
    />
    <div class="widget-content">
        <div class="relative overflow-hidden rounded-lg" style="width: {{ $width }}; height: {{ $height }};">
            <!-- Google Maps Style Placeholder -->
            <div class="absolute inset-0 bg-gray-200">
                <!-- Map Grid Background -->
                <div class="absolute inset-0 opacity-30">
                    <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                                <path d="M 40 0 L 0 0 0 40" fill="none" stroke="#e5e7eb" stroke-width="1"/>
                            </pattern>
                        </defs>
                        <rect width="100%" height="100%" fill="url(#grid)"/>
                    </svg>
                </div>
                
                <!-- Map Controls (Mock) -->
                <div class="absolute top-4 left-4 z-10">
                    <div class="bg-white rounded shadow-lg p-2 flex flex-col space-y-2">
                        <button class="w-8 h-8 bg-white rounded border hover:bg-gray-50 transition">
                            <svg class="w-4 h-4 mx-auto text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </button>
                        <button class="w-8 h-8 bg-white rounded border hover:bg-gray-50 transition">
                            <svg class="w-4 h-4 mx-auto text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Google Logo (Mock) -->
                <div class="absolute bottom-4 left-4 z-10">
                    <div class="bg-white px-2 py-1 rounded shadow flex items-center space-x-1">
                        <div class="w-4 h-4 bg-[#4285F4] rounded"></div>
                        <span class="text-xs text-gray-600 font-medium">Google</span>
                    </div>
                </div>
                
                <!-- Location Marker -->
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-10">
                    <div class="relative">
                        <!-- Marker Shadow -->
                        <div class="absolute -bottom-2 -left-2 w-8 h-4 bg-black/20 rounded-full"></div>
                        <!-- Marker -->
                        <div class="w-6 h-10 bg-[#faa21b] rounded-full flex items-end justify-center relative">
                            <div class="w-3 h-3 bg-white rounded-full mb-1"></div>
                        </div>
                        <!-- Info Window -->
                        <div class="absolute -top-16 -left-12 bg-white rounded shadow-lg p-2 min-w-[200px] border">
                            <div class="text-sm font-semibold text-gray-900">{{ $location }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ __('Localisation approximative') }}</div>
                            <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 w-3 h-3 bg-white rotate-45 border-l border-t"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Map Attribution -->
                <div class="absolute bottom-2 right-2 text-xs text-gray-500 bg-white/80 px-2 py-1 rounded">
                    © Google Maps
                </div>
                
                <!-- Loading Overlay (Optional) -->
                <div class="absolute inset-0 bg-white/50 flex items-center justify-center" id="map-loading" style="display: none;">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#faa21b]"></div>
                </div>
            </div>
        </div>
        
        <!-- Map Details -->
        <div class="mt-4 p-4 bg-[#faa21b]/5 rounded-lg border border-[#faa21b]/20">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="font-semibold text-gray-900">{{ __('Coordonnées approximatives') }}</h4>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ __('Lat') }}: 48.8566, {{ __('Long') }}: 2.3522 ({{ $location }})
                    </p>
                </div>
                <button class="btn-primary-orange text-xs px-3 py-1">
                    {{ __('Ouvrir dans Maps') }}
                </button>
            </div>
        </div>
    </div>
</div>