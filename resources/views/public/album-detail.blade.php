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
    <title>{{ $spaceName }} — {{ $monthName }} {{ $year }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@400;500;600&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body x-data="{
        currentTheme: '{{ $theme }}',
        themes: ['light', 'dark', 'rose', 'midnight', 'sky', 'mint', 'lavender', 'pink']
    }" 
    :data-theme="currentTheme"
    :class="currentTheme"
    data-theme="{{ $theme }}"
    class="{{ $theme }} antialiased font-sans theme-text theme-bg transition-colors duration-1000">

    <div class="relative min-h-screen">
        {{-- Hero Header --}}
        <header class="relative w-full aspect-[4/5] md:aspect-[21/9] overflow-hidden"
                x-data="{ 
                    active: 0, 
                    images: {{ json_encode($allMediaPaths) }},
                    init() {
                        if (this.images.length > 1) {
                            setInterval(() => {
                                this.active = (this.active + 1) % this.images.length;
                            }, 3000);
                        }
                    }
                }">
            @if(count($allMediaPaths) > 0)
                <template x-for="(img, index) in images" :key="index">
                    <div x-show="active === index" 
                         x-transition:enter="transition opacity duration-1000 ease-in-out"
                         x-transition:leave="transition opacity duration-1000 ease-in-out"
                         class="absolute inset-0 w-full h-full">
                        <img :src="img" class="w-full h-full object-cover">
                    </div>
                </template>
            @else
                <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-300 flex items-center justify-center italic opacity-30">no preview</div>
            @endif

            {{-- Top Navigation Bar --}}
            <nav class="absolute top-0 left-0 right-0 p-4 md:p-8 flex items-center justify-between z-20">
                <a href="{{ route('welcome') }}" class="w-10 h-10 rounded-full bg-black/20 backdrop-blur-xl border border-white/10 flex items-center justify-center text-white transition-transform active:scale-90">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <div class="w-10"></div>
            </nav>

            {{-- Hero Bottom Info --}}
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent flex flex-col justify-end p-6 md:p-12 text-white pointer-events-none">
                <h1 class="text-4xl md:text-6xl font-bold tracking-tight lowercase">{{ $monthName }}</h1>
                <div class="flex items-center gap-2 opacity-80 mt-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                    <span class="text-sm md:text-base font-medium">{{ count($allMediaPaths) }} {{ \Illuminate\Support\Str::plural('item', count($allMediaPaths)) }}</span>
                </div>
            </div>
        </header>

        {{-- Gapless Square Grid --}}
        <main class="w-full">
            <div class="grid grid-cols-3 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-8 gap-[1px]">
                @foreach($posts as $post)
                    @foreach($post->media as $media)
                        <a href="{{ route('posts.preview', $post) }}" 
                           class="relative aspect-square overflow-hidden group">
                            <img src="{{ Storage::disk('public')->url($media->file_path_original) }}" 
                                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            
                            {{-- Selection Overlay --}}
                            <div class="absolute inset-0 bg-white/0 group-hover:bg-white/10 transition-colors pointer-events-none"></div>
                        </a>
                    @endforeach
                @endforeach
            </div>

            @if($posts->hasPages())
                <div class="mt-12 mb-20 px-4">
                    {{ $posts->links() }}
                </div>
            @endif
        </main>
    </div>

    @livewireScripts
</body>
</html>
