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

        {{-- Responsive Video Grid --}}
        <main class="max-w-7xl mx-auto px-4 py-12 md:py-16 lg:py-24">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 md:gap-10 lg:gap-12">
                @forelse($videos as $video)
                    <div class="group space-y-4">
                        <div class="relative aspect-video rounded-3xl overflow-hidden shadow-2xl transition-transform duration-500 group-hover:scale-[1.02] bg-gray-100 dark:bg-gray-800">
                            <iframe 
                                class="absolute inset-0 w-full h-full"
                                src="https://www.youtube.com/embed/{{ $video['id'] }}?modestbranding=1&rel=0&showinfo=0" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen>
                            </iframe>
                        </div>
                        <div class="px-2">
                            <h3 class="text-lg font-bold line-clamp-1 group-hover:theme-text-brand transition-colors">{{ $video['title'] }}</h3>
                            <p class="text-sm opacity-50 line-clamp-2 mt-1">{{ $video['description'] }}</p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-20 opacity-30 italic">
                        No videos found in the journey playlist.
                    </div>
                @endforelse
            </div>
        </main>

        {{-- Footer Spacer --}}
        <div class="h-20"></div>
    </div>
</body>
</html>
