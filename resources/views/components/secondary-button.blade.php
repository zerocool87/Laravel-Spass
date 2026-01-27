@php
    // Detect edit links and use primary (orange) style for edit actions
    $href = $attributes->get('href') ?? '';
    // Use explicit prop for action type if provided, fallback to href detection
    $actionType = $attributes->get('action') ?? null;
    $isEdit = $actionType === 'edit' || (is_string($href) && (str_contains($href, '/edit') || str_contains($href, '.edit')));
    $baseClass = $isEdit ? 'btn-primary' : 'btn-secondary';
    $classes = $baseClass . ' inline-flex items-center px-4 py-2 rounded-lg font-medium text-sm';
@endphp

@if($attributes->has('href'))
    <a {{ $attributes->except('type')->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => $attributes->get('type', 'button'), 'class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
