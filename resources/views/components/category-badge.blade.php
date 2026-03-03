@props(['category' => null])

@if(!$category)
    <span class="text-xs text-gray-400">—</span>
@else
    @php
        $colors = config('documents.category_colors', []);
        $icons = config('documents.category_icons', []);
        $infoColor = $colors[$category] ?? 'bg-gray-400';
        $icon = $icons[$category] ?? '';

        // Sanitize icon HTML: strip scripts and inline event handlers
        $safeIcon = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $icon);
        $safeIcon = preg_replace('/\s+on[a-z]+\s*=\s*(".*?"|\'.*?\'|[^\s>]+)/i', '', $safeIcon);

        // Forcera une couleur de texte foncée sur les couleurs claires, sinon blanc
        // Use a simple contrast check for accessibility
        $lightBg = preg_match('/amber-|yellow-|lime-|sky-|cyan-/', $infoColor);
        $textClass = $lightBg ? 'text-gray-900' : 'text-white';
        // Ajoute un contour pour la lisibilité sur fond blanc
        $borderClass = 'border border-gray-200';
        // Accentue l'ombre
        $shadowClass = 'shadow-md';
    @endphp

    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-semibold {{ $infoColor }} {{ $textClass }} {{ $borderClass }} {{ $shadowClass }}" aria-label="{{ __($category) }}">
        @if(!empty($safeIcon))
            <span class="-ml-1 mr-2">{!! $safeIcon !!}</span>
        @endif
        <span>{{ __($category) }}</span>
    </span>
@endif
