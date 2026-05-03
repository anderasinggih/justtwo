<div x-data="{ 
    cols: window.innerWidth > 1024 ? 5 : (window.innerWidth > 768 ? 4 : 3), 
    isSelecting: @entangle('isSelecting'),
    selectedIds: @entangle('selectedMedia'),
    isDownloading: false,
    
    toggleSelect(id) {
        if (!this.isSelecting) return;
        if (this.selectedIds.includes(id)) {
            this.selectedIds = this.selectedIds.filter(i => i !== id);
        } else {
            this.selectedIds.push(id);
        }
    },
    async downloadSelected() {
        if (this.selectedIds.length === 0) return;
        this.isDownloading = true;
        try {
            const mediaItems = @js($groupedMedia->flatten());
            const selectedFiles = mediaItems.filter(m => this.selectedIds.includes(m.id));
            
            const filesToShare = await Promise.all(selectedFiles.map(async (m) => {
                const response = await fetch('/storage/' + m.file_path_original);
                const blob = await response.blob();
                return new File([blob], m.file_path_original.split('/').pop(), { type: blob.type });
            }));

            if (navigator.share && navigator.canShare({ files: filesToShare })) {
                await navigator.share({
                    files: filesToShare,
                    title: 'Shared Photos',
                    text: 'Photos from JustTwo'
                });
            } else {
                alert('device not supported.');
            }
        } catch (e) { console.error(e); }
        finally { this.isDownloading = false; this.isSelecting = false; this.selectedIds = []; }
    }
}" class="min-h-screen theme-bg select-none">

    {{-- Header --}}
    <header class="fixed top-0 left-0 right-0 z-50 bg-black/80 backdrop-blur-lg border-b border-white/5 h-16 flex items-center justify-between px-6">
        <div class="flex items-center gap-4">
            <template x-if="!isSelecting">
                <h1 class="text-xl font-bold lowercase tracking-tighter text-white">Library</h1>
            </template>
            <template x-if="isSelecting">
                <div class="flex items-center gap-4">
                    <button @click="isSelecting = false; selectedIds = []" class="text-xs font-bold text-white/50 lowercase">Cancel</button>
                    <span class="text-xs font-bold text-white lowercase"><span x-text="selectedIds.length"></span> selected</span>
                </div>
            </template>
        </div>

        <div class="flex items-center gap-4">
            <template x-if="!isSelecting">
                <button @click="isSelecting = true" class="text-xs font-bold theme-accent lowercase">Select</button>
            </template>
            
            <template x-if="isSelecting">
                <div class="flex items-center gap-3">
                    <button @click="downloadSelected" :disabled="isDownloading || selectedIds.length === 0" class="font-bold text-xs theme-accent disabled:opacity-30">
                        <span x-text="isDownloading ? 'Preparing...' : 'Save'"></span>
                    </button>
                    <button x-show="selectedIds.length > 0" wire:click="confirmDelete" class="font-bold text-xs text-red-500">
                        Delete
                    </button>
                </div>
            </template>
        </div>
    </header>

    {{-- Content Grid --}}
    <main class="w-full pb-32 pt-20">
        @forelse($groupedMedia as $monthYear => $mediaItems)
            @php [$year, $month] = explode('-', $monthYear); @endphp
            <section class="mb-2">
                <div class="px-4 py-2">
                    <h2 class="text-lg font-bold lowercase tracking-tight text-white">{{ $month }}</h2>
                    <p class="text-[9px] opacity-30 uppercase tracking-widest text-white">{{ $year }}</p>
                </div>

                <div class="gallery-grid gap-[1px]" :style="'--grid-cols: ' + cols">
                    @foreach($mediaItems as $media)
                        <div wire:key="media-{{ $media->id }}" 
                             @click="toggleSelect({{ $media->id }})"
                             class="gallery-item relative aspect-square overflow-hidden bg-white/5 group">
                            <img src="{{ Storage::disk('public')->url($media->file_path_original) }}" 
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            
                            <div x-show="selectedIds.includes({{ $media->id }})" class="absolute inset-0 bg-brand-500/20 flex items-center justify-center z-10">
                                <div class="w-6 h-6 bg-brand-500 rounded-full flex items-center justify-center shadow-lg border-2 border-white">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @empty
            <div class="flex flex-col items-center justify-center py-32 opacity-20">
                <svg class="w-12 h-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <p class="text-sm font-bold lowercase">no memories yet</p>
            </div>
        @endforelse
    </main>

    {{-- Internal Confirmation Modal --}}
    @if($showConfirmModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-6">
            <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" wire:click="cancelDelete"></div>
            <div class="relative w-full max-w-xs bg-zinc-900 border border-white/10 rounded-[32px] p-8 shadow-2xl text-center space-y-6 animate-in zoom-in-95 duration-200">
                <div class="space-y-2">
                    <h3 class="text-xl font-bold text-white lowercase">Delete Items</h3>
                    <p class="text-xs text-zinc-400 lowercase leading-relaxed">Move {{ count($selectedMedia) }} items to trash? They will be deleted forever in 30 days.</p>
                </div>
                <div class="flex flex-col gap-2">
                    <button wire:click="archiveSelected" class="w-full py-4 bg-white text-black rounded-2xl font-bold text-sm lowercase active:scale-95 transition-transform">Yes, Move to Trash</button>
                    <button wire:click="cancelDelete" class="w-full py-4 bg-zinc-800 text-white rounded-2xl font-bold text-sm lowercase active:scale-95 transition-transform">Cancel</button>
                </div>
            </div>
        </div>
    @endif

    <style>
        .gallery-grid { display: grid; grid-template-columns: repeat(var(--grid-cols), 1fr); }
        [x-cloak] { display: none !important; }
    </style>
</div>
