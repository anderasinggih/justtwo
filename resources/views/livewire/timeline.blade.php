<div class="max-w-3xl mx-auto pb-32 pt-2 px-1.5 sm:px-4" wire:poll.30s.visible>
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

    {{-- Feed --}}
    <div class="space-y-0" x-data="{ 
        activeMenu: null, 
        activeComments: null,
        replyingTo: null,
        replyingToName: '',
        commentContent: ''
    }">
        @forelse($posts as $post)
            @php
                $isLocked = $post->is_secret && $post->unlock_at && $post->unlock_at->isFuture() && $post->user_id !== Auth::id();
            @endphp
             <article id="post-{{ $post->id }}" 
                      x-init="if({{ $post->id }} == {{ $this->post ?? 'null' }}) { $el.scrollIntoView({ behavior: 'smooth', block: 'center' }) }"
                      class="theme-card sm:border theme-border sm:rounded-2xl sm:mb-8 overflow-hidden relative">
                {{-- User Header --}}
                <div class="px-4 py-3 flex items-center justify-between relative">
                    <div class="flex items-center gap-3">
                        <img src="{{ $post->user->profile_photo_url }}" class="w-8 h-8 rounded-full border theme-border object-cover">
                        <div>
                            <div class="flex items-center gap-1.5">
                                <p class="text-xs font-bold lowercase tracking-tight theme-text">{{ $post->user->id === Auth::id() ? 'you' : $post->user->name }}</p>
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
                    
                    {{-- Three Dots Menu --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="p-2 theme-text opacity-40 hover:opacity-100 transition-opacity">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"></path></svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-cloak
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="absolute right-0 mt-2 w-48 theme-card border theme-border rounded-xl shadow-xl z-50 overflow-hidden">
                            
                            @if($post->user_id === Auth::id())
                                <a href="{{ route('memory.edit', $post) }}" wire:navigate class="flex items-center gap-3 px-4 py-3 text-xs theme-text hover:bg-white/5 transition-colors lowercase">
                                    <svg class="w-4 h-4 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    edit
                                </a>
                            @endif
                            
                            <button wire:click="archivePost({{ $post->id }})" @click="open = false" class="w-full flex items-center gap-3 px-4 py-3 text-xs theme-text hover:bg-white/5 transition-colors lowercase text-left">
                                <svg class="w-4 h-4 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                {{ $post->is_archived ? 'unarchive' : 'archive' }}
                            </button>

                            @if($post->user_id === Auth::id())
                                <button wire:click="deletePost({{ $post->id }})" 
                                        wire:confirm="are you sure you want to delete this memory?"
                                        @click="open = false" 
                                        class="w-full flex items-center gap-3 px-4 py-3 text-xs text-red-500 hover:bg-red-500/5 transition-colors lowercase text-left border-t theme-border">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    delete
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Media Gallery --}}
                @if($post->media->isNotEmpty())
                    <div x-data="{ 
                        index: {{ ($post->id == $this->post) ? $this->index : 0 }}, 
                        total: {{ $post->media->count() }},
                        showHeart: false,
                        initialized: false,
                        init() {
                            if(this.index > 0) {
                                setTimeout(() => {
                                    if (this.$refs.slider) {
                                        this.$refs.slider.scrollLeft = this.$refs.slider.offsetWidth * this.index;
                                    }
                                    setTimeout(() => { this.initialized = true }, 100);
                                }, 400);
                            } else {
                                this.initialized = true;
                            }
                        },
                        like() {
                            if (!{{ $post->reactions->where('user_id', Auth::id())->isNotEmpty() ? 'true' : 'false' }}) {
                                $wire.toggleReaction({{ $post->id }});
                            }
                            this.showHeart = true;
                            setTimeout(() => this.showHeart = false, 800);
                        }
                    }">
                        <div class="relative group/carousel">
                            <div class="flex overflow-x-auto snap-x snap-mandatory scrollbar-hide {{ $isLocked ? 'blur-2xl brightness-50 pointer-events-none' : '' }}" 
                                 x-ref="slider"
                                 @dblclick="like()"
                                 @scroll.debounce.100ms="if(initialized) index = Math.round($event.target.scrollLeft / $event.target.offsetWidth)">
                                @foreach($post->media as $i => $item)
                                    <div id="media-{{ $post->id }}-{{ $i }}" class="flex-none w-full snap-center aspect-[4/5] bg-gray-900/50 relative overflow-hidden">
                                        @if($item->file_path_original)
                                            <img src="{{ Storage::disk('public')->url($item->file_path_thumbnail ?? $item->file_path_original) }}" 
                                                 class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                @endforeach
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

                            @if($post->media->count() > 1)
                                <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-1.5">
                                    @foreach($post->media as $i => $item)
                                        <div class="h-1 w-1 rounded-full transition-all duration-300"
                                             :class="index === {{ $i }} ? 'bg-white w-2' : 'bg-white/40'"></div>
                                    @endforeach
                                </div>
                            @endif
                            @if($isLocked)
                                {{-- Locked Overlay --}}
                                <div class="absolute inset-0 z-30 flex flex-col items-center justify-center text-center p-6 bg-black/20">
                                    <div class="w-16 h-16 rounded-full bg-white/10 backdrop-blur-xl border border-white/20 flex items-center justify-center text-white mb-4 shadow-2xl">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    </div>
                                    <p class="text-white text-sm font-bold lowercase tracking-tight">secret note from {{ $post->user->name }}</p>
                                    <p class="text-white/60 text-[10px] lowercase mt-1 italic">unlocks {{ $post->unlock_at->diffForHumans() }}</p>
                                    
                                    <div class="mt-6 flex items-center gap-2 bg-white/10 backdrop-blur-md px-3 py-1.5 rounded-full border border-white/10">
                                        <div class="w-1.5 h-1.5 rounded-full bg-brand-500 animate-pulse"></div>
                                        <span class="text-[9px] font-bold text-white uppercase tracking-widest">{{ $post->unlock_at->format('M d, H:i') }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Actions --}}
                <div class="px-4 pt-3 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <button wire:click="toggleReaction({{ $post->id }})" class="transition-transform active:scale-125">
                            <svg class="w-6 h-6 {{ $post->reactions->where('user_id', Auth::id())->isNotEmpty() ? 'theme-accent fill-current' : 'theme-text opacity-70' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </button>
                        <button @click="activeComments = (activeComments === {{ $post->id }} ? null : {{ $post->id }}); replyingTo = null" class="theme-text opacity-70">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        </button>
                    </div>
                    <button wire:click="toggleBookmark({{ $post->id }})" class="transition-transform active:scale-125">
                        <svg class="w-5 h-5 {{ $post->bookmarks->where('user_id', Auth::id())->isNotEmpty() ? 'theme-accent fill-current' : 'theme-text opacity-70' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                        </svg>
                    </button>
                </div>

                {{-- Content & Comments --}}
                <div class="px-4 py-2 space-y-2 pb-6">
                    @if($post->reactions->count() > 0)
                        <p class="text-xs font-bold lowercase theme-text">{{ $post->reactions->count() }} likes</p>
                    @endif
                    
                    <div class="text-xs" x-data="{ expanded: false }">
                        <span class="font-bold lowercase theme-text block mb-1">{{ $post->user->id === Auth::id() ? 'you' : $post->user->name }}</span>
                        <div class="relative">
                            <div class="opacity-80 lowercase theme-text break-words {{ $isLocked ? 'blur-md select-none' : '' }}" 
                                 :class="!expanded ? 'line-clamp-2' : ''"
                                 @click="expanded = true">
                                {!! nl2br(e($isLocked ? Str::random(50) : $post->content)) !!}
                            </div>
                            @if(strlen($post->content) > 80)
                                <button x-show="!expanded" @click.stop="expanded = true" 
                                        class="text-[10px] theme-text opacity-40 hover:opacity-100 transition-opacity lowercase font-bold mt-1">
                                    ... more
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Comments Section --}}
                    <div x-show="activeComments === {{ $post->id }}" x-cloak x-collapse class="mt-4 space-y-4">
                        @foreach($post->comments as $comment)
                            <div class="space-y-4">
                                {{-- Parent Comment --}}
                                <div class="flex items-start gap-3">
                                    <img src="{{ $comment->user->profile_photo_url }}" class="w-6 h-6 rounded-full object-cover">
                                    <div class="flex-1">
                                        <p class="text-xs theme-text">
                                            <span class="font-bold lowercase mr-1">{{ $comment->user->name }}</span>
                                            <span class="opacity-70 lowercase">{{ $comment->content }}</span>
                                        </p>
                                        <div class="flex items-center gap-3 mt-1">
                                            <p class="text-[9px] opacity-30 uppercase">{{ $comment->created_at->diffForHumans() }}</p>
                                            <button @click="replyingTo = {{ $comment->id }}; replyingToName = '{{ $comment->user->name }}'; commentContent = '@' + replyingToName + ' '" 
                                                    class="text-[9px] font-bold opacity-40 hover:opacity-100 transition-opacity lowercase">reply</button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Replies --}}
                                @if($comment->replies->count() > 0)
                                    <div class="ml-9 space-y-4 border-l theme-border pl-4">
                                        @foreach($comment->replies as $reply)
                                            <div class="flex items-start gap-3">
                                                <img src="{{ $reply->user->profile_photo_url }}" class="w-5 h-5 rounded-full object-cover">
                                                <div class="flex-1">
                                                    <p class="text-xs theme-text">
                                                        <span class="font-bold lowercase mr-1">{{ $reply->user->name }}</span>
                                                        <span class="opacity-70 lowercase">{{ $reply->content }}</span>
                                                    </p>
                                                    <p class="text-[9px] opacity-30 uppercase mt-0.5">{{ $reply->created_at->diffForHumans() }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        {{-- Comment Input --}}
                        <div class="relative mt-6">
                            {{-- Reply Indicator --}}
                            <template x-if="replyingTo">
                                <div class="flex items-center justify-between px-3 py-1.5 bg-brand-500/5 rounded-t-xl border-t border-x theme-border">
                                    <p class="text-[10px] theme-text opacity-40 lowercase">replying to <span class="font-bold theme-accent" x-text="replyingToName"></span></p>
                                    <button @click="replyingTo = null; replyingToName = ''; commentContent = ''" class="theme-text opacity-40 hover:opacity-100">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </template>
                            
                            <div class="relative">
                                <input 
                                    x-model="commentContent"
                                    @keydown.enter="$wire.addComment({{ $post->id }}, commentContent, replyingTo); commentContent = ''; replyingTo = null"
                                    placeholder="add a comment..."
                                    :class="replyingTo ? 'rounded-b-xl rounded-t-none' : 'rounded-xl'"
                                    class="w-full bg-white/5 border theme-border px-4 py-2 text-xs theme-text lowercase focus:ring-brand-500/20"
                                >
                            </div>
                        </div>
                    </div>

                    <template x-if="activeComments !== {{ $post->id }} && {{ $post->comments->count() }} > 0">
                        <button @click="activeComments = {{ $post->id }}" class="text-[10px] opacity-30 lowercase mt-1 theme-text hover:opacity-100 transition-opacity">
                            view all {{ $post->comments->count() }} comments
                        </button>
                    </template>
                    
                    <p class="text-[9px] opacity-20 uppercase mt-1 theme-text">
                        {{ $post->created_at->diffInHours() < 24 
                            ? $post->created_at->diffForHumans() 
                            : ($post->created_at->isCurrentYear() ? $post->created_at->format('d F') : $post->created_at->format('d F Y')) }}
                    </p>
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
