<div class="max-w-xl mx-auto px-1.5 pt-6 space-y-4 pb-20">
    {{-- Minimalist Header --}}
    <div class="px-2 flex items-center justify-between mb-2">
        <div>
            <h2 class="text-xs font-bold theme-text opacity-30 lowercase tracking-widest">our space</h2>
            <h1 class="text-xl font-bold theme-text lowercase tracking-tighter">{{ Auth::user()->relationship->name }}</h1>
        </div>
        <div class="w-10 h-10 rounded-full bg-white/5 border theme-border flex items-center justify-center">
            <svg class="w-5 h-5 theme-accent" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
        </div>
    </div>

    {{-- Hero Section: Together Timer (Dynamic Alpine Counter) --}}
    <div class="relative overflow-hidden theme-card border theme-border rounded-3xl p-6 shadow-sm text-center"
         x-data="{ 
            start: @js($togetherStats['timestamp']),
            days: 0, hours: 0, mins: 0, secs: 0,
            update() {
                let now = new Date().getTime();
                let diff = Math.abs(now - this.start);
                this.days = Math.floor(diff / (1000 * 60 * 60 * 24));
                this.hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                this.mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                this.secs = Math.floor((diff % (1000 * 60)) / 1000);
            }
         }"
         x-init="update(); setInterval(() => update(), 1000)">
        
        <div class="absolute -right-16 -top-16 w-32 h-32 bg-brand-500/10 rounded-full blur-3xl opacity-40"></div>
        <div class="absolute -left-16 -bottom-16 w-32 h-32 theme-accent-bg opacity-10 rounded-full blur-3xl"></div>
        
        <div class="relative z-10 space-y-3">
            <div class="flex justify-center items-center gap-4 md:gap-8">
                <div class="flex flex-col items-center">
                    <img src="{{ Auth::user()->profile_photo_url }}" class="w-12 h-12 md:w-14 md:h-14 rounded-full border-2 theme-border shadow-sm object-cover">
                    <p class="text-[8px] font-bold mt-1 lowercase theme-text opacity-50">{{ Auth::user()->name }}</p>
                </div>
                
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 bg-brand-500/5 rounded-full flex items-center justify-center theme-accent animate-pulse">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                    </div>
                </div>

                <div class="flex flex-col items-center">
                    @if($partner)
                        <img src="{{ $partner->profile_photo_url }}" class="w-12 h-12 md:w-14 md:h-14 rounded-full border-2 theme-border shadow-sm object-cover">
                        <p class="text-[8px] font-bold mt-1 lowercase theme-text opacity-50">{{ $partner->name }}</p>
                    @else
                        <div class="w-12 h-12 rounded-full border-2 border-dashed theme-border flex items-center justify-center bg-white/5">
                            <p class="text-[7px] opacity-30 theme-text lowercase">wait</p>
                        </div>
                    @endif
                </div>
            </div>

            <div>
                <div class="flex items-center justify-center gap-1.5 flex-wrap">
                    <h1 class="text-2xl font-bold tracking-tighter theme-text" x-text="days + 'd'"></h1>
                    <h1 class="text-2xl font-bold tracking-tighter theme-text" x-text="hours + 'h'"></h1>
                    <h1 class="text-2xl font-bold tracking-tighter theme-text" x-text="mins + 'm'"></h1>
                    <h1 class="text-2xl font-bold tracking-tighter theme-accent" x-text="secs + 's'"></h1>
                </div>
                <p class="text-[10px] opacity-40 theme-text lowercase tracking-wide mt-1">
                    since {{ $togetherStats['anniversary_formatted'] }}
                </p>
            </div>
        </div>
    </div>

    {{-- Section: Our Next Big Event --}}
    @if($nextMilestone)
    <div class="theme-card border theme-border rounded-3xl p-4 shadow-sm space-y-3">
        <div class="flex items-center justify-between px-1">
            <h2 class="text-[10px] font-bold lowercase tracking-tight theme-text opacity-50">next event</h2>
            <span class="text-[9px] font-bold theme-accent uppercase tracking-widest">{{ $daysRemainingFormatted }}</span>
        </div>

        <div class="relative p-4 bg-white/5 rounded-2xl overflow-hidden group border theme-border">
            <div class="relative z-10 flex items-center justify-between mb-3">
                <div>
                    <h3 class="text-sm font-bold lowercase tracking-tight theme-text">{{ $nextMilestone->title }}</h3>
                    <p class="text-[9px] opacity-30 theme-text lowercase">{{ $nextMilestone->event_date->format('M d, Y') }}</p>
                </div>
                <div class="w-8 h-8 theme-card rounded-full shadow-sm flex items-center justify-center theme-accent border theme-border">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z"></path></svg>
                </div>
            </div>

            <div class="relative h-1.5 w-full bg-white/10 rounded-full overflow-hidden">
                <div class="absolute inset-y-0 left-0 theme-accent-bg transition-all duration-1000 ease-out rounded-full"
                     style="width: {{ $milestoneProgress }}%"></div>
            </div>
        </div>
    </div>
    @endif

    {{-- Section: Our Wishlist --}}
    <div class="theme-card border theme-border rounded-3xl p-4 shadow-sm space-y-4">
        <div class="flex items-center justify-between px-1">
            <h2 class="text-[10px] font-bold lowercase tracking-tight theme-text opacity-50">wishlist</h2>
            <span class="text-[9px] font-bold opacity-30 theme-text uppercase tracking-widest">{{ $wishlistItems->where('is_completed', true)->count() }} goals finished</span>
        </div>

        <form wire:submit.prevent="addToWishlist" class="relative group">
            <input wire:model="newWishlistTitle" placeholder="add a goal..."
                class="w-full bg-white/5 border theme-border rounded-xl pl-4 pr-10 py-2 text-[11px] focus:ring-brand-200 placeholder:text-gray-500 lowercase theme-text">
            <button type="submit" class="absolute right-1.5 top-1/2 -translate-y-1/2 p-1 theme-accent-bg text-white rounded-lg hover:scale-105 active:scale-95 transition-all">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            </button>
        </form>

        <div class="space-y-1.5 max-h-48 overflow-y-auto scrollbar-hide">
            @forelse($wishlistItems as $item)
                <div class="flex items-center gap-3 p-3 bg-white/5 rounded-xl group hover:bg-white/10 border theme-border transition-all">
                    <button wire:click="toggleWishlist({{ $item->id }})" 
                            class="shrink-0 w-5 h-5 rounded-full border flex items-center justify-center transition-all {{ $item->is_completed ? 'bg-green-500 border-green-500 text-white' : 'theme-border bg-transparent' }}">
                        @if($item->is_completed)
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        @endif
                    </button>
                    <div class="flex-1">
                        <p class="text-[11px] font-bold lowercase transition-all {{ $item->is_completed ? 'opacity-20 line-through' : 'opacity-70' }} theme-text">
                            {{ $item->title }}
                        </p>
                    </div>
                    <button wire:click="deleteWishlist({{ $item->id }})" class="opacity-0 group-hover:opacity-100 theme-text opacity-30 hover:opacity-100 hover:text-red-400 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            @empty
                <div class="py-6 text-center">
                    <p class="text-[9px] opacity-20 theme-text lowercase italic">dreaming together!</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-2 gap-3">
        <div class="theme-card border theme-border rounded-2xl p-4 text-center">
            <p class="text-lg font-bold theme-text">{{ App\Models\Relationship::formatNumber($stats['total_memories']) }}</p>
            <p class="text-[8px] opacity-30 theme-text uppercase tracking-widest font-bold">posts</p>
        </div>
        <div class="theme-card border theme-border rounded-2xl p-4 text-center">
            <p class="text-lg font-bold theme-text">{{ App\Models\Relationship::formatNumber($stats['total_photos']) }}</p>
            <p class="text-[8px] opacity-30 theme-text uppercase tracking-widest font-bold">photos</p>
        </div>
    </div>
</div>