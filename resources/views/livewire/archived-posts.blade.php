<div class="max-w-xl mx-auto pb-20">
    {{-- Header --}}
    <header class="flex items-center gap-4 px-4 h-14 sticky top-0 theme-bg z-30 border-b theme-border">
        <a href="{{ route('app-settings') }}" wire:navigate class="theme-text opacity-70 hover:opacity-100 transition-opacity">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h1 class="text-lg font-bold theme-text lowercase tracking-tighter">archive</h1>
    </header>

    <div class="p-4">
        <div class="grid grid-cols-3 gap-1 md:gap-2">
            @forelse($posts as $post)
                <div class="aspect-square relative group bg-white/5 rounded-lg overflow-hidden border theme-border" x-data="{ open: false }">
                    {{-- Media --}}
                    @if($post->media->isNotEmpty())
                        <img src="{{ Storage::disk('public')->url($post->media->first()->file_path_thumbnail ?? $post->media->first()->file_path_original) }}" 
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center p-2">
                            <p class="text-[8px] theme-text opacity-30 text-center line-clamp-3 italic lowercase">{{ $post->content }}</p>
                        </div>
                    @endif

                    {{-- Actions Overlay --}}
                    <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-2">
                        <button wire:click="restorePost({{ $post->id }})" class="p-2 bg-white/20 backdrop-blur-md rounded-full text-white hover:bg-white/40 transition-all shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        </button>
                        <button wire:click="deletePost({{ $post->id }})" 
                                wire:confirm="permanently delete this memory?"
                                class="p-2 bg-red-500/20 backdrop-blur-md rounded-full text-red-500 hover:bg-red-500/40 transition-all shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>

                    {{-- Info Badge --}}
                    <div class="absolute top-1 right-1 px-1 py-0.5 bg-black/40 rounded text-[7px] text-white/70 uppercase">
                        {{ $post->created_at->format('M y') }}
                    </div>
                </div>
            @empty
                <div class="col-span-full py-32 text-center space-y-4">
                    <div class="w-16 h-16 bg-white/5 rounded-full flex items-center justify-center mx-auto theme-text opacity-10">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    </div>
                    <p class="text-[10px] theme-text opacity-30 lowercase italic tracking-widest">your archive is empty.</p>
                </div>
            @endforelse
        </div>

        @if($posts->hasPages())
            <div class="mt-8">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</div>
