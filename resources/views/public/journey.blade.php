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
        {{-- Simple Top Nav --}}
        <nav class="max-w-5xl mx-auto p-6 md:p-12 flex items-center justify-between">
            <a href="{{ route('welcome') }}" class="w-10 h-10 rounded-full hover:bg-white/5 flex items-center justify-center transition-transform active:scale-90">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <h1 class="text-xl md:text-4xl font-bold tracking-tighter lowercase">
                {{ $settings->journey_title ?? 'all journey' }}
            </h1>
            <div class="w-10"></div>
        </nav>

        {{-- Responsive Video List --}}
        <main class="max-w-7xl mx-auto px-4 md:px-12 lg:px-24 py-8 md:py-12">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-8">
                @forelse($videos as $video)
                    <a href="https://youtube.com/watch?v={{ $video['id'] }}" target="_blank" 
                       class="flex gap-4 md:gap-8 p-3 rounded-3xl hover:bg-white/5 active:scale-[0.98] transition-all group">
                        
                        {{-- Thumbnail --}}
                        <div class="relative w-36 md:w-56 aspect-video rounded-2xl overflow-hidden flex-shrink-0 bg-black/20 shadow-xl border border-white/5">
                            <img src="https://img.youtube.com/vi/{{ $video['id'] }}/hqdefault.jpg" 
                                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <div class="absolute bottom-2 right-2 bg-black/80 backdrop-blur-sm text-[10px] font-bold px-2 py-0.5 rounded text-white tracking-tight uppercase">
                                Play
                            </div>
                        </div>

                        {{-- Info --}}
                        <div class="flex-grow py-1 flex flex-col justify-center min-w-0">
                            <h3 class="text-sm md:text-2xl font-bold tracking-tight leading-tight line-clamp-2 theme-text mb-1 lowercase">
                                {{ $video['title'] }}
                            </h3>
                            <p class="text-[11px] md:text-sm opacity-40 font-medium line-clamp-2 lowercase tracking-tight leading-relaxed">
                                {{ \Illuminate\Support\Str::limit($video['description'] ?? 'our journey', 100) }}
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

        {{-- Premium Footer (Same as Welcome) --}}
        <footer class="relative z-10 py-12 px-6 border-t theme-border">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-brand-500/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold tracking-tight lowercase">{{ $spaceName }}</p>
                        <p class="text-xs opacity-40 lowercase">all rights reserved © 2026</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
