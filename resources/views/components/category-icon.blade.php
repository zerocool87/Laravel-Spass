@props(['category' => null, 'document' => null, 'size' => 'w-5 h-5'])

@php
    $doc = $document ?? (isset($category) ? (new \App\Models\Document(['category' => $category])) : null);
    
    if (!$doc) {
        $doc = new \App\Models\Document();
    }
    
    $colorClass = $doc->getCategoryColor();
    $icon = $doc->getCategoryIcon();
    
    // Extract color from Tailwind class (e.g., "bg-amber-600" -> "amber-600")
    $colorName = str_replace('bg-', '', $colorClass);
    
    // Get the RGB values for the color
    $colorMap = [
        'amber-600' => '217, 119, 6',
        'amber-500' => '245, 158, 11',
        'emerald-600' => '5, 150, 105',
        'cyan-600' => '8, 145, 178',
        'rose-600' => '225, 29, 72',
        'sky-600' => '2, 132, 199',
        'faa21b' => '250, 162, 27',
    ];
    
    $rgb = $colorMap[$colorName] ?? '250, 162, 27';
@endphp

<div class="inline-flex items-center justify-center {{ $size }} rounded-sm" style="background-color: rgba({{ $rgb }}, 0.1); color: rgb({{ $rgb }})">
    {!! $icon !!}
</div>