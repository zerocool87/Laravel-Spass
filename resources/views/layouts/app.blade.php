<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{ largeText: localStorage.getItem('spass_large_text') === 'true' }"
    x-init="
        if (largeText) document.documentElement.classList.add('text-large');
        $watch('largeText', val => {
            document.documentElement.classList.toggle('text-large', val);
            localStorage.setItem('spass_large_text', val);
        });
    "
    :class="{ 'text-large': largeText }"
>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <!-- Vite manifest not found: static assets may be missing. Run `npm run build` or `npm run dev` locally. -->
        @endif
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-[#fffbe9]">
            {{-- Accessibility toolbar --}}
            <div class="fixed top-4 right-4 z-[100] flex gap-2">
                <button
                    @click="largeText = !largeText"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-bold transition shadow-lg"
                    :class="largeText ? 'bg-[#faa21b] text-white' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'"
                    :title="largeText ? 'Texte normal' : 'Agrandir le texte'"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <span x-text="largeText ? 'A' : 'A+'"></span>
                </button>
            </div>

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow-sm border-b border-[#faa21b]/10">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Breadcrumbs -->
            @isset($breadcrumbs)
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-4">
                    {{ $breadcrumbs }}
                </div>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        {{-- Toast container --}}
        <div
            x-data
            x-init="
                @if(session('success'))
                    $store.toasts.add('{{ session('success') }}', 'success');
                @endif
                @if(session('error'))
                    $store.toasts.add('{{ session('error') }}', 'error');
                @endif
                @if(session('info'))
                    $store.toasts.add('{{ session('info') }}', 'info');
                @endif
                @if(session('celebrate'))
                    setTimeout(() => $store.confetti.fire({ particleCount: 100 }), 300);
                @endif
            "
            class="fixed bottom-6 right-6 z-50 flex flex-col gap-3 max-w-sm w-full pointer-events-none"
        >
            <template x-for="toast in $store.toasts.items" :key="toast.id">
                <div
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-4"
                    class="pointer-events-auto flex items-start gap-3 px-5 py-4 rounded-xl shadow-2xl border cursor-pointer"
                    :class="{
                        'bg-emerald-600 text-white border-emerald-700': toast.type === 'success',
                        'bg-red-600 text-white border-red-700': toast.type === 'error',
                        'bg-blue-600 text-white border-blue-700': toast.type === 'info',
                    }"
                    @click="$store.toasts.remove(toast.id)"
                >
                    <span class="text-lg flex-shrink-0 leading-none" x-text="toast.type === 'success' ? '✅' : toast.type === 'error' ? '❌' : 'ℹ️'"></span>
                    <p class="text-base font-semibold flex-1" x-text="toast.message"></p>
                    <button @click.stop="$store.toasts.remove(toast.id)" class="text-white/70 hover:text-white flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </template>
        </div>
    </body>
</html>
