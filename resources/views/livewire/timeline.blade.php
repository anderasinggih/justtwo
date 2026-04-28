<div class="max-w-xl mx-auto pb-12 pt-6" wire:poll.30s.visible>
    {{-- Search Bar (Slim) --}}
    <div class="px-4 mb-4">
        <div class="relative group">
            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input 
                wire:model.live.debounce.300ms="search" 
                placeholder="search memories..." 
                class="w-full bg-white/5 border theme-border rounded-xl pl-10 pr-4 py-2 text-sm shadow-sm focus:ring-brand-200 focus:border-brand-300 transition-all lowercase theme-text"
            >
        </div>
    </div>

    {{-- Feed (Instagram Style: No gaps, No lines) --}}
    <div class="space-y-0">
        @forelse($posts as $post)
            <article class="theme-card sm:border theme-border sm:rounded-2xl sm:mb-8 overflow-hidden">
                {{-- User Header --}}
                <div class="px-4 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img src="{{ $post->user->profile_photo_url }}" class="w-8 h-8 rounded-full border theme-border object-cover">
                        <div>
                            <div class="flex items-center gap-1.5">
                                <p class="text-xs font-bold lowercase tracking-tight theme-text">{{ $post->user->id === Auth::id() ? 'you' : $post->user->name }}</p>
                                @if($post->mood)
                                    <span class="text-[10px]">{{ $post->mood }}</span>
                                @endif
                            </div>
                            @if($post->location)
                                <p class="text-[9px] text-gray-400 lowercase">{{ $post->location }}</p>
                            @else
                                <p class="text-[9px] text-gray-400 lowercase">
                                    {{ $post->created_at->diffInHours() < 24 
                                        ? $post->created_at->diffForHumans() 
                                        : ($post->created_at->isCurrentYear() ? $post->created_at->format('d F') : $post->created_at->format('d F Y')) }}
                                </p>
                            @endif
                        </div>
                    </div>
                    
                    <button class="text-gray-400">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"></path></svg>
                    </button>
                </div>

                {{-- Media Gallery --}}
                @if($post->media->isNotEmpty())
                    <div x-data="{ 
                        index: 0, 
                        total: {{ $post->media->count() }},
                        open(src) { $dispatch('open-lightbox', src) } 
                    }">
                        <div class="relative group/carousel">
                            <div class="flex overflow-x-auto snap-x snap-mandatory scrollbar-hide" 
                                 x-ref="slider"
                                 @scroll.debounce.100ms="index = Math.round($event.target.scrollLeft / $event.target.clientWidth)">
                                @foreach($post->media as $item)
                                    <div class="flex-none w-full snap-center aspect-[4/5] bg-gray-900/50 relative overflow-hidden cursor-pointer"
                                         @click="open('{{ Storage::disk('public')->url($item->file_path_original) }}')">
                                        @if($item->file_path_original)
                                            <img src="{{ Storage::disk('public')->url($item->file_path_thumbnail ?? $item->file_path_original) }}" 
                                                 class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            {{-- Indicator Dots --}}
                            @if($post->media->count() > 1)
                                <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-1.5">
                                    @foreach($post->media as $i => $item)
                                        <div class="h-1 w-1 rounded-full transition-all duration-300 {{ $i === 0 ? 'bg-white' : 'bg-white/40' }}"
                                             :class="index === {{ $i }} ? 'bg-white w-2' : 'bg-white/40'"></div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Actions --}}
                <div class="px-4 pt-3 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <button wire:click="toggleReaction({{ $post->id }})" class="transition-transform active:scale-125">
                            <svg class="w-6 h-6 {{ $post->reactions->where('user_id', Auth::id())->first() ? 'theme-accent fill-current' : 'theme-text opacity-70' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </button>
                        <button class="theme-text opacity-70">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        </button>
                    </div>
                    <button wire:click="archivePost({{ $post->id }})" class="text-gray-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                    </button>
                </div>

                {{-- Content --}}
                <div class="px-4 py-2 space-y-1 pb-6">
                    @if($post->reactions->count() > 0)
                        <p class="text-xs font-bold lowercase theme-text">{{ $post->reactions->count() }} likes</p>
                    @endif
                    
                    <div class="text-xs">
                        <span class="font-bold lowercase mr-1 theme-text">{{ $post->user->id === Auth::id() ? 'you' : $post->user->name }}</span>
                        <span class="opacity-80 lowercase theme-text">{{ $post->content }}</span>
                    </div>

                    @if($post->comments->count() > 0)
                        <button class="text-[10px] opacity-50 lowercase mt-1 theme-text">view all {{ $post->comments->count() }} comments</button>
                    @endif
                    
                    @php
                        $isRecent = $post->created_at->diffInHours() < 24;
                        $isThisYear = $post->created_at->isCurrentYear();
                        $formattedDate = $isRecent 
                            ? $post->created_at->diffForHumans() 
                            : ($isThisYear ? $post->created_at->format('d F') : $post->created_at->format('d F Y'));
                    @endphp
                    <p class="text-[9px] opacity-30 uppercase mt-1 theme-text">{{ $formattedDate }}</p>
                </div>
            </article>
        @empty
            <div class="py-20 text-center">
                <p class="opacity-30 lowercase theme-text">no memories yet.</p>
            </div>
        @endforelse

        <div class="p-4">
            {{ $posts->links() }}
        </div>
    </div>
</div>
