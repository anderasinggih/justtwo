<div class="w-full pb-32 min-h-screen bg-black overflow-x-hidden text-white" 
     x-data="{ 
        cols: window.innerWidth > 1024 ? 5 : (window.innerWidth > 768 ? 4 : 3), 
        levels: [1, 3, 5, 13],
        currentLevel: window.innerWidth > 1024 ? 2 : (window.innerWidth > 768 ? 2 : 1), 
        
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
            touch-action: none;
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
            <div class="flex items-center gap-3">
                <button x-show="isSelecting && selectedIds.length > 0"
                        @click="$dispatch('confirm', { 
                            title: 'Delete Items', 
                            message: 'Move ' + selectedIds.length + ' items to trash? They will be deleted forever in 30 days.', 
                            onConfirm: () => archive() 
                        })" 
                        class="font-bold text-xs text-red-500 animate-in fade-in slide-in-from-right-2 duration-200">
                    Delete (<span x-text="selectedIds.length"></span>)
                </button>
                <button @click="isSelecting = !isSelecting; selectedIds = []" 
                        class="font-bold text-xs theme-accent" 
                        x-text="isSelecting ? 'Cancel' : 'Select'"></button>
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
                                let item = el?.closest('.gallery-item');
                                if (item) {
                                    let id = parseInt(item.getAttribute('data-id'));
                                    if (id) handleDragOver(id);
                                }
                             "
                             data-id="{{ $media->id }}">
                            
                            {{-- Selection Overlay --}}
                            <template x-if="isSelecting">
                                <div @click="toggleSelect({{ $media->id }})" 
                                     class="absolute inset-0 z-30 transition-colors duration-150"
                                     :class="selectedIds.includes({{ $media->id }}) ? 'bg-brand-500/20' : 'bg-transparent'">
                                    
                                    {{-- Icon in Bottom Left --}}
                                    <div class="absolute bottom-1.5 left-1.5 w-5 h-5 rounded-full border-2 transition-all duration-150 flex items-center justify-center"
                                         :class="selectedIds.includes({{ $media->id }}) ? 'bg-brand-500 border-brand-500 text-white' : 'border-white/30 bg-black/20'">
                                        <template x-if="selectedIds.includes({{ $media->id }})">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <template x-if="!isSelecting">
                                <a href="{{ route('gallery.preview', $media->id) }}" wire:navigate class="absolute inset-0 z-10"></a>
                            </template>

                            <img src="{{ Storage::disk('public')->url($media->file_path_thumbnail ?? $media->file_path_original) }}" 
                                 class="w-full h-full object-cover pointer-events-none"
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
        <div class="py-12 text-center" x-show="cols !== 13">
            <p class="text-[10px] font-bold opacity-40 uppercase tracking-widest text-white">
                {{ $groupedMedia->flatten()->count() }} items • updated just now
            </p>
        </div>
    </main>
</div>
