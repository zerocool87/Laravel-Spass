<?php

declare(strict_types=1);

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
        // Harmonized palette centered on orange accents for the app
        'Convocations' => 'bg-amber-600',     // meetings / summons
        'Ordres du jour' => 'bg-amber-500',   // agendas
        'Comptes rendus' => 'bg-emerald-600', // minutes / reports
        'Rapports' => 'bg-cyan-600',         // longer reports
        'Délibérations' => 'bg-rose-600',     // decisions / important
        'Guides' => 'bg-sky-600',            // help / guides
    ],

    // MIME patterns considered previewable by Document::isPreviewable
    'preview_mime_patterns' => ['image/', 'text/', 'application/pdf'],
];
