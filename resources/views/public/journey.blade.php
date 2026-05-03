<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    
    @php
        $themeColors = [
            'light' => '#ffffff', 'dark' => '#000000', 'rose' => '#fff1f2', 'midnight' => '#020617',
            'sky' => '#f0f9ff', 'mint' => '#f0fdf4', 'lavender' => '#f5f3ff', 'pink' => '#fff5f5'
        ];
        $bgColor = $themeColors[$theme] ?? '#ffffff';
        $relationship = \App\Models\Relationship::orderBy('id', 'desc')->first();
        $spaceName = $relationship?->name ?? 'justtwo';
    @endphp

    <meta name="theme-color" content="{{ $bgColor }}">
    <title>{{ $spaceName }} — Our Journey</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@400;500;600&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body x-data="{ currentTheme: '{{ $theme }}' }" 
    :data-theme="currentTheme"
    :class="currentTheme"
    class="antialiased font-sans theme-text theme-bg transition-colors duration-1000">

    <div class="relative min-h-screen">
        {{-- Hero Header --}}
        <header class="relative w-full h-[40vh] md:h-[50vh] lg:h-[60vh] overflow-hidden bg-black flex flex-col items-center justify-center text-center px-6">
            <div class="absolute inset-0 opacity-40">
                <div class="absolute inset-0 bg-gradient-to-b from-transparent via-black/20 to-black/80"></div>
                @if(count($videos) > 0)
                    <img src="https://img.youtube.com/vi/{{ $videos[0]['id'] }}/maxresdefault.jpg" class="w-full h-full object-cover blur-sm">
                @endif
            </div>

            <nav class="absolute top-0 left-0 right-0 p-4 md:p-8 flex items-center justify-between z-20">
                <a href="{{ route('welcome') }}" class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-xl border border-white/10 flex items-center justify-center text-white transition-transform active:scale-90">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <div class="w-10"></div>
            </nav>

            <div class="relative z-10 space-y-2">
                <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-white tracking-tighter lowercase drop-shadow-2xl">
                    {{ $settings->journey_title ?? 'Our Journey' }}
                </h1>
                <p class="text-white/60 text-sm md:text-lg max-w-md mx-auto">Shared memories captured in motion</p>
            </div>
        </header>

        {{-- Responsive Video List --}}
        <main class="max-w-5xl mx-auto px-4 py-12 md:py-16">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
                @forelse($videos as $video)
                    <a href="https://youtube.com/watch?v={{ $video['id'] }}" target="_blank" 
                       class="flex gap-4 md:gap-6 p-3 rounded-3xl hover:bg-white/5 active:scale-[0.98] transition-all group">
                        
                        {{-- Thumbnail --}}
                        <div class="relative w-32 md:w-48 aspect-video rounded-2xl overflow-hidden flex-shrink-0 bg-black/20 shadow-xl">
                            <img src="https://img.youtube.com/vi/{{ $video['id'] }}/hqdefault.jpg" 
                                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <div class="absolute bottom-1.5 right-1.5 bg-black/80 backdrop-blur-sm text-[10px] font-bold px-2 py-0.5 rounded text-white tracking-tight uppercase">
                                Play
                            </div>
                        </div>

                        {{-- Info --}}
                        <div class="flex-grow py-1 flex flex-col justify-center min-w-0">
                            <h3 class="text-sm md:text-xl font-bold tracking-tight leading-tight line-clamp-2 theme-text mb-1 lowercase">
                                {{ $video['title'] }}
                            </h3>
                            <p class="text-[11px] md:text-sm opacity-40 font-medium line-clamp-1 lowercase tracking-tight">
                                {{ \Illuminate\Support\Str::limit($video['description'] ?? 'our journey', 80) }}
                            </p>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full flex flex-col items-center justify-center py-20 opacity-20">
                        <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        <p class="text-xs font-bold uppercase tracking-widest">No journey videos found</p>
                    </div>
                @endforelse
            </div>
        </main>

        {{-- Footer --}}
        <footer class="max-w-5xl mx-auto mt-24 py-12 text-center border-t theme-border">
            <p class="text-[11px] opacity-40 tracking-widest uppercase">
                All Rights Reserved ©Copyright 2026 {{ $spaceName }}
            </p>
        </footer>
    </div>
</body>
</html>
