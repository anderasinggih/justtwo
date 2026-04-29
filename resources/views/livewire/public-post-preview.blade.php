@php
    $themeColors = [
        'light' => ['bg' => 'bg-white', 'text' => 'text-gray-900', 'sub' => 'text-gray-400', 'border' => 'border-gray-50'],
        'dark' => ['bg' => 'bg-black', 'text' => 'text-white', 'sub' => 'text-white/40', 'border' => 'border-white/5'],
        'rose' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-900', 'sub' => 'text-rose-300', 'border' => 'border-rose-100'],
        'midnight' => ['bg' => 'bg-slate-900', 'text' => 'text-blue-50', 'sub' => 'text-blue-300/40', 'border' => 'border-white/5'],
    ];
    $colors = $themeColors[$theme] ?? $themeColors['light'];
@endphp

<div class="min-h-screen {{ $colors['bg'] }} {{ $colors['text'] }} selection:bg-brand-500/20" 
     x-data="{ 
        targetId: @js($targetId),
        init() {
            this.$nextTick(() => {
                const target = document.getElementById('post-' + this.targetId);
                if (target) {
                    target.scrollIntoView({ behavior: 'auto', block: 'start' });
                    // Adjust for sticky header
                    window.scrollBy(0, -60);
                }
            });
        }
     }">
    {{-- Header --}}
    <nav class="sticky top-0 z-[100] {{ $colors['bg'] }}/80 backdrop-blur-md border-b {{ $colors['border'] }} py-3">
        <div class="max-w-xl mx-auto px-4 flex items-center justify-between">
            <a href="{{ route('welcome') }}" wire:navigate class="{{ $colors['text'] }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="text-sm md:text-base font-bold lowercase tracking-tighter">public stories</h2>
            <div class="w-6"></div> {{-- Spacer --}}
        </div>
    </nav>

    <main class="max-w-xl mx-auto pb-20 space-y-12">
        @foreach($posts as $post)
            <article id="post-{{ $post->id }}" class="{{ $post->id == $targetId ? 'ring-2 ring-brand-500/5' : '' }}">
                {{-- Post Header (IG Style) --}}
                <div class="px-4 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img src="{{ $post->user->profile_photo_url }}" class="w-8 h-8 rounded-full border {{ $colors['border'] }} object-cover">
                        <div>
                            <div class="flex items-center gap-1.5">
                                <p class="text-xs font-bold lowercase tracking-tight">{{ $post->user->name }}</p>
                            </div>
                            <p class="text-[9px] {{ $colors['sub'] }} brightness-[2] opacity-80 lowercase">{{ $post->created_at->format('d F Y') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Media (4:5 Ratio) --}}
                <div class="relative w-full aspect-[4/5] bg-black/5 flex items-center justify-center overflow-hidden">
                    @if($post->media->count() > 1)
                        <div x-data="{ index: 0, total: {{ $post->media->count() }} }" class="w-full h-full">
                            <div class="flex h-full overflow-x-auto snap-x snap-mandatory scrollbar-hide" 
                                 @scroll.debounce.100ms="index = Math.round($event.target.scrollLeft / $event.target.clientWidth)">
                                @foreach($post->media as $item)
                                    <div class="flex-none w-full h-full snap-center relative">
                                        @if(str_contains($item->file_type, 'video'))
                                            <video src="{{ Storage::disk('public')->url($item->file_path_original) }}" 
                                                   class="w-full h-full object-cover" 
                                                   autoplay loop muted playsinline></video>
                                        @else
                                            <img src="{{ Storage::disk('public')->url($item->file_path_original) }}" 
                                                 class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            
                            {{-- Dots --}}
                            <div class="absolute bottom-4 left-0 right-0 flex justify-center gap-1.5">
                                @foreach($post->media as $i => $item)
                                    <div class="h-1 w-1 rounded-full transition-all duration-300 bg-white"
                                         :class="index === {{ $i }} ? 'bg-white w-2' : 'bg-white/40'"></div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        @php $item = $post->media->first(); @endphp
                        @if($item)
                            @if(str_contains($item->file_type, 'video'))
                                <video src="{{ Storage::disk('public')->url($item->file_path_original) }}" 
                                       class="w-full h-full object-cover" 
                                       autoplay loop muted playsinline></video>
                            @else
                                <img src="{{ Storage::disk('public')->url($item->file_path_original) }}" 
                                     class="w-full h-full object-cover">
                            @endif
                        @endif
                    @endif
                </div>



                {{-- Content --}}
                <div class="px-4 pt-2 pb-10 space-y-1" x-data="{ expanded: false }">
                    <div class="text-xs">
                        <span class="font-bold lowercase block mb-0.5">{{ $post->user->name }}</span>
                        <div class="opacity-80 lowercase leading-relaxed relative">
                            <p :class="expanded ? '' : 'line-clamp-2 overflow-hidden text-ellipsis'" 
                               class="whitespace-pre-line">{{ $post->content }}</p>
                            
                            @if(strlen($post->content) > 80)
                                <button x-show="!expanded" @click="expanded = true" 
                                        class="text-[10px] font-bold opacity-40 hover:opacity-100 transition-opacity mt-1">
                                    ... selengkapnya
                                </button>
                            @endif
                        </div>
                    </div>
                    <p class="text-[9px] {{ $colors['sub'] }} brightness-[2] opacity-80 uppercase pt-1">{{ $post->created_at->diffForHumans() }}</p>
                </div>
            </article>
        @endforeach

        {{-- More from --}}
        <div class="mt-12 px-4 pt-8 border-t {{ $colors['border'] }} text-center">
            <a href="{{ route('welcome') }}" wire:navigate class="text-[10px] font-bold uppercase tracking-widest opacity-40 hover:opacity-100 transition-opacity">
                explore more stories
            </a>
        </div>
    </main>
</div>
