<div class="w-full pb-32 min-h-screen bg-black" 
     x-data="{ 
        cols: 3, 
        startDist: 0,
        levels: [1, 3, 5, 13],
        currentLevel: 1,
        zoomIn() {
            if (this.currentLevel > 0) {
                this.currentLevel--;
                this.cols = this.levels[this.currentLevel];
            }
        },
        zoomOut() {
            if (this.currentLevel < this.levels.length - 1) {
                this.currentLevel++;
                this.cols = this.levels[this.currentLevel];
            }
        },
        handleTouchStart(e) {
            if (e.touches.length === 2) {
                this.startDist = Math.hypot(
                    e.touches[0].pageX - e.touches[1].pageX,
                    e.touches[0].pageY - e.touches[1].pageY
                );
            }
        },
        handleTouchMove(e) {
            if (e.touches.length === 2 && this.startDist > 0) {
                let currentDist = Math.hypot(
                    e.touches[0].pageX - e.touches[1].pageX,
                    e.touches[0].pageY - e.touches[1].pageY
                );
                let scale = currentDist / this.startDist;
                
                if (scale > 1.3) { // Spread: Zoom IN
                    this.zoomIn();
                    this.startDist = currentDist;
                } else if (scale < 0.7) { // Pinch: Zoom OUT
                    this.zoomOut();
                    this.startDist = currentDist;
                }
            }
        }
     }"
     @touchstart="handleTouchStart($event)"
     @touchmove.prevent="handleTouchMove($event)">
    
    {{-- Header --}}
    <header class="sticky top-0 z-50 py-5 px-4 transition-all duration-500"
            :class="cols === 13 ? 'opacity-0 pointer-events-none' : 'bg-black/60 backdrop-blur-xl'">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold tracking-tight text-white">Library</h1>
            <div class="flex items-center gap-4">
                <button class="font-bold text-xs theme-accent">Select</button>
            </div>
        </div>
    </header>

    {{-- Content Grid --}}
    <main class="w-full transition-all duration-700 ease-[cubic-bezier(0.4,0,0.2,1)]"
          style="will-change: transform, opacity;">
        @forelse($groupedMedia as $monthYear => $mediaItems)
            @php
                [$year, $month] = explode('-', $monthYear);
            @endphp
            <section :class="cols === 13 ? 'mb-0' : 'mb-8'">
                <div class="px-4 py-4 transition-all duration-500" 
                     x-show="cols < 13"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform -translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    <h2 class="text-lg font-bold lowercase tracking-tight">{{ $month }}</h2>
                    <p class="text-[9px] opacity-30 uppercase tracking-widest">{{ $year }}</p>
                </div>

                <div class="grid gap-[1px] transition-all duration-500 ease-[cubic-bezier(0.4,0,0.2,1)]"
                     :style="'grid-template-columns: repeat(' + cols + ', minmax(0, 1fr))'">
                    @foreach($mediaItems as $media)
                        <a href="{{ route('gallery.preview', $media->id) }}" wire:navigate 
                           class="relative aspect-square overflow-hidden bg-white/5 transition-all duration-500">
                            <img src="{{ Storage::disk('public')->url($media->file_path_thumbnail ?? $media->file_path_original) }}" 
                                 class="w-full h-full object-cover transition-all duration-500"
                                 loading="lazy">
                            
                            @if(str_contains($media->file_type, 'video'))
                                <div class="absolute bottom-1 right-1 bg-black/40 backdrop-blur-md rounded px-0.5 py-0.5" x-show="cols < 5">
                                    <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>
            </section>
        @empty
            <div class="py-40 text-center space-y-4">
                <p class="text-sm opacity-30 lowercase italic">no photos yet.</p>
            </div>
        @endforelse

        {{-- Library Stats --}}
        <div class="py-12 text-center transition-opacity duration-500" :class="cols === 13 ? 'opacity-0' : 'opacity-100'">
            <p class="text-[10px] font-bold opacity-40 uppercase tracking-widest">
                {{ $groupedMedia->flatten()->count() }} items • updated just now
            </p>
        </div>
    </main>
</div>




