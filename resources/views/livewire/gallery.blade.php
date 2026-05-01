<div class="max-w-xl mx-auto pb-32 pt-4 px-4">
    {{-- Header --}}
    <header class="py-8 px-4 border-b theme-border">
        <div class="flex items-center justify-between">
            <h1 class="text-4xl font-bold tracking-tight">Library</h1>
            <div class="flex items-center gap-4">
                <button class="text-brand-500 font-bold text-sm tracking-tight">Select</button>
            </div>
        </div>
    </header>

    {{-- Content Grid --}}
    <main class="w-full -mx-4 md:mx-0">
        @forelse($groupedMedia as $monthYear => $mediaItems)
            @php
                [$year, $month] = explode('-', $monthYear);
            @endphp
            <section class="mb-8">
                <div class="px-4 py-3 border-b theme-border flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold lowercase">{{ $month }}</h2>
                        <p class="text-[10px] opacity-40 uppercase tracking-widest">{{ $year }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-[1px] md:gap-1 px-[1px]">
                    @foreach($mediaItems as $media)
                        <a href="{{ route('gallery.preview', $media->id) }}" wire:navigate 
                           class="relative aspect-square group overflow-hidden bg-current/5 cursor-pointer">
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
            <div class="py-40 text-center space-y-4">
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
