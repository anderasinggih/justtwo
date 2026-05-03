@php
    $themeColors = [
        'light' => ['bg' => 'bg-white', 'text' => 'text-gray-900', 'sub' => 'text-gray-400', 'border' => 'border-gray-50'],
        'dark' => ['bg' => 'bg-black', 'text' => 'text-white', 'sub' => 'text-white/40', 'border' => 'border-white/5'],
        'rose' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-900', 'sub' => 'text-rose-300', 'border' => 'border-rose-100'],
        'midnight' => ['bg' => 'bg-slate-900', 'text' => 'text-blue-50', 'sub' => 'text-blue-300/40', 'border' => 'border-white/5'],
    ];
    $colors = $themeColors[$theme] ?? $themeColors['light'];
    $overlayBg = in_array($theme, ['dark', 'midnight']) ? 'bg-white/10' : 'bg-black/5';
    $iconBg = in_array($theme, ['dark', 'midnight']) ? 'bg-white/10' : 'bg-black/10';
@endphp
<div class="fixed inset-0 theme-bg theme-text z-[200] flex flex-col overflow-hidden select-none" x-data="{ 
        currentIndex: {{ $initialMediaIndex }},
        total: {{ count($allMedia) }},
        showHeart: false,
        showInfo: false,
        touchStartY: 0,
        isScrollingCarousel: false,
        isScrollingThumbs: false,
        allMedia: @js($allMedia),
        handleTouchStart(e) { this.touchStartY = e.touches[0].clientY; },
        handleTouchEnd(e) {
            const touchEndY = e.changedTouches[0].clientY;
            const diff = this.touchStartY - touchEndY;
            if (diff > 80 && !this.showInfo) { 
                this.showInfo = true;
                this.$nextTick(() => this.initMap());
            } else if (diff < -80 && this.showInfo) {
                this.showInfo = false;
            }
        },
        initMap() {
            this.$nextTick(() => {
                const media = this.allMedia[this.currentIndex];
                if (!media || !media.lat || !media.lon) return;
                const mapId = 'map-' + this.currentIndex;
                const mapContainer = document.getElementById(mapId);
                if (!mapContainer || mapContainer._leaflet_id) return;
                setTimeout(() => {
                    const map = L.map(mapContainer, { zoomControl: false, attributionControl: false }).setView([media.lat, media.lon], 13);
                    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/dark_all/{z}/{x}/{y}{r}.png').addTo(map);
                    
                    const icon = L.divIcon({
                        className: 'custom-map-marker',
                        html: `<div class='relative flex flex-col items-center'><div class='w-14 h-14 p-1.5 theme-bg rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.8)] overflow-hidden border-2 theme-border transform transition-transform active:scale-95'><img src='${media.file_path}' class='w-full h-full object-cover rounded-xl'></div><div class='w-4 h-4 theme-bg theme-border border-r border-b rotate-45 -mt-2.5 shadow-lg'></div></div>`,
                        iconSize: [56, 68],
                        iconAnchor: [28, 68]
                    });
                    L.marker([media.lat, media.lon], { icon }).addTo(map);
                    setTimeout(() => { map.invalidateSize(); }, 200);
                }, 300);
            });
        },
        onThumbsScroll(e) {
            if (this.isScrollingCarousel) return;
            this.isScrollingThumbs = true;
            const container = e.target;
            const center = container.scrollLeft + (container.clientWidth / 2);
            const items = container.querySelectorAll('.thumb-item');
            let closestIndex = 0;
            let minDistance = Infinity;
            items.forEach((item, index) => {
                const itemCenter = item.offsetLeft + (item.clientWidth / 2);
                const distance = Math.abs(center - itemCenter);
                if (distance < minDistance) { minDistance = distance; closestIndex = index; }
            });
            if (this.currentIndex !== closestIndex) {
                this.currentIndex = closestIndex;
                this.$refs.carousel.scrollTo({ left: this.$refs.carousel.clientWidth * closestIndex, behavior: 'auto' });
                if (this.showInfo) this.$nextTick(() => this.initMap());
            }
            clearTimeout(this.thumbTimeout);
            this.thumbTimeout = setTimeout(() => { this.isScrollingThumbs = false; }, 150);
        },
        onCarouselScroll(e) {
            if (this.isScrollingThumbs) return;
            this.isScrollingCarousel = true;
            const newIndex = Math.round(e.target.scrollLeft / e.target.clientWidth);
            if (this.currentIndex !== newIndex) {
                this.currentIndex = newIndex;
                this.scrollToThumb(newIndex);
                if (this.showInfo) this.$nextTick(() => this.initMap());
            }
            clearTimeout(this.carouselTimeout);
            this.carouselTimeout = setTimeout(() => { this.isScrollingCarousel = false; }, 150);
        },
        scrollToThumb(index) {
            const thumb = document.getElementById('thumb-' + index);
            if (thumb) {
                thumb.parentElement.scrollTo({
                    left: thumb.offsetLeft - (thumb.parentElement.clientWidth / 2) + (thumb.clientWidth / 2),
                    behavior: 'smooth'
                });
            }
        },
        like(postId) {
            $wire.toggleReaction(postId);
            this.showHeart = true;
            setTimeout(() => this.showHeart = false, 800);
        }
     }" x-init="setTimeout(() => {
        scrollToThumb(currentIndex);
        $refs.carousel.scrollTo({ left: $refs.carousel.clientWidth * currentIndex, behavior: 'auto' });
     }, 100)" @media-deleted.window="
        if (allMedia.length === 0) { window.location.href = '/'; return; }
        if (currentIndex >= allMedia.length) { currentIndex = Math.max(0, allMedia.length - 1); }
        $nextTick(() => { $refs.carousel.scrollTo({ left: $refs.carousel.clientWidth * currentIndex, behavior: 'auto' }); scrollToThumb(currentIndex); });
     " @touchstart="handleTouchStart($event)" @touchend="handleTouchEnd($event)">
    <nav class="p-4 md:p-6 flex items-center justify-between z-30">
        <button onclick="history.back()" class="w-10 h-10 rounded-full {{ $iconBg }} backdrop-blur-xl border {{ $colors['border'] }} flex items-center justify-center transition-transform active:scale-90">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <div class="flex flex-col items-center {{ $overlayBg }} backdrop-blur-md px-6 py-1.5 rounded-full min-w-[150px] cursor-pointer" @click="showInfo = true; $nextTick(() => initMap())">
            <template x-for="(media, index) in allMedia">
                <div x-show="currentIndex === index" class="flex flex-col items-center text-center">
                    <span class="text-[10px] md:text-xs font-bold leading-tight truncate max-w-[160px] md:max-w-[250px]" x-text="media.location || 'Captured Moment'"></span>
                    <span class="text-[9px] md:text-[10px] opacity-60 leading-tight" x-text="media.date + ' • ' + media.time"></span>
                </div>
            </template>
        </div>
        <div class="relative w-10 h-10">
            @foreach($allMedia as $index => $m)
                @if(Auth::check() && $m['user_id'] === Auth::id())
                    <button x-show="currentIndex === {{ $index }}" 
                            @click="$wire.archiveMedia({{ $m['id'] }})" 
                            class="absolute inset-0 rounded-full bg-red-500/10 backdrop-blur-xl border border-red-500/20 flex items-center justify-center text-red-500 transition-transform active:scale-90 z-40">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                @endif
            @endforeach
        </div>
    </nav>
    <main class="flex-1 relative flex flex-col justify-start pt-4 md:pt-10 overflow-hidden">
        <div class="relative w-full h-[65vh] md:h-[75vh] flex items-center overflow-x-auto snap-x snap-mandatory scrollbar-hide" @scroll="onCarouselScroll($event)" x-ref="carousel">
            @foreach($allMedia as $index => $m)
                <div class="flex-none w-full h-full snap-center flex items-center justify-center relative" @dblclick="like({{ $m['post_id'] }})">
                    @if(str_contains($m['file_type'], 'video'))
                        <video src="{{ $m['file_path'] }}" class="w-full h-full object-contain" {{ $index === $initialMediaIndex ? 'autoplay' : '' }} loop muted playsinline></video>
                    @else
                        <img src="{{ $m['file_path'] }}" draggable="false" class="w-full h-full object-contain">
                    @endif
                    <div x-show="showHeart && currentIndex === {{ $index }}" x-cloak x-transition:enter="transition-all ease-[cubic-bezier(0.175,0.885,0.32,1.275)] duration-500" x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-125" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 scale-125" x-transition:leave-end="opacity-0 scale-150" class="absolute inset-0 flex items-center justify-center pointer-events-none z-20">
                        <svg class="w-32 h-32 text-brand-500 fill-current filter drop-shadow-[0_0_30px_rgba(244,63,94,0.6)]" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                    </div>
                </div>
            @endforeach
        </div>
    </main>
    <footer class="pb-16 pt-4 overflow-hidden relative">
        <div class="absolute left-1/2 top-4 bottom-16 w-[1px] bg-white/20 -translate-x-1/2 z-20 pointer-events-none"></div>
        <div class="flex items-center gap-0 overflow-x-auto scrollbar-hide px-4" @scroll="onThumbsScroll($event)" x-ref="filmstrip">
            <div class="flex-none w-[48vw]"></div>
            @foreach($allMedia as $index => $m)
                <button @click="currentIndex = {{ $index }}; $refs.carousel.scrollTo({ left: $refs.carousel.clientWidth * {{ $index }}, behavior: 'smooth' }); scrollToThumb({{ $index }})" id="thumb-{{ $index }}" class="thumb-item flex-none w-10 h-14 md:w-12 md:h-16 rounded-none overflow-hidden transition-all duration-300" :class="currentIndex === {{ $index }} ? 'scale-125 z-10 opacity-100' : 'opacity-30 scale-90'">
                    <img src="{{ $m['file_path'] }}" draggable="false" class="w-full h-full object-cover">
                </button>
            @endforeach
            <div class="flex-none w-[48vw]"></div>
        </div>
    </footer>
    <div x-show="showInfo" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full" class="absolute inset-x-0 bottom-0 h-[45vh] theme-card backdrop-blur-3xl rounded-t-[2rem] z-[300] p-6 flex flex-col space-y-4 overflow-hidden">
        <div class="w-12 h-1 bg-white/10 rounded-full mx-auto mb-2" @click="showInfo = false"></div>
        <div class="flex-1 overflow-y-auto scrollbar-hide">
            <template x-for="(media, index) in allMedia">
                <div x-show="currentIndex === index" class="flex flex-col space-y-6 pb-10">
                    <div class="flex items-center justify-between px-2">
                        <p class="text-sm font-medium opacity-80 tracking-tight" x-text="media.date + ' • ' + media.time"></p>
                    </div>
                    <div x-show="media.lat && media.lon" class="space-y-4">
                        <div :id="'map-' + index" class="w-full h-44 rounded-[2rem] overflow-hidden border theme-border shadow-lg"></div>
                        <div class="flex items-center justify-between px-2">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full bg-current opacity-10 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                                <p class="text-sm font-bold leading-tight truncate max-w-[200px]" x-text="media.location"></p>
                            </div>
                        </div>
                    </div>
                    <div x-show="!media.lat || !media.lon" class="py-12 flex flex-col items-center justify-center text-center space-y-4 bg-current/5 rounded-[2rem]">
                        <svg class="w-10 h-10 opacity-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <div>
                            <p class="text-sm font-bold opacity-80" x-text="media.location || 'Moment captured'"></p>
                            <p class="text-[10px] opacity-40 uppercase tracking-widest mt-1">no GPS metadata found in photo</p>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .leaflet-container { background: #0f172a !important; border-radius: 2rem; }
        .custom-map-marker { filter: drop-shadow(0 12px 24px rgba(0, 0, 0, 0.6)); border: none !important; background: transparent !important; }
    </style>
</div>