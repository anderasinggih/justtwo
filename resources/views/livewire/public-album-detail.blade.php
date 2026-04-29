@php
    $themeColors = [
        'light' => ['bg' => 'bg-white', 'text' => 'text-gray-900', 'sub' => 'text-gray-400', 'border' => 'border-gray-100'],
        'dark' => ['bg' => 'bg-black', 'text' => 'text-white', 'sub' => 'text-white/40', 'border' => 'border-white/5'],
        'rose' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-900', 'sub' => 'text-rose-300', 'border' => 'border-rose-100'],
        'midnight' => ['bg' => 'bg-slate-900', 'text' => 'text-blue-50', 'sub' => 'text-blue-300/40', 'border' => 'border-white/5'],
    ];
    $colors = $themeColors[$theme] ?? $themeColors['light'];
@endphp

<div class="min-h-screen {{ $colors['bg'] }} {{ $colors['text'] }} selection:bg-brand-500/20">
    {{-- Header --}}
    <nav class="sticky top-0 z-[100] {{ $colors['bg'] }}/80 backdrop-blur-md border-b {{ $colors['border'] }} py-4 md:py-6">
        <div class="max-w-7xl mx-auto px-4 md:px-12 flex items-center justify-between">
            <a href="{{ route('welcome') }}" wire:navigate class="flex items-center gap-2 {{ $colors['text'] }} opacity-60 hover:opacity-100 transition-opacity">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span class="text-xs font-bold uppercase tracking-widest hidden md:inline">back to albums</span>
            </a>
            
            <div class="text-center">
                <h2 class="text-sm md:text-xl font-bold lowercase tracking-tighter">{{ $monthName }} {{ $year }}</h2>
                <p class="text-[9px] md:text-[10px] {{ $colors['sub'] }} uppercase tracking-[0.2em] mt-0.5">{{ $posts->total() }} memories captured</p>
            </div>

            <div class="w-10"></div> {{-- Spacer --}}
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-8 md:py-16 px-0 md:px-12">
        {{-- Premium Dynamic Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-0 md:gap-6 lg:gap-8 auto-rows-[160px] md:auto-rows-[250px]">
            @forelse($posts as $index => $post)
                @php
                    $pattern = $index % 8;
                    $classes = 'col-span-1 row-span-1';
                    if ($pattern === 0) $classes = 'col-span-2 row-span-1';
                    if ($pattern === 5) $classes = 'col-span-1 row-span-2';
                @endphp
                <a href="{{ route('posts.preview', $post) }}" wire:navigate 
                   class="relative group cursor-pointer overflow-hidden rounded-none md:rounded-[2.5rem] transition-all duration-500 hover:shadow-[0_20px_50px_rgba(0,0,0,0.1)] {{ $classes }}">
                    
                    {{-- Media --}}
                    <div class="w-full h-full bg-gray-50 overflow-hidden">
                        @if($post->media->isNotEmpty())
                            <img src="{{ Storage::disk('public')->url($post->media->first()->file_path_original) }}" 
                                 class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
                        @else
                            <div class="w-full h-full flex items-center justify-center p-6 bg-gradient-to-br from-brand-50 to-white">
                                <p class="text-[10px] theme-text opacity-40 line-clamp-4 italic text-center">{{ $post->content }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Hover Overlay --}}
                    <div class="absolute inset-0 bg-black/40 md:bg-black/20 opacity-0 group-hover:opacity-100 transition-all duration-500 backdrop-blur-[2px] flex flex-col justify-end p-4 md:p-8">
                        <div class="transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                            <p class="text-[8px] md:text-[10px] text-white/70 uppercase tracking-widest font-medium mb-1">{{ $post->location ?? 'Captured Moment' }}</p>
                            <div class="flex items-center justify-between">
                                <p class="text-xs md:text-sm text-white font-bold lowercase">view story</p>
                                @if($post->reactions->count() > 0)
                                    <span class="flex items-center gap-1 text-[10px] text-white font-bold">
                                        <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                        {{ $post->reactions->count() }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full py-20 text-center opacity-40">
                    <p class="italic lowercase">no stories found for this period.</p>
                </div>
            @endforelse
        </div>

        @if($posts->hasPages())
            <div class="mt-16">
                {{ $posts->links() }}
            </div>
        @endif
    </main>
</div>
