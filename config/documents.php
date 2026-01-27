<?php

return [
    // Available document categories shown in admin forms and filters
    'categories' => [
        'Convocations',
        'Ordres du jour',
        'Comptes rendus',
        'Rapports',
        'Délibérations',
        'Guides',
    ],

    // Per-category style tokens (tailwind classes) for easy customization in the UI
    'category_colors' => [
        'Convocations' => 'bg-blue-600',
        'Ordres du jour' => 'bg-yellow-600',
        'Comptes rendus' => 'bg-green-600',
        'Rapports' => 'bg-indigo-600',
        'Délibérations' => 'bg-red-600',
        'Guides' => 'bg-teal-600',
    ],

    // Per-category icons (SVG markup) which can be overridden in different environments
    'category_icons' => [
        'Convocations' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2z"/></svg>',
        'Ordres du jour' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>',
        'Comptes rendus' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m2 0a2 2 0 012 2v4a2 2 0 01-2 2H7a2 2 0 01-2-2v-4a2 2 0 012-2h12zm0-6V5a2 2 0 00-2-2H9a2 2 0 00-2 2v1"/></svg>',
        'Rapports' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m-6 4h6m-6 4h3m5-12H7a2 2 0 00-2 2v12l3-2 3 2 3-2 3 2V5a2 2 0 00-2-2z"/></svg>',
        'Délibérations' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9V5a2 2 0 012-2h8a2 2 0 012 2v4m-6 6v3m-3-3h6m-7-4h8a1 1 0 011 1v3a4 4 0 01-4 4h-2a4 4 0 01-4-4v-3a1 1 0 011-1z"/></svg>',
        'Guides' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h9l3 3v11a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 8h4"/></svg>',
    ],

    // Examples of preview types shown in preview modal (kept for backward compatibility)
    'preview_examples' => [
        'PDF', 'JPEG', 'PNG', 'TXT',
    ],

    // MIME patterns considered previewable by Document::isPreviewable
    'preview_mime_patterns' => ['image/', 'text/', 'application/pdf'],
];
