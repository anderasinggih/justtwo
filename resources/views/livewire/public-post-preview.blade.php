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

    <main class="max-w-xl mx-auto pb-20 space-y-0 sm:space-y-8 px-0 sm:px-4">
        @foreach($posts as $post)
            <article id="post-{{ $post->id }}" 
                     class="bg-white/5 sm:border {{ $colors['border'] }} sm:rounded-2xl overflow-hidden {{ $post->id == $targetId ? 'ring-2 ring-brand-500/10' : '' }}">
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
                <div x-data="{ 
                    index: 0, 
                    total: {{ $post->media->count() }},
                    showHeart: false,
                    like() {
                        $wire.toggleReaction({{ $post->id }});
                        this.showHeart = true;
                        setTimeout(() => this.showHeart = false, 800);
                    }
                }" class="relative w-full aspect-[4/5] bg-black/5 flex items-center justify-center overflow-hidden">
                    <div class="w-full h-full" @dblclick="like()">
                        @if($post->media->count() > 1)
                            <div class="w-full h-full">
                                <div class="flex h-full overflow-x-auto snap-x snap-mandatory scrollbar-hide" 
                                     x-ref="slider"
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

                    {{-- Big Heart Animation --}}
                    <div x-show="showHeart" x-cloak 
                         x-transition:enter="transition-all ease-[cubic-bezier(0.175,0.885,0.32,1.275)] duration-500"
                         x-transition:enter-start="opacity-0 scale-50 rotate-[-15deg]"
                         x-transition:enter-end="opacity-100 scale-125 rotate-0"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 scale-125"
                         x-transition:leave-end="opacity-0 scale-150"
                         class="absolute inset-0 flex items-center justify-center pointer-events-none z-20">
                        <svg class="w-32 h-32 text-brand-500 fill-current filter drop-shadow-[0_0_30px_rgba(244,63,94,0.6)]" viewBox="0 0 24 24">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        </svg>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="px-4 pt-3 flex items-center justify-between" x-data="{ activeComments: false }">
                    <div class="flex items-center gap-4">
                        <button @click="$wire.toggleReaction({{ $post->id }})" class="transition-transform active:scale-125">
                            <svg class="w-6 h-6 {{ $post->reactions->where('user_id', Auth::id())->where('guest_id', session()->getId())->isNotEmpty() ? 'text-brand-500 fill-current' : $colors['text'].' opacity-70' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </button>
                        <button @click="activeComments = !activeComments" class="{{ $colors['text'] }} opacity-70">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        </button>
                    </div>
                </div>

                {{-- Content & Comments --}}
                <div class="px-4 pt-2 pb-10 space-y-3" x-data="{ expanded: false }">
                    @if($post->reactions->count() > 0)
                        <p class="text-xs font-bold lowercase">{{ $post->reactions->count() }} likes</p>
                    @endif

                    <div class="text-xs">
                        <span class="font-bold lowercase block mb-0.5">{{ $post->user->name }}</span>
                        <div class="opacity-80 lowercase leading-relaxed relative">
                            <p :class="expanded ? '' : 'line-clamp-2 overflow-hidden text-ellipsis'" 
                               class="whitespace-pre-line">{{ $post->content }}</p>
                            
                            @if(strlen($post->content) > 80)
                                <button x-show="!expanded" @click="expanded = true" 
                                        class="text-[10px] font-bold opacity-40 hover:opacity-100 transition-opacity mt-1">
                                    ... more
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- View Only Comments --}}
                    @if($post->comments->count() > 0)
                        <div class="space-y-3 mt-4 pt-4 border-t {{ $colors['border'] }}">
                            @foreach($post->comments as $comment)
                                <div class="space-y-3">
                                    <div class="flex items-start gap-2">
                                        <img src="{{ $comment->user->profile_photo_url }}" class="w-5 h-5 rounded-full object-cover">
                                        <div class="flex-1">
                                            <p class="text-[11px]">
                                                <span class="font-bold lowercase mr-1">{{ $comment->user->name }}</span>
                                                <span class="opacity-70 lowercase">{{ $comment->content }}</span>
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Replies --}}
                                    @foreach($comment->replies as $reply)
                                        <div class="flex items-start gap-2 ml-7">
                                            <img src="{{ $reply->user->profile_photo_url }}" class="w-4 h-4 rounded-full object-cover">
                                            <div class="flex-1">
                                                <p class="text-[10px]">
                                                    <span class="font-bold lowercase mr-1">{{ $reply->user->name }}</span>
                                                    <span class="opacity-70 lowercase">{{ $reply->content }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @endif

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
