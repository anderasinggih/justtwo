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

<div class="fixed inset-0 {{ $colors['bg'] }} {{ $colors['text'] }} z-[200] flex flex-col overflow-hidden select-none"
     x-data="{ 
        currentIndex: {{ $initialMediaIndex }},
        total: {{ count($allMedia) }},
        showHeart: false,
        isScrollingCarousel: false,
        isScrollingThumbs: false,

        // Update carousel when thumb is scrolled to center
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
                if (distance < minDistance) {
                    minDistance = distance;
                    closestIndex = index;
                }
            });
            
            if (this.currentIndex !== closestIndex) {
                this.currentIndex = closestIndex;
                this.$refs.carousel.scrollTo({ 
                    left: this.$refs.carousel.clientWidth * closestIndex, 
                    behavior: 'auto' 
                });
            }
            
            clearTimeout(this.thumbTimeout);
            this.thumbTimeout = setTimeout(() => { this.isScrollingThumbs = false; }, 150);
        },

        // Update thumb position when carousel is swiped
        onCarouselScroll(e) {
            if (this.isScrollingThumbs) return;
            this.isScrollingCarousel = true;
            
            const newIndex = Math.round(e.target.scrollLeft / e.target.clientWidth);
            if (this.currentIndex !== newIndex) {
                this.currentIndex = newIndex;
                this.scrollToThumb(newIndex);
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
     }"
     x-init="setTimeout(() => {
        scrollToThumb(currentIndex);
        $refs.carousel.scrollTo({ left: $refs.carousel.clientWidth * currentIndex, behavior: 'auto' });
     }, 100)">

    {{-- iOS Minimal Top Bar --}}
    <nav class="p-4 md:p-6 flex items-center justify-between z-30">
        <button onclick="history.back()" class="w-10 h-10 rounded-full {{ $iconBg }} backdrop-blur-xl border {{ $colors['border'] }} flex items-center justify-center transition-transform active:scale-90">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>

        <div class="flex flex-col items-center {{ $overlayBg }} backdrop-blur-md px-6 py-1.5 rounded-full min-w-[150px]">
            <template x-for="(media, index) in @js($allMedia)">
                <div x-show="currentIndex === index" class="flex flex-col items-center text-center">
                    <span class="text-[10px] md:text-xs font-bold leading-tight" x-text="media.location || 'Captured Moment'"></span>
                    <span class="text-[9px] md:text-[10px] opacity-60 leading-tight" x-text="media.date + ' • ' + media.time"></span>
                </div>
            </template>
        </div>

        <div class="relative w-10 h-10">
            @foreach($allMedia as $index => $m)
                @if(Auth::check() && $m['user_id'] === Auth::id())
                    <button x-show="currentIndex === {{ $index }}" 
                            wire:click="deletePost({{ $m['post_id'] }})"
                            wire:confirm="are you sure you want to delete this entire memory?"
                            class="absolute inset-0 rounded-full bg-red-500/10 backdrop-blur-xl border border-red-500/20 flex items-center justify-center text-red-500 transition-transform active:scale-90 z-40">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                @endif
            @endforeach
        </div>
    </nav>

    {{-- Main Carousel Section (Higher) --}}
    <main class="flex-1 relative flex flex-col justify-start pt-4 md:pt-10 overflow-hidden">
        <div class="relative w-full h-[65vh] md:h-[75vh] flex items-center overflow-x-auto snap-x snap-mandatory scrollbar-hide"
             @scroll="onCarouselScroll($event)"
             x-ref="carousel">
            
            @foreach($allMedia as $index => $m)
                <div class="flex-none w-full h-full snap-center flex items-center justify-center p-4 relative" 
                     @dblclick="like({{ $m['post_id'] }})">
                    @if(str_contains($m['file_type'], 'video'))
                        <video src="{{ $m['file_path'] }}" 
                               class="max-w-full max-h-full object-contain rounded-xl shadow-2xl" 
                               {{ $index === $initialMediaIndex ? 'autoplay' : '' }} loop muted playsinline></video>
                    @else
                        <img src="{{ $m['file_path'] }}" 
                             draggable="false"
                             class="max-w-full max-h-full object-contain shadow-2xl rounded-xl">
                    @endif

                    {{-- Big Heart Animation --}}
                    <div x-show="showHeart && currentIndex === {{ $index }}" x-cloak 
                         x-transition:enter="transition-all ease-[cubic-bezier(0.175,0.885,0.32,1.275)] duration-500"
                         x-transition:enter-start="opacity-0 scale-50"
                         x-transition:enter-end="opacity-100 scale-125"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 scale-125"
                         x-transition:leave-end="opacity-0 scale-150"
                         class="absolute inset-0 flex items-center justify-center pointer-events-none z-20">
                        <svg class="w-32 h-32 text-brand-500 fill-current filter drop-shadow-[0_0_30px_rgba(244,63,94,0.6)]" viewBox="0 0 24 24">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        </svg>
                    </div>
                </div>
            @endforeach
        </div>
    </main>

    {{-- Interactive Filmstrip Scrubber (Gapless & No Border) --}}
    <footer class="pb-16 pt-4 overflow-hidden relative">
        {{-- Selection Guide (Subtle) --}}
        <div class="absolute left-1/2 top-4 bottom-16 w-[1px] bg-white/20 -translate-x-1/2 z-20 pointer-events-none"></div>

        <div class="flex items-center gap-0 overflow-x-auto scrollbar-hide px-4" 
             @scroll="onThumbsScroll($event)"
             x-ref="filmstrip">
            <div class="flex-none w-[48vw]"></div> {{-- Perfect Center Spacer --}}
            
            @foreach($allMedia as $index => $m)
                <button @click="currentIndex = {{ $index }}; $refs.carousel.scrollTo({ left: $refs.carousel.clientWidth * {{ $index }}, behavior: 'smooth' }); scrollToThumb({{ $index }})"
                        id="thumb-{{ $index }}"
                        class="thumb-item flex-none w-10 h-14 md:w-12 md:h-16 rounded-none overflow-hidden transition-all duration-300"
                        :class="currentIndex === {{ $index }} ? 'scale-125 z-10 opacity-100' : 'opacity-30 scale-90'">
                    <img src="{{ $m['file_path'] }}" draggable="false" class="w-full h-full object-cover">
                </button>
            @endforeach
            
            <div class="flex-none w-[48vw]"></div> {{-- Perfect Center Spacer --}}
        </div>
    </footer>

    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</div>
