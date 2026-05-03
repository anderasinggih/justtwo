<div class="w-full pb-32">
    {{-- Floating Header with Fade --}}
    <header class="fixed top-0 inset-x-0 z-50 pt-12 pb-24 px-6 pointer-events-none">
        <div class="absolute inset-0 bg-gradient-to-b from-black/90 via-black/40 to-transparent backdrop-blur-sm"></div>
        
        <div class="relative flex items-end justify-between pointer-events-auto">
            <div>
                <h1 class="text-4xl font-bold tracking-tight text-white">Library</h1>
                <p class="text-sm font-medium text-white/60 mt-1">{{ $groupedMedia->flatten()->count() }} Items</p>
            </div>
            <div class="flex items-center gap-3">
                <button class="w-10 h-10 rounded-full bg-white/10 backdrop-blur-md flex items-center justify-center text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <button class="px-5 py-2 rounded-full bg-white/10 backdrop-blur-md font-bold text-sm text-white">Select</button>
            </div>
        </div>
    </header>

    {{-- Content Grid --}}
    <main class="w-full">
        @forelse($groupedMedia as $monthYear => $mediaItems)
            @php
                [$year, $month] = explode('-', $monthYear);
            @endphp
            <section class="mb-1">
                <div class="px-4 py-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold lowercase tracking-tight">{{ $month }}</h2>
                        <p class="text-[10px] opacity-30 uppercase tracking-widest">{{ $year }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-[1px] md:gap-1">
                    @foreach($mediaItems as $media)
                        <a href="{{ route('gallery.preview', $media->id) }}" wire:navigate 
                           class="relative aspect-square group overflow-hidden bg-white/5 cursor-pointer">
                            <img src="{{ Storage::disk('public')->url($media->file_path_thumbnail ?? $media->file_path_original) }}" 
                                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                 loading="lazy">
                            
                            @if(str_contains($media->file_type, 'video'))
                                <div class="absolute bottom-1.5 right-1.5 bg-black/40 backdrop-blur-md rounded px-1 py-0.5">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                            @endif

                            <div class="absolute inset-0 bg-white/0 group-active:bg-white/20 transition-colors pointer-events-none"></div>
                        </a>
                    @endforeach
                </div>
            </section>
        @empty
            <div class="py-60 text-center space-y-4">
                <div class="w-20 h-20 bg-current/5 rounded-full mx-auto flex items-center justify-center opacity-20">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <p class="text-sm opacity-30 lowercase italic">no photos in your library yet.</p>
            </div>
        @endforelse

        {{-- Library Stats --}}
        <div class="py-12 text-center">
            <p class="text-sm font-bold opacity-80">{{ $groupedMedia->flatten()->count() }} Photos, {{ $groupedMedia->flatten()->filter(fn($m) => str_contains($m->file_type, 'video'))->count() }} Videos</p>
            <p class="text-[10px] opacity-40 uppercase tracking-widest mt-1">last updated just now</p>
        </div>
    </main>
</div>
