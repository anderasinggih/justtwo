<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @php
        $themeColors = [
            'light' => ['bg' => '#ffffff', 'icon' => 'bg-white/10', 'overlay' => 'bg-black/5', 'border' => 'border-black/5'],
            'dark' => ['bg' => '#000000', 'icon' => 'bg-white/10', 'overlay' => 'bg-white/5', 'border' => 'border-white/5'],
            'rose' => ['bg' => '#fff1f2', 'icon' => 'bg-rose-500/10', 'overlay' => 'bg-rose-500/5', 'border' => 'border-rose-200/50'],
            'midnight' => ['bg' => '#020617', 'icon' => 'bg-blue-500/10', 'overlay' => 'bg-blue-500/5', 'border' => 'border-white/5'],
            'sky' => ['bg' => '#f0f9ff', 'icon' => 'bg-sky-500/10', 'overlay' => 'bg-sky-500/5', 'border' => 'border-sky-200/50'],
            'mint' => ['bg' => '#f0fdf4', 'icon' => 'bg-emerald-500/10', 'overlay' => 'bg-emerald-500/5', 'border' => 'border-emerald-200/50'],
            'lavender' => ['bg' => '#f5f3ff', 'icon' => 'bg-violet-500/10', 'overlay' => 'bg-violet-500/5', 'border' => 'border-violet-200/50'],
            'pink' => ['bg' => '#fff5f5', 'icon' => 'bg-pink-500/10', 'overlay' => 'bg-pink-500/5', 'border' => 'border-pink-200/50'],
        ];
        $colors = $themeColors[$theme] ?? $themeColors['light'];
        $bgColor = $colors['bg'];
        $iconBg = $colors['icon'];
        $overlayBg = $colors['overlay'];
        $relationship = \App\Models\Relationship::orderBy('id', 'desc')->first();
        $spaceName = $relationship?->name ?? 'justtwo';
    @endphp

    <meta name="theme-color" content="{{ $bgColor }}">
    <title>{{ $spaceName }} — Preview</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@400;500;600&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>

<body x-data="{
        currentTheme: '{{ $theme }}',
        currentIndex: 0,
        showInfo: false,
        showHeart: false,
        allMedia: @js($allMedia),
        map: null,
        marker: null,

        init() {
            // No need to scroll to initialMediaIndex as we start from 0 for single post preview usually
        },
        onCarouselScroll(e) {
            const scrollLeft = e.target.scrollLeft;
            const width = e.target.clientWidth;
            const newIndex = Math.round(scrollLeft / width);
            if (newIndex !== this.currentIndex) {
                this.currentIndex = newIndex;
                this.scrollToThumb(newIndex);
            }
        },
        scrollToThumb(index) {
            const thumb = document.getElementById('thumb-' + index);
            if (thumb && this.$refs.filmstrip) {
                const container = this.$refs.filmstrip;
                const scrollLeft = thumb.offsetLeft - (container.clientWidth / 2) + (thumb.clientWidth / 2);
                container.scrollTo({ left: scrollLeft, behavior: 'smooth' });
            }
        },
        initMap() {
            const media = this.allMedia[this.currentIndex];
            if (!media.lat || !media.lng) return;

            setTimeout(() => {
                if (this.map) {
                    this.map.setView([media.lat, media.lng], 15);
                    this.marker.setLatLng([media.lat, media.lng]);
                } else {
                    const tileUrl = (this.currentTheme === 'dark' || this.currentTheme === 'midnight')
                        ? 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png'
                        : 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png';

                    this.map = L.map('map-preview', { zoomControl: false, attributionControl: false }).setView([media.lat, media.lng], 15);
                    L.tileLayer(tileUrl).addTo(this.map);
                    this.marker = L.marker([media.lat, media.lng]).addTo(this.map);
                }
            }, 100);
        },
        async toggleLike() {
            try {
                const response = await fetch(`/posts/{{ $post->id }}/react`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json'
                    }
                });
                if (response.ok) {
                    this.showHeart = true;
                    setTimeout(() => this.showHeart = false, 800);
                }
            } catch (error) {
                console.error('Error toggling like:', error);
            }
        }
    }" 
    :data-theme="currentTheme"
    :class="currentTheme"
    class="antialiased font-sans theme-text theme-bg transition-colors duration-1000 overflow-hidden">

    <div class="fixed inset-0 theme-bg theme-text z-[200] flex flex-col overflow-hidden select-none">
        <nav class="p-4 md:p-6 flex items-center justify-between z-30">
            @php
                $date = \Carbon\Carbon::parse($post->created_at);
                $backUrl = route('public.album', ['year' => $date->format('Y'), 'month' => $date->format('F')]);
            @endphp
            <a href="{{ $backUrl }}" class="w-10 h-10 rounded-full {{ $iconBg }} backdrop-blur-xl border {{ $colors['border'] }} flex items-center justify-center transition-transform active:scale-90">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div class="flex flex-col items-center {{ $overlayBg }} backdrop-blur-md px-6 py-1.5 rounded-full min-w-[150px] cursor-pointer" @click="showInfo = true; initMap()">
                <template x-for="(media, index) in allMedia">
                    <div x-show="currentIndex === index" class="flex flex-col items-center text-center">
                        <span class="text-[10px] uppercase tracking-widest opacity-50 font-bold" x-text="media.captured_at"></span>
                        <span class="text-xs font-bold truncate max-w-[120px]" x-text="media.location_name || 'Unidentified'"></span>
                    </div>
                </template>
            </div>
            <div class="w-10"></div>
        </nav>

        <main class="flex-1 relative flex flex-col justify-start pt-2 md:pt-6 overflow-hidden">
            <div class="relative w-full h-[65vh] md:h-[75vh] flex items-center overflow-x-auto snap-x snap-mandatory scrollbar-hide" 
                 @scroll="onCarouselScroll($event)" 
                 @click="showInfo = false"
                 x-ref="carousel">
                <template x-for="(m, index) in allMedia" :key="index">
                    <div class="flex-none w-full h-full snap-center flex items-center justify-center relative" @dblclick="toggleLike()">
                        <template x-if="m.file_type.includes('video')">
                            <video :src="m.file_path" class="w-full h-full object-cover md:object-contain" autoplay loop muted playsinline></video>
                        </template>
                        <template x-if="!m.file_type.includes('video')">
                            <img :src="m.file_path" draggable="false" class="w-full h-full object-cover md:object-contain">
                        </template>
                        
                        {{-- Heart Animation --}}
                        <div x-show="showHeart" x-cloak x-transition:enter="transition-all ease-[cubic-bezier(0.175,0.885,0.32,1.275)] duration-500" x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-125" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 scale-125" x-transition:leave-end="opacity-0 scale-150" class="absolute inset-0 flex items-center justify-center pointer-events-none z-20">
                            <svg class="w-32 h-32 text-brand-500 fill-current filter drop-shadow-[0_0_30px_rgba(244,63,94,0.6)]" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                        </div>
                    </div>
                </template>
            </div>
        </main>

        <footer class="pb-16 pt-4 overflow-hidden relative">
            <div class="absolute left-1/2 top-4 bottom-16 w-[1px] bg-white/20 -translate-x-1/2 z-20 pointer-events-none"></div>
            <div class="flex items-center gap-0 overflow-x-auto overflow-y-hidden scrollbar-hide px-4 py-4" @scroll="onThumbsScroll($event)" x-ref="filmstrip">
                <div class="flex-none w-[48vw]"></div>
                <template x-for="(m, index) in allMedia" :key="index">
                    <button @click="currentIndex = index; $refs.carousel.scrollTo({ left: $refs.carousel.clientWidth * index, behavior: 'smooth' }); scrollToThumb(index)" 
                            :id="'thumb-' + index" 
                            class="thumb-item flex-none w-10 h-14 md:w-12 md:h-16 rounded-none overflow-hidden transition-all duration-300" 
                            :class="currentIndex === index ? 'scale-125 z-10 opacity-100' : 'opacity-30 scale-90'">
                        <img :src="m.file_path" class="w-full h-full object-cover">
                    </button>
                </template>
                <div class="flex-none w-[48vw]"></div>
            </div>
        </footer>

        {{-- Info Rack (Location Detail) --}}
        <div x-show="showInfo" x-cloak 
             x-transition:enter="transition transform duration-500 ease-[cubic-bezier(0.19,1,0.22,1)]" 
             x-transition:enter-start="translate-y-full" 
             x-transition:enter-end="translate-y-0" 
             x-transition:leave="transition transform duration-400 ease-[cubic-bezier(0.19,1,0.22,1)]" 
             x-transition:leave-start="translate-y-0" 
             x-transition:leave-end="translate-y-full" 
             class="absolute inset-x-0 bottom-0 h-[42vh] theme-bg backdrop-blur-3xl rounded-t-[2.5rem] z-[300] p-6 flex flex-col space-y-4 overflow-hidden border-t theme-border shadow-[0_-10px_40px_rgba(0,0,0,0.1)]">
            <div class="w-12 h-1.5 bg-current opacity-20 rounded-full mx-auto mb-1" @click="showInfo = false"></div>
            <div class="flex-1 overflow-y-auto scrollbar-hide">
                <template x-for="(media, index) in allMedia">
                    <div x-show="currentIndex === index" class="flex flex-col space-y-4">
                        <div class="flex items-start justify-between">
                            <div class="space-y-1">
                                <h3 class="text-xl font-bold leading-tight" x-text="media.location_name || 'Unidentified'"></h3>
                                <p class="text-xs opacity-50 font-medium tracking-wide uppercase" x-text="media.captured_at"></p>
                            </div>
                            <div class="p-2 rounded-full {{ $iconBg }} border {{ $colors['border'] }}">
                                <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                        </div>
                        <div id="map-preview" class="w-full h-40 rounded-3xl overflow-hidden grayscale-[0.5] brightness-[0.9] border {{ $colors['border'] }}"></div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</body>
</html>
