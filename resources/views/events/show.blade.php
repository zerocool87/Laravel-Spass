<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">{{ $event->title }}</h2>
    </x-slot>

    <div class="container">
        <h1>{{ $event->title }}</h1>
        <p>{{ $event->start_at->format('Y-m-d H:i') }} @if($event->end_at) - {{ $event->end_at->format('Y-m-d H:i') }} @endif</p>
        <p><strong>Location:</strong> {{ $event->location }}</p>
        <div class="mt-4">{!! nl2br(e($event->description)) !!}</div>
    </div>
</x-app-layout>
