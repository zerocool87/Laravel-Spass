@props(['category' => null])

@if(!$category)
    <span class="text-xs text-gray-400">—</span>
@else
    @php
        $colors = config('documents.category_colors', []);
        $icons = config('documents.category_icons', []);
        $infoColor = $colors[$category] ?? 'bg-gray-400';
        $icon = $icons[$category] ?? '';

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
        @if(!empty($icon))
            <span class="-ml-1 mr-2">{!! $icon !!}</span>
        @endif
        <span>{{ __($category) }}</span>
    </span>
@endif
