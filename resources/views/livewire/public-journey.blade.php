<div class="max-w-4xl mx-auto px-4 py-8 md:py-16 min-h-screen">
    {{-- Top Nav --}}
    <div class="mb-12 flex items-center justify-between">
        <a href="/" wire:navigate class="p-2 -ml-2 opacity-50 hover:opacity-100 transition-all">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h2 class="text-xl md:text-4xl font-bold tracking-tighter lowercase">all journey</h2>
        <div class="w-10"></div> {{-- Spacer --}}
    </div>

    {{-- Playlist Items --}}
    <div class="space-y-1 md:space-y-2">
        @forelse($videos as $video)
            <a href="https://youtube.com/watch?v={{ $video['id'] }}" target="_blank" class="flex gap-4 md:gap-8 p-2 rounded-2xl hover:bg-white/5 active:scale-[0.98] transition-all group">
                {{-- Thumbnail Side --}}
                <div class="relative w-36 md:w-56 aspect-video rounded-xl md:rounded-2xl overflow-hidden flex-shrink-0 bg-black/20 shadow-lg">
                    <img src="https://img.youtube.com/vi/{{ $video['id'] }}/hqdefault.jpg" 
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    
                    {{-- Duration Badge --}}
                    <div class="absolute bottom-1.5 right-1.5 bg-black/80 backdrop-blur-sm text-[9px] font-bold px-1.5 py-0.5 rounded text-white tracking-tight">
                        PLAY
                    </div>

                    {{-- Progress Bar (Aesthetic) --}}
                    <div class="absolute bottom-0 left-0 h-[3px] bg-red-600 w-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>

                {{-- Info Side --}}
                <div class="flex-grow py-1 flex flex-col justify-center min-w-0">
                    <h3 class="text-[13px] md:text-2xl font-bold tracking-tight leading-tight line-clamp-2 theme-text mb-1 lowercase">
                        {{ $video['title'] }}
                    </h3>
                    <div class="text-[10px] md:text-sm opacity-40 font-medium line-clamp-1 lowercase tracking-tight">
                        {{ \Illuminate\Support\Str::limit($video['description'] ?? 'our journey', 100, '......') }}
                    </div>
                </div>
            </a>
        @empty
            <div class="flex flex-col items-center justify-center py-20 opacity-20">
                <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                <p class="text-xs font-bold uppercase tracking-widest">No journey videos found</p>
            </div>
        @endforelse
    </div>

    {{-- Footer --}}
    @php
        $relationship = \App\Models\Relationship::orderBy('id', 'desc')->first();
        $spaceName = $relationship?->name ?? 'justtwo';
    @endphp
    <footer class="mt-24 py-8 text-center border-t theme-border bg-transparent">
        <p class="text-[11px] opacity-50 tracking-tight">
            All Rights Reserved ©Copyright 2026 {{ $spaceName }}
        </p>
    </footer>
</div>
