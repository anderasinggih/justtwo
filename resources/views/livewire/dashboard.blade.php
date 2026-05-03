<div class="max-w-4xl mx-auto px-1.5 sm:px-4 pt-4 space-y-8 pb-32" wire:poll.30s.visible>
    {{-- Minimalist Header --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-xs font-bold theme-text opacity-30 lowercase tracking-widest">our space</h2>
            <h1 class="text-2xl font-bold theme-text lowercase tracking-tighter">{{ Auth::user()->relationship->name }}</h1>
        </div>
        <div class="w-12 h-12 rounded-full bg-white/5 border theme-border flex items-center justify-center shadow-inner">
            <svg class="w-6 h-6 theme-accent" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
        </div>
    </div>

    {{-- Hero Section: Together Timer --}}
    <div class="relative overflow-hidden theme-card border theme-border rounded-[2.5rem] p-8 shadow-sm text-center"
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
        
        <div class="absolute -right-16 -top-16 w-48 h-48 bg-brand-500/10 rounded-full blur-3xl opacity-40"></div>
        <div class="absolute -left-16 -bottom-16 w-48 h-48 theme-accent-bg opacity-10 rounded-full blur-3xl"></div>
        
        <div class="relative z-10 space-y-6">
            <div class="flex justify-center items-center gap-6 md:gap-12">
                <div class="flex flex-col items-center group">
                    <div class="relative">
                        <img src="{{ Auth::user()->profile_photo_url }}" class="w-16 h-16 md:w-20 md:h-20 rounded-full border-2 theme-border shadow-md object-cover group-hover:scale-105 transition-transform">
                        <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 border-2 border-white rounded-full"></div>
                    </div>
                    <p class="text-[10px] font-bold mt-2 lowercase theme-text opacity-50">{{ Auth::user()->name }}</p>
                </div>
                
                <div class="w-10 h-10 bg-brand-500/5 rounded-full flex items-center justify-center theme-accent animate-pulse">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                </div>

                <div class="flex flex-col items-center group">
                    @if($partner)
                        <div class="relative">
                            <img src="{{ $partner->profile_photo_url }}" class="w-16 h-16 md:w-20 md:h-20 rounded-full border-2 theme-border shadow-md object-cover group-hover:scale-105 transition-transform">
                            <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 border-2 border-white rounded-full"></div>
                        </div>
                        <p class="text-[10px] font-bold mt-2 lowercase theme-text opacity-50">{{ $partner->name }}</p>
                    @else
                        <div class="w-16 h-16 md:w-20 md:h-20 rounded-full border-2 border-dashed theme-border flex items-center justify-center bg-white/5">
                            <p class="text-[8px] opacity-30 theme-text lowercase">waiting...</p>
                        </div>
                    @endif
                </div>
            </div>

            <div>
                <div class="flex items-center justify-center gap-3 flex-wrap">
                    <div class="text-center">
                        <h1 class="text-4xl md:text-5xl font-bold tracking-tighter theme-text" x-text="days"></h1>
                        <p class="text-[9px] uppercase tracking-widest opacity-30 font-bold">days</p>
                    </div>
                    <div class="text-center">
                        <h1 class="text-4xl md:text-5xl font-bold tracking-tighter theme-text" x-text="hours"></h1>
                        <p class="text-[9px] uppercase tracking-widest opacity-30 font-bold">hours</p>
                    </div>
                    <div class="text-center">
                        <h1 class="text-4xl md:text-5xl font-bold tracking-tighter theme-text" x-text="mins"></h1>
                        <p class="text-[9px] uppercase tracking-widest opacity-30 font-bold">mins</p>
                    </div>
                    <div class="text-center">
                        <h1 class="text-4xl md:text-5xl font-bold tracking-tighter theme-accent" x-text="secs"></h1>
                        <p class="text-[9px] uppercase tracking-widest opacity-30 font-bold theme-accent">secs</p>
                    </div>
                </div>
                <p class="text-[11px] opacity-40 theme-text lowercase tracking-wide mt-4">
                    since {{ $togetherStats['anniversary_formatted'] }}
                </p>
            </div>
        </div>
    </div>

    {{-- Grid 1: Savings & Plans --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Section: Shared Savings --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between px-2">
                <h2 class="text-[11px] font-bold lowercase tracking-tight theme-text opacity-50">shared savings</h2>
                <button wire:click="$set('showAddSavingModal', true)" class="text-[10px] font-bold theme-accent uppercase tracking-widest hover:opacity-70 transition-opacity">+ add goal</button>
            </div>

            @forelse($savings as $saving)
                <div class="theme-card border theme-border rounded-3xl p-6 shadow-sm space-y-5 relative overflow-hidden group">
                    <div class="absolute -right-8 -top-8 w-24 h-24 theme-accent-bg opacity-5 rounded-full blur-2xl group-hover:opacity-10 transition-opacity"></div>
                    
                    <div class="flex items-center justify-between relative z-10">
                        <div>
                            <h3 class="text-base font-bold lowercase tracking-tight theme-text">{{ $saving->title }}</h3>
                            <p class="text-[11px] opacity-40 theme-text">target: <span class="font-bold">Rp {{ number_format($saving->target_amount, 0, ',', '.') }}</span></p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold theme-accent tracking-tighter">Rp {{ number_format($saving->current_amount, 0, ',', '.') }}</p>
                            <p class="text-[10px] opacity-30 theme-text font-bold uppercase tracking-widest">{{ round($saving->progress) }}% saved</p>
                        </div>
                    </div>

                    <div class="relative h-2.5 w-full bg-white/5 rounded-full overflow-hidden border theme-border">
                        <div class="absolute inset-y-0 left-0 theme-accent-bg transition-all duration-1000 ease-out rounded-full shadow-[0_0_15px_rgba(var(--accent-color),0.4)]"
                             style="width: {{ $saving->progress }}%"></div>
                    </div>

                    <div class="flex items-center gap-3 pt-1 relative z-10">
                        <div class="relative flex-1">
                            <input type="number" wire:model="addAmount" placeholder="amount..."
                                   class="w-full bg-white/5 border theme-border rounded-2xl pl-4 pr-10 py-2.5 text-xs focus:ring-brand-200 placeholder:text-gray-500 lowercase theme-text">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[9px] opacity-30 font-bold uppercase">rb</span>
                        </div>
                        <button wire:click="deposit({{ $saving->id }})" 
                                class="px-6 py-2.5 theme-accent-bg text-white rounded-2xl text-xs font-bold hover:scale-105 active:scale-95 transition-all shadow-lg shadow-brand-500/20">
                            save
                        </button>
                    </div>
                </div>
            @empty
                <div class="theme-card border border-dashed theme-border rounded-[2rem] p-10 text-center space-y-4">
                    <div class="w-16 h-16 bg-current/5 rounded-full mx-auto flex items-center justify-center opacity-20">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="text-[11px] opacity-30 theme-text lowercase italic leading-relaxed">no saving goals yet. let's plan something big together!</p>
                </div>
            @endforelse
        </div>

        {{-- Section: Latest Plan --}}
        <div class="space-y-4">
            @if($latestPlan)
                <div class="flex items-center justify-between px-2">
                    <h2 class="text-[11px] font-bold lowercase tracking-tight theme-text opacity-50">latest plan</h2>
                    <a href="{{ route('planner') }}" wire:navigate class="text-[10px] font-bold theme-accent uppercase tracking-widest hover:opacity-70 transition-opacity">view all</a>
                </div>

                <a href="{{ route('planner.detail', $latestPlan->id) }}" wire:navigate 
                   class="theme-card border theme-border rounded-3xl p-5 shadow-sm flex items-center gap-5 group active:scale-95 transition-all h-[calc(100%-2rem)]">
                    <div class="w-20 h-20 rounded-2xl overflow-hidden bg-current/5 shrink-0 border theme-border shadow-sm">
                        @if($latestPlan->cover_image)
                            <img src="{{ Storage::disk('public')->url($latestPlan->cover_image) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-8 h-8 opacity-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0 space-y-2">
                        <h4 class="text-base font-bold theme-text tracking-tight lowercase truncate">{{ $latestPlan->title }}</h4>
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold theme-accent uppercase tracking-widest">budget</span>
                                <span class="text-[10px] font-bold theme-accent">{{ round($latestPlan->budget_progress) }}%</span>
                            </div>
                            <div class="h-1.5 bg-current/5 rounded-full overflow-hidden">
                                <div class="h-full theme-accent-bg transition-all duration-1000" style="width: {{ $latestPlan->budget_progress }}%"></div>
                            </div>
                        </div>
                    </div>
                </a>
            @endif
        </div>
    </div>

    {{-- Grid 2: Events & Wishlist --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Section: Our Next Big Event --}}
        <div class="space-y-4">
            @if($nextMilestone)
                <div class="flex items-center justify-between px-2">
                    <h2 class="text-[11px] font-bold lowercase tracking-tight theme-text opacity-50">next event</h2>
                    <span class="text-[10px] font-bold theme-accent uppercase tracking-widest px-3 py-1 bg-brand-500/10 rounded-full">{{ $daysRemainingFormatted }}</span>
                </div>

                <div class="relative p-6 theme-card border theme-border rounded-3xl overflow-hidden group shadow-sm">
                    <div class="relative z-10 flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-base font-bold lowercase tracking-tight theme-text">{{ $nextMilestone->title }}</h3>
                            <p class="text-[11px] opacity-40 theme-text lowercase font-medium">{{ $nextMilestone->event_date->format('M d, Y') }}</p>
                        </div>
                        <div class="w-12 h-12 theme-card rounded-2xl shadow-sm flex items-center justify-center theme-accent border theme-border group-hover:rotate-12 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z"></path></svg>
                        </div>
                    </div>

                    <div class="relative h-2 w-full bg-white/5 rounded-full overflow-hidden border theme-border">
                        <div class="absolute inset-y-0 left-0 theme-accent-bg transition-all duration-1000 ease-out rounded-full"
                             style="width: {{ $milestoneProgress }}%"></div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Section: Our Wishlist --}}
        <div class="theme-card border theme-border rounded-[2.5rem] p-6 shadow-sm space-y-5">
            <div class="flex items-center justify-between px-1">
                <h2 class="text-[11px] font-bold lowercase tracking-tight theme-text opacity-50">wishlist</h2>
                <span class="text-[10px] font-bold opacity-30 theme-text uppercase tracking-widest">{{ $wishlistItems->where('is_completed', true)->count() }} finished</span>
            </div>

            <form wire:submit.prevent="addToWishlist" class="relative group">
                <input wire:model="newWishlistTitle" placeholder="add a goal..."
                    class="w-full bg-white/5 border theme-border rounded-2xl pl-5 pr-12 py-3 text-xs focus:ring-brand-200 placeholder:text-gray-500 lowercase theme-text">
                <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 p-2 theme-accent-bg text-white rounded-xl hover:scale-105 active:scale-95 transition-all shadow-lg shadow-brand-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </button>
            </form>

            <div class="space-y-2 max-h-56 overflow-y-auto scrollbar-hide pr-1">
                @forelse($wishlistItems as $item)
                    <div class="flex items-center gap-4 p-4 bg-white/5 rounded-2xl group hover:bg-white/10 border theme-border transition-all">
                        <button wire:click="toggleWishlist({{ $item->id }})" 
                                class="shrink-0 w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all {{ $item->is_completed ? 'bg-green-500 border-green-500 text-white' : 'theme-border bg-transparent' }}">
                            @if($item->is_completed)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            @endif
                        </button>
                        <div class="flex-1">
                            <p class="text-xs font-bold lowercase transition-all {{ $item->is_completed ? 'opacity-20 line-through' : 'opacity-80' }} theme-text">
                                {{ $item->title }}
                            </p>
                        </div>
                        <button wire:click="deleteWishlist({{ $item->id }})" class="opacity-0 group-hover:opacity-100 theme-text opacity-30 hover:opacity-100 hover:text-red-400 transition-all p-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                @empty
                    <div class="py-10 text-center">
                        <p class="text-[11px] opacity-20 theme-text lowercase italic">dreaming together!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="theme-card border theme-border rounded-[2rem] p-6 text-center group hover:bg-white/5 transition-all">
            <p class="text-3xl font-bold theme-text tracking-tighter">{{ App\Models\Relationship::formatNumber($stats['total_memories']) }}</p>
            <p class="text-[9px] opacity-30 theme-text uppercase tracking-widest font-bold mt-1">memories</p>
        </div>
        <div class="theme-card border theme-border rounded-[2rem] p-6 text-center group hover:bg-white/5 transition-all">
            <p class="text-3xl font-bold theme-text tracking-tighter">{{ App\Models\Relationship::formatNumber($stats['total_photos']) }}</p>
            <p class="text-[9px] opacity-30 theme-text uppercase tracking-widest font-bold mt-1">photos</p>
        </div>
    </div>

    {{-- Add Saving Modal --}}
    @if($showAddSavingModal)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/70 backdrop-blur-md animate-reveal">
        <div class="theme-card border theme-border rounded-[3rem] w-full max-w-sm p-10 shadow-2xl space-y-8">
            <div class="text-center space-y-2">
                <h3 class="text-xl font-bold lowercase tracking-tight theme-text">New Saving Goal</h3>
                <p class="text-[11px] opacity-40 theme-text lowercase italic">what are we dreaming of today?</p>
            </div>

            <form wire:submit.prevent="addSaving" class="space-y-5">
                <div class="space-y-2">
                    <label class="text-[10px] font-bold opacity-30 uppercase tracking-widest pl-2">goal name</label>
                    <input wire:model="newSavingTitle" placeholder="e.g. bali vacation"
                           class="w-full bg-white/5 border theme-border rounded-2xl px-5 py-4 text-sm focus:ring-brand-500/20 theme-text lowercase">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold opacity-30 uppercase tracking-widest pl-2">target amount</label>
                    <input type="number" wire:model="newSavingTarget" placeholder="e.g. 5000000"
                           class="w-full bg-white/5 border theme-border rounded-2xl px-5 py-4 text-sm focus:ring-brand-500/20 theme-text">
                </div>

                <div class="flex gap-4 pt-6">
                    <button type="button" wire:click="$set('showAddSavingModal', false)" 
                            class="flex-1 px-6 py-4 bg-white/5 theme-text rounded-[1.5rem] text-xs font-bold hover:bg-white/10 transition-all">cancel</button>
                    <button type="submit" 
                            class="flex-1 px-6 py-4 theme-accent-bg text-white rounded-[1.5rem] text-xs font-bold hover:scale-105 active:scale-95 transition-all shadow-2xl shadow-brand-500/30">create</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>