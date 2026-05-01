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

<div class="min-h-screen {{ $colors['bg'] }} {{ $colors['text'] }} selection:bg-brand-500/20 pb-20">
    {{-- iOS Hero Header --}}
    <header class="relative w-full aspect-[4/5] md:aspect-[21/9] overflow-hidden"
            x-data="{ 
                active: 0, 
                images: {{ json_encode($allMediaPaths) }},
                init() {
                    if (this.images.length > 1) {
                        setInterval(() => {
                            this.active = (this.active + 1) % this.images.length;
                        }, 3000);
                    }
                }
            }">
        @if(count($allMediaPaths) > 0)
            <template x-for="(img, index) in images" :key="index">
                <div x-show="active === index" 
                     x-transition:enter="transition opacity duration-1000 ease-in-out"
                     x-transition:leave="transition opacity duration-1000 ease-in-out"
                     class="absolute inset-0 w-full h-full">
                    <img :src="img" class="w-full h-full object-cover">
                </div>
            </template>
        @else
            <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-300 flex items-center justify-center italic opacity-30">no preview</div>
        @endif

        {{-- Top Navigation Bar (Transparent to Blur) --}}
        <div class="absolute top-0 left-0 right-0 p-4 md:p-8 flex items-center justify-between z-20">
            <a href="{{ route('welcome') }}" wire:navigate class="w-10 h-10 rounded-full bg-black/20 backdrop-blur-xl border border-white/10 flex items-center justify-center text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div class="w-10"></div>
        </div>

        {{-- Hero Bottom Info --}}
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent flex flex-col justify-end p-6 md:p-12 text-white pointer-events-none">
            <h1 class="text-4xl md:text-6xl font-bold tracking-tight lowercase">{{ $monthName }}</h1>
            <div class="flex items-center gap-2 opacity-80 mt-1">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                <span class="text-sm md:text-base font-medium">{{ $posts->total() }} items</span>
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

