<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Share+Tech+Mono&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
        :root{
            --neon-pink:#ff44cc;
            --neon-cyan:#00e5ff;
            --neon-green:#39ff14;
            --panel: rgba(255,255,255,0.02);
            --muted: rgba(255,255,255,0.06);
        }
        body{font-family: 'Orbitron', 'Share Tech Mono', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; color:#e6f7ff;}
        .neon-btn{background: linear-gradient(90deg,var(--neon-pink),var(--neon-cyan)); color:#041017; font-weight:700; box-shadow: 0 8px 36px rgba(0,229,255,0.12), 0 0 18px rgba(255,68,204,0.08) inset; border-radius:10px; padding:8px 14px; display:inline-block;}
        .neon-btn:hover{filter:brightness(1.06); transform: translateY(-1px);}
        .neon-outline{border:1px solid rgba(0,229,255,0.12); box-shadow: 0 0 18px rgba(0,229,255,0.06);} 
        .neon-h1{color:var(--neon-cyan); text-shadow:0 2px 18px rgba(0,229,255,0.08); font-family: 'Orbitron', sans-serif;}
        .cyber-table thead th{color:var(--neon-pink); border-bottom:1px solid rgba(255,68,204,0.06);} 
        .cyber-table tr:nth-child(even){background: rgba(255,255,255,0.02);} 
        input, select, textarea{background:#071018;border:1px solid rgba(255,255,255,0.04); color:#e6f7ff; padding:8px; border-radius:6px;}
        input:focus, textarea:focus{outline:none; box-shadow:0 0 18px rgba(0,229,255,0.08); border-color:var(--neon-cyan);} 
        .glass{background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01)); border:1px solid rgba(255,255,255,0.03); border-radius:8px; padding:14px;} 
        .accent-gradient{background: linear-gradient(90deg, rgba(0,229,255,0.06), rgba(255,68,204,0.06));}
        </style>
    </head>
    <body class="font-sans antialiased bg-gradient-to-b from-slate-900 via-gray-900 to-black text-gray-100">
        <div class="min-h-screen">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-gradient-to-r from-slate-800 to-gray-800 shadow-md">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 text-gray-100">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
