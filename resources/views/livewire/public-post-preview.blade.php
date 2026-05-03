<div class="fixed inset-0 z-50 bg-black flex flex-col h-[100dvh]"
     x-data="{ 
        currentIndex: @entangle('currentIndex'),
        allMedia: @js($allMedia),
        showConfirm: false,
        next() {
            if (this.currentIndex < this.allMedia.length - 1) this.currentIndex++;
        },
        prev() {
            if (this.currentIndex > 0) this.currentIndex--;
        },
        archive() {
            let mediaId = this.allMedia[this.currentIndex].id;
            $wire.archiveMedia(mediaId);
            this.showConfirm = false;
        }
     }"
     @keydown.right.window="next()"
     @keydown.left.window="prev()"
     @keydown.escape.window="window.history.back()">
     
    {{-- Header --}}
    <header class="p-6 flex items-center justify-between z-50">
        <button @click="window.history.back()" class="p-2 text-white/50 hover:text-white transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        
        {{-- Progress Indicator --}}
        <div class="flex gap-1.5 px-3 py-1.5 bg-white/5 backdrop-blur-md rounded-full border border-white/10">
            @foreach($allMedia as $index => $m)
                <div class="w-1 h-1 rounded-full transition-all duration-500"
                     :class="currentIndex === {{ $index }} ? 'bg-white scale-125' : 'bg-white/20'"></div>
            @endforeach
        </div>

        <div class="w-10"></div>
    </header>

    {{-- Main Content --}}
    <div class="relative flex-1 flex items-center justify-center overflow-hidden">
        {{-- Navigation Areas --}}
        <div @click="prev()" class="absolute left-0 top-0 bottom-0 w-1/3 z-20 cursor-w-resize"></div>
        <div @click="next()" class="absolute right-0 top-0 bottom-0 w-1/3 z-20 cursor-e-resize"></div>

        {{-- Media Container --}}
        <div class="relative w-full h-full flex items-center justify-center p-4">
            @foreach($allMedia as $index => $m)
                <template x-if="currentIndex === {{ $index }}">
                    <div class="w-full h-full flex items-center justify-center animate-in fade-in zoom-in-95 duration-500">
                        @if(str_contains($m['file_type'], 'video'))
                            <video src="{{ Storage::disk('public')->url($m['file_path_original']) }}" 
                                   class="max-w-full max-h-full object-contain rounded-2xl shadow-2xl"
                                   controls 
                                   autoplay 
                                   loop
                                   playsinline></video>
                        @else
                            <img src="{{ Storage::disk('public')->url($m['file_path_original']) }}" 
                                 class="max-w-full max-h-full object-contain rounded-2xl shadow-2xl">
                        @endif
                    </div>
                </template>
            @endforeach
        </div>
    </div>

    {{-- Footer Controls --}}
    <div class="p-8 pb-12 flex flex-col items-center gap-8">
        {{-- Location & Date --}}
        <div class="text-center space-y-1">
            @foreach($allMedia as $index => $m)
                <template x-if="currentIndex === {{ $index }}">
                    <div class="animate-in fade-in slide-in-from-bottom-2 duration-500">
                        @if($m['location_name'])
                            <p class="text-sm font-bold text-white lowercase tracking-tight">{{ $m['location_name'] }}</p>
                        @endif
                        <p class="text-[10px] text-white/40 uppercase tracking-widest font-bold">
                            {{ \Carbon\Carbon::parse($m['captured_at'] ?? $m['created_at'])->format('M d, Y') }}
                        </p>
                    </div>
                </template>
            @endforeach
        </div>

        {{-- Delete Button --}}
        <div class="relative w-10 h-10">
            @foreach($allMedia as $index => $m)
                @if(Auth::check() && $m['user_id'] === Auth::id())
                    <button x-show="currentIndex === {{ $index }}" 
                            @click="showConfirm = true" 
                            class="absolute inset-0 rounded-full bg-red-500/10 backdrop-blur-xl border border-red-500/20 flex items-center justify-center text-red-500 transition-transform active:scale-90 z-40">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                @endif
            @endforeach
        </div>
    </div>

    {{-- Premium Confirmation Modal --}}
    <template x-teleport="body">
        <div x-show="showConfirm" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[200] flex items-center justify-center px-6 bg-black/80 backdrop-blur-md"
             x-cloak>
            <div @click.away="showConfirm = false" class="bg-zinc-900 border border-white/10 rounded-[2.5rem] p-8 w-full max-w-sm text-center shadow-2xl animate-in zoom-in-95 duration-300">
                <div class="w-16 h-16 bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Delete photo?</h3>
                <p class="text-sm text-white/50 mb-8 lowercase">this photo will be moved to deleted items and permanently removed after 30 days.</p>
                
                <div class="flex flex-col gap-3">
                    <button @click="archive()" class="w-full py-4 bg-red-500 text-white rounded-2xl font-bold text-sm active:scale-95 transition-all">
                        Delete
                    </button>
                    <button @click="showConfirm = false" class="w-full py-4 bg-white/5 text-white/70 rounded-2xl font-bold text-sm active:scale-95 transition-all">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>