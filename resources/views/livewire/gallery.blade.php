<div class="w-full pb-32 min-h-screen bg-black overflow-x-hidden text-white" 
     x-data="{ 
        cols: window.innerWidth > 1024 ? 5 : (window.innerWidth > 768 ? 4 : 3), 
        levels: [1, 3, 5, 13],
        currentLevel: window.innerWidth > 1024 ? 2 : (window.innerWidth > 768 ? 2 : 1), 
        
        {{-- Selection State --}}
        isSelecting: @entangle('isSelecting'),
        selectedIds: [],
        isDragging: false,
        lastDraggedId: null,

        toggleSelect(id) {
            if (!this.isSelecting) return;
            if (this.selectedIds.includes(id)) {
                this.selectedIds = this.selectedIds.filter(i => i !== id);
            } else {
                this.selectedIds.push(id);
            }
        },

        handleDragStart(id) {
            if (!this.isSelecting) return;
            this.isDragging = true;
            this.toggleSelect(id);
            this.lastDraggedId = id;
        },

        handleDragOver(id) {
            if (!this.isDragging || !this.isSelecting || this.lastDraggedId === id) return;
            this.toggleSelect(id);
            this.lastDraggedId = id;
        },

        handleDragEnd() {
            this.isDragging = false;
            this.lastDraggedId = null;
        },

        archive() {
            if (this.selectedIds.length === 0) return;
            $wire.set('selectedMedia', this.selectedIds);
            $wire.archiveSelected();
            this.selectedIds = [];
        }
     }"
     @mouseup.window="handleDragEnd()"
     @touchend.window="handleDragEnd()">

    <style>
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(var(--grid-cols), 1fr);
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .gallery-item {
            user-select: none;
            -webkit-user-drag: none;
        }
    </style>
    
    {{-- Header --}}
    <header class="sticky top-0 z-50 py-5 px-4 transition-all duration-300"
            x-show="cols !== 13"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="bg-black/60 backdrop-blur-xl border-b border-white/5">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold tracking-tight text-white">Library</h1>
            <div class="flex items-center gap-4">
                <button @click="isSelecting = !isSelecting; selectedIds = []" class="font-bold text-xs theme-accent" x-text="isSelecting ? 'Cancel' : 'Select'"></button>
            </div>
        </div>
    </header>

    {{-- Content Grid --}}
    <main class="w-full">
        @forelse($groupedMedia as $monthYear => $mediaItems)
            @php
                [$year, $month] = explode('-', $monthYear);
            @endphp
            <section :class="cols === 13 ? 'mb-0' : 'mb-2'">
                <div class="px-4 py-2" x-show="cols !== 13">
                    <h2 class="text-lg font-bold lowercase tracking-tight text-white">{{ $month }}</h2>
                    <p class="text-[9px] opacity-30 uppercase tracking-widest text-white">{{ $year }}</p>
                </div>

                <div class="gallery-grid"
                     :class="cols === 13 ? 'gap-0' : 'gap-[1px]'"
                     :style="'--grid-cols: ' + cols">
                    @foreach($mediaItems as $media)
                        <div class="gallery-item relative aspect-square overflow-hidden bg-white/5 group"
                             @mousedown="handleDragStart({{ $media->id }})"
                             @mouseenter="handleDragOver({{ $media->id }})"
                             @touchstart.passive="handleDragStart({{ $media->id }})"
                             @touchmove.passive="
                                let touch = $event.touches[0];
                                let el = document.elementFromPoint(touch.clientX, touch.clientY);
                                let id = el?.closest('.gallery-item')?.getAttribute('data-id');
                                if (id) handleDragOver(parseInt(id));
                             "
                             data-id="{{ $media->id }}">
                            
                            {{-- Selection Overlay (Simple & Fast) --}}
                            <div x-show="isSelecting" 
                                 class="absolute inset-0 z-20 transition-colors duration-150 flex items-center justify-center"
                                 :class="selectedIds.includes({{ $media->id }}) ? 'bg-brand-500/20' : 'bg-transparent'">
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all duration-150"
                                     :class="selectedIds.includes({{ $media->id }}) ? 'bg-brand-500 border-brand-500 text-white' : 'border-white/30 bg-black/10'">
                                    <template x-if="selectedIds.includes({{ $media->id }})">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </template>
                                </div>
                            </div>

                            <a x-show="!isSelecting" href="{{ route('gallery.preview', $media->id) }}" wire:navigate class="absolute inset-0 z-10"></a>

                            <img src="{{ Storage::disk('public')->url($media->file_path_thumbnail ?? $media->file_path_original) }}" 
                                 class="w-full h-full object-cover"
                                 loading="lazy"
                                 draggable="false">
                            
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
        <div class="py-12 text-center" x-show="cols !== 13">
            <p class="text-[10px] font-bold opacity-40 uppercase tracking-widest text-white">
                {{ $groupedMedia->flatten()->count() }} items • updated just now
            </p>
        </div>
    </main>

    {{-- Selection Action Bar --}}
    <div x-show="isSelecting && selectedIds.length > 0" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-20 opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-20 opacity-0"
         class="fixed bottom-24 inset-x-4 z-[100] flex justify-center">
        <button @click="if(confirm('Archive ' + selectedIds.length + ' items?')) archive()" 
                class="flex items-center gap-3 bg-red-500 text-white px-8 py-4 rounded-full shadow-[0_20px_50px_rgba(239,68,68,0.4)] font-bold text-sm active:scale-95 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            Archive <span x-text="selectedIds.length"></span> Items
        </button>
    </div>
</div>
