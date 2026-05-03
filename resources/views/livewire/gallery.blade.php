<div class="w-full pb-32 min-h-screen bg-black overflow-x-hidden text-white" 
     x-data="{ 
        cols: window.innerWidth > 1024 ? 5 : (window.innerWidth > 768 ? 4 : 3), 
        startDist: 0,
        levels: [1, 3, 5, 13],
        currentLevel: window.innerWidth > 1024 ? 2 : (window.innerWidth > 768 ? 2 : 1), 
        isZooming: false,
        zoomIn() {
            if (this.currentLevel > 0) {
                this.isZooming = true;
                this.currentLevel--;
                this.cols = this.levels[this.currentLevel];
                setTimeout(() => { this.isZooming = false }, 800);
            }
        },
        zoomOut() {
            if (this.currentLevel < this.levels.length - 1) {
                this.isZooming = true;
                this.currentLevel++;
                this.cols = this.levels[this.currentLevel];
                setTimeout(() => { this.isZooming = false }, 800);
            }
        }
     }"
     @touchstart="if($event.touches.length === 2) startDist = Math.hypot($event.touches[0].pageX - $event.touches[1].pageX, $event.touches[0].pageY - $event.touches[1].pageY)"
     @touchmove="if($event.touches.length === 2 && startDist > 0) {
        $event.preventDefault();
        let currentDist = Math.hypot($event.touches[0].pageX - $event.touches[1].pageX, $event.touches[0].pageY - $event.touches[1].pageY);
        let scale = currentDist / startDist;
        if (scale > 1.5) { zoomIn(); startDist = currentDist; }
        else if (scale < 0.6) { zoomOut(); startDist = currentDist; }
     }">

    <style>
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(var(--grid-cols), 1fr);
            transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: grid-template-columns, gap, padding;
        }
        .gallery-item {
            transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: transform, opacity, width, height;
        }
    </style>
    
    {{-- Header --}}
    <header class="sticky top-0 z-50 py-5 px-4 transition-all duration-700"
            :class="cols === 13 ? 'opacity-0 pointer-events-none' : 'bg-black/60 backdrop-blur-xl border-b border-white/5'">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold tracking-tight text-white">Library</h1>
            <div class="flex items-center gap-4">
                <button wire:click="toggleSelection" class="font-bold text-xs theme-accent">
                    {{ $isSelecting ? 'Cancel' : 'Select' }}
                </button>
            </div>
        </div>
    </header>

    {{-- Content Grid --}}
    <main class="w-full">
        @forelse($groupedMedia as $monthYear => $mediaItems)
            @php
                [$year, $month] = explode('-', $monthYear);
            @endphp
            <section :class="cols === 13 ? 'mb-0' : 'mb-2'" class="transition-all duration-700">
                <div class="px-4 transition-all duration-700 overflow-hidden" 
                     :class="cols === 13 ? 'opacity-0 h-0 py-0' : 'opacity-100 py-2'">
                    <h2 class="text-lg font-bold lowercase tracking-tight">{{ $month }}</h2>
                    <p class="text-[9px] opacity-30 uppercase tracking-widest">{{ $year }}</p>
                </div>

                <div class="gallery-grid"
                     :class="cols === 13 ? 'gap-0' : 'gap-[1px]'"
                     :style="'--grid-cols: ' + cols">
                    @foreach($mediaItems as $media)
                        <div class="gallery-item relative aspect-square overflow-hidden bg-white/5 group cursor-pointer">
                            @if($isSelecting)
                                <div wire:click="selectMedia({{ $media->id }})" 
                                     class="absolute inset-0 z-20 transition-all flex items-center justify-center {{ in_array($media->id, $selectedMedia) ? 'bg-brand-500/10' : '' }}"
                                     :class="cols < 5 ? 'p-2' : 'p-0.5'">
                                    <div class="relative w-6 h-6 rounded-full border-2 transition-all flex items-center justify-center {{ in_array($media->id, $selectedMedia) ? 'bg-brand-500 border-brand-500 text-white' : 'border-white/30 bg-black/20' }}">
                                        @if(in_array($media->id, $selectedMedia))
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <a href="{{ route('gallery.preview', $media->id) }}" wire:navigate class="absolute inset-0 z-10"></a>
                            @endif

                            <img src="{{ Storage::disk('public')->url($media->file_path_thumbnail ?? $media->file_path_original) }}" 
                                 class="w-full h-full object-cover transition-all duration-700 {{ $isSelecting && in_array($media->id, $selectedMedia) ? 'scale-75 rounded-2xl' : 'scale-100' }}"
                                 loading="lazy">
                            
                            @if(str_contains($media->file_type, 'video'))
                                <div class="absolute bottom-1.5 right-1.5 bg-black/40 backdrop-blur-md rounded p-0.5 z-0" x-show="cols < 5">
                                    <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @empty
            <div class="py-40 text-center space-y-4">
                <p class="text-sm opacity-30 lowercase italic">no photos yet.</p>
            </div>
        @endforelse

        {{-- Library Stats --}}
        <div class="py-12 text-center transition-opacity duration-700" :class="cols === 13 ? 'opacity-0' : 'opacity-100'">
            <p class="text-[10px] font-bold opacity-40 uppercase tracking-widest">
                {{ $groupedMedia->flatten()->count() }} items • updated just now
            </p>
        </div>
    </main>

    {{-- Selection Action Bar --}}
    @if($isSelecting && count($selectedMedia) > 0)
        <div class="fixed bottom-24 inset-x-4 z-[100] flex justify-center animate-in slide-in-from-bottom-10 duration-300">
            <button wire:click="archiveSelected" wire:confirm="move {{ count($selectedMedia) }} items to archive?" 
                    class="flex items-center gap-3 bg-red-500 text-white px-8 py-4 rounded-full shadow-[0_20px_50px_rgba(239,68,68,0.4)] font-bold text-sm active:scale-95 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Archive {{ count($selectedMedia) }} Items
            </button>
        </div>
    @endif
</div>
