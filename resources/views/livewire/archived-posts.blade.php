<div class="w-full pb-32 min-h-screen bg-black overflow-x-hidden text-white" 
     x-data="{ 
        cols: window.innerWidth > 1024 ? 5 : (window.innerWidth > 768 ? 4 : 3), 
        isSelecting: @entangle('isSelecting'),
        selectedIds: @entangle('selectedMedia'),
        isDragging: false,
        lastDraggedId: null,
        showConfirm: false,

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

        deleteForever() {
            $wire.deleteSelectedPermanently();
            this.showConfirm = false;
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
    <header class="sticky top-0 z-50 py-5 px-4 bg-black/60 backdrop-blur-xl border-b border-white/5">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('app-settings') }}" wire:navigate class="theme-text opacity-70 hover:opacity-100 transition-opacity">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <h1 class="text-xl font-bold tracking-tight text-white">Deleted Items</h1>
            </div>
            
            <div class="flex items-center gap-3">
                <template x-if="isSelecting && selectedIds.length > 0">
                    <div class="flex items-center gap-3 animate-in fade-in slide-in-from-right-2 duration-200">
                        <button wire:click="restoreSelected" class="font-bold text-xs theme-accent">
                            Restore
                        </button>
                        <button @click="showConfirm = true" class="font-bold text-xs text-red-500">
                            Delete Forever
                        </button>
                    </div>
                </template>
                <button @click="isSelecting = !isSelecting; selectedIds = []" 
                        class="font-bold text-xs opacity-50" 
                        x-text="isSelecting ? 'Cancel' : 'Select'"></button>
            </div>
        </div>
    </header>

    {{-- Content Grid --}}
    <main class="w-full">
        <div class="px-4 py-3 bg-white/5 mb-2">
            <p class="text-[10px] theme-text opacity-40 uppercase tracking-widest font-bold">
                Items will be permanently deleted after 30 days
            </p>
        </div>

        @forelse($groupedMedia as $monthYear => $mediaItems)
            @php
                [$year, $month] = explode('-', $monthYear);
            @endphp
            <section class="mb-2">
                <div class="px-4 py-2">
                    <h2 class="text-lg font-bold lowercase tracking-tight text-white">{{ $month }}</h2>
                    <p class="text-[9px] opacity-30 uppercase tracking-widest text-white">{{ $year }}</p>
                </div>

                <div class="gallery-grid gap-[1px]" :style="'--grid-cols: ' + cols">
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
                            <div x-show="isSelecting" 
                                 @click="toggleSelect({{ $media->id }})" 
                                 class="absolute inset-0 z-30 transition-colors duration-150"
                                 :class="selectedIds.includes({{ $media->id }}) ? 'bg-brand-500/20' : 'bg-transparent'">
                                
                                <div class="absolute bottom-1.5 left-1.5 w-5 h-5 rounded-full border-2 transition-all duration-150 flex items-center justify-center"
                                     :class="selectedIds.includes({{ $media->id }}) ? 'bg-brand-500 border-brand-500 text-white' : 'border-white/30 bg-black/20'">
                                    <template x-if="selectedIds.includes({{ $media->id }})">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </template>
                                </div>
                            </div>

                            <img src="{{ Storage::disk('public')->url($media->file_path_thumbnail ?? $media->file_path_original) }}" 
                                 class="w-full h-full object-cover pointer-events-none opacity-60 grayscale-[0.5]"
                                 loading="lazy">
                            
                            {{-- Days Left Badge --}}
                            <div class="absolute top-1 right-1 px-1 py-0.5 bg-black/60 backdrop-blur-md rounded text-[7px] text-white/70 font-bold uppercase z-10">
                                @php 
                                    $archivedAt = \Carbon\Carbon::parse($media->archived_at ?? $media->created_at);
                                    $daysLeft = 30 - now()->diffInDays($archivedAt);
                                @endphp
                                {{ (int) max(0, $daysLeft) }}d left
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @empty
            <div class="py-40 text-center space-y-4">
                <div class="w-16 h-16 bg-white/5 rounded-full flex items-center justify-center mx-auto theme-text opacity-10">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </div>
                <p class="text-sm opacity-30 lowercase italic">trash is empty.</p>
            </div>
        @endforelse
    </main>

    {{-- Premium Confirmation Modal --}}
    <div x-show="showConfirm" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[100] flex items-center justify-center px-6 bg-black/80 backdrop-blur-md"
         x-cloak>
        <div class="bg-zinc-900 border border-white/10 rounded-[2.5rem] p-8 w-full max-w-sm text-center shadow-2xl animate-in zoom-in-95 duration-300">
            <div class="w-16 h-16 bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Delete forever?</h3>
            <p class="text-sm text-white/50 mb-8 lowercase">this action cannot be undone. selected items will be permanently removed.</p>
            
            <div class="flex flex-col gap-3">
                <button @click="deleteForever()" class="w-full py-4 bg-red-500 text-white rounded-2xl font-bold text-sm active:scale-95 transition-all">
                    Delete Permanently
                </button>
                <button @click="showConfirm = false" class="w-full py-4 bg-white/5 text-white/70 rounded-2xl font-bold text-sm active:scale-95 transition-all">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
