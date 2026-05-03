@php
    $themeColors = [
        'light' => ['bg' => 'bg-white', 'text' => 'text-gray-900', 'sub' => 'text-gray-400', 'border' => 'border-gray-100'],
        'dark' => ['bg' => 'bg-black', 'text' => 'text-white', 'sub' => 'text-white/40', 'border' => 'border-white/5'],
        'rose' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-900', 'sub' => 'text-rose-300', 'border' => 'border-rose-100'],
        'midnight' => ['bg' => 'bg-slate-900', 'text' => 'text-blue-50', 'sub' => 'text-blue-300/40', 'border' => 'border-white/5'],
    ];
    $colors = $themeColors[$theme] ?? $themeColors['light'];
    $heroPost = $posts->first();
@endphp

<div class="min-h-screen theme-bg theme-text selection:bg-brand-500/20">
    {{-- iOS Hero Header - TRUE FULLSCREEN (100dvh) --}}
    <header class="relative w-full h-[100dvh] md:h-[65vh] lg:h-[85vh] overflow-hidden"
            x-data="{ 
                active: 0, 
                images: {{ json_encode($allMediaPaths) }},
                init() {
                    if (this.images.length > 1) {
                        setInterval(() => {
                            this.active = (this.active + 1) % this.images.length;
                        }, 3500);
                    }
                }
            }">
        @if(count($allMediaPaths) > 0)
            <template x-for="(img, index) in images" :key="index">
                <div x-show="active === index" 
                     x-transition:enter="transition opacity duration-1500 ease-in-out"
                     x-transition:leave="transition opacity duration-1500 ease-in-out"
                     class="absolute inset-0 w-full h-full">
                    <img :src="img" class="w-full h-full object-cover">
                </div>
            </template>
        @else
            <div class="w-full h-full bg-current opacity-[0.03] flex items-center justify-center italic">no preview</div>
        @endif

        {{-- Top Navigation Bar (Welcome-style Mix-Blend) --}}
        <nav class="absolute top-0 left-0 right-0 p-6 md:p-12 lg:p-24 flex items-center justify-between z-30 mix-blend-difference text-white">
            <a href="{{ route('welcome') }}" wire:navigate class="flex items-center gap-2 group">
                <div class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center transition-all group-hover:bg-white group-hover:text-black">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </div>
                <span class="text-xs font-bold tracking-widest uppercase hidden md:inline">back</span>
            </a>
            <div class="text-xl font-bold tracking-tighter lowercase">{{ $spaceName ?? 'justtwo' }}</div>
            <div class="w-10"></div>
        </nav>

        {{-- Hero Bottom Info (Editorial Style) --}}
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent flex flex-col justify-end p-8 md:p-16 lg:p-24 text-white pointer-events-none">
            <h1 class="text-5xl md:text-8xl font-bold tracking-tighter lowercase mb-2">{{ $monthName }}</h1>
            <div class="flex items-center gap-3 opacity-60">
                <div class="h-[1px] w-12 bg-white"></div>
                <span class="text-xs md:text-sm font-bold tracking-[0.2em] uppercase">{{ $posts->total() }} captured moments</span>
            </div>
        </div>
    </header>

    {{-- Gapless Square Grid (iOS Style) --}}
    <main class="w-full">
        <div class="grid grid-cols-3 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-8 gap-[1px]">
            @foreach($posts as $post)
                @foreach($post->media as $media)
                    <a href="{{ route('posts.preview', $post) }}" wire:navigate 
                       class="relative aspect-square overflow-hidden group">
                        {{-- Media --}}
                        <img src="{{ Storage::disk('public')->url($media->file_path_original) }}" 
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">

                        {{-- iOS Favorite Heart Icon --}}
                        <div class="absolute bottom-2 left-2 pointer-events-none opacity-0 group-hover:opacity-100 md:group-hover:opacity-100 transition-opacity">
                            <svg class="w-4 h-4 text-white fill-current drop-shadow-md" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                        </div>

                        {{-- Selection Overlay --}}
                        <div class="absolute inset-0 bg-white/0 group-hover:bg-white/10 transition-colors pointer-events-none"></div>
                    </a>
                @endforeach
            @endforeach
        </div>

        @if($posts->hasPages())
            <div class="mt-8 px-4">
                {{ $posts->links() }}
            </div>
        @endif
    </main>
</div>

