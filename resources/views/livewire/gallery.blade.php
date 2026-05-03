<div class="w-full pb-32 min-h-screen theme-bg overflow-x-hidden" wire:poll.60s>
    {{-- Header --}}
    <header class="sticky top-0 z-50 py-6 px-6 bg-current/[0.02] backdrop-blur-xl border-b theme-border">
        <div class="flex items-center justify-between max-w-5xl mx-auto">
            <div>
                <h2 class="text-[10px] font-bold opacity-30 uppercase tracking-widest leading-none mb-1 theme-text">all memories</h2>
                <h1 class="text-2xl font-bold tracking-tighter theme-text lowercase">Gallery</h1>
            </div>
            <div class="flex items-center gap-3">
                <button class="p-2.5 rounded-full bg-current/5 theme-text hover:bg-current/10 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                </button>
            </div>
        </div>
    </header>

    {{-- Content --}}
    <main class="max-w-5xl mx-auto px-4 mt-8">
        @forelse($groupedMedia as $monthYear => $mediaItems)
            @php
                [$year, $month] = explode('-', $monthYear);
            @endphp
            <section class="mb-12 relative">
                {{-- Side Timeline Marker --}}
                <div class="sticky top-24 float-left -ml-2 mb-4">
                    <div class="flex flex-col border-l-2 theme-accent-border pl-4">
                        <h2 class="text-xl font-bold theme-text lowercase tracking-tight">{{ $month }}</h2>
                        <p class="text-[10px] font-bold opacity-20 uppercase tracking-[0.2em] theme-text">{{ $year }}</p>
                    </div>
                </div>

                <div class="clear-both"></div>

                {{-- Masonry Grid --}}
                <div class="columns-2 sm:columns-3 lg:columns-4 gap-4 space-y-4 pt-4">
                    @foreach($mediaItems as $media)
                        <div class="break-inside-avoid relative group">
                            <a href="{{ route('gallery.preview', $media->id) }}" wire:navigate 
                               class="block relative overflow-hidden rounded-2xl border theme-border shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-500 bg-white/5">
                                
                                <img src="{{ Storage::disk('public')->url($media->file_path_original) }}" 
                                     class="w-full h-auto object-cover transform group-hover:scale-105 transition-transform duration-700"
                                     loading="lazy">
                                
                                {{-- Overlay info (hidden until hover on desktop, subtle on mobile) --}}
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-4">
                                    <p class="text-[10px] text-white/70 font-medium tracking-wide">
                                        {{ $media->created_at->format('M d, Y') }}
                                    </p>
                                </div>

                                @if(str_contains($media->file_type, 'video'))
                                    <div class="absolute top-3 right-3 bg-black/40 backdrop-blur-md rounded-full p-2 text-white">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    </div>
                                @endif
                            </a>
                        </div>
                    @endforeach
                </div>
            </section>
        @empty
            <div class="py-40 text-center space-y-4">
                <div class="w-20 h-20 mx-auto rounded-full bg-current/5 flex items-center justify-center opacity-20">
                    <svg class="w-10 h-10 theme-text" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <p class="text-sm opacity-30 lowercase italic">no memories captured yet.</p>
            </div>
        @endforelse

        {{-- Library Stats --}}
        <div class="py-20 text-center">
            <div class="inline-block px-4 py-2 rounded-full bg-current/5 border theme-border">
                <p class="text-[10px] font-bold opacity-40 uppercase tracking-widest theme-text">
                    {{ $groupedMedia->flatten()->count() }} memories preserved forever
                </p>
            </div>
        </div>
    </main>
</div>
