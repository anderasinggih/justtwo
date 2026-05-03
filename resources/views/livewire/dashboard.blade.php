<div class="max-w-5xl mx-auto px-1.5 sm:px-4 pt-4 space-y-5 pb-32" wire:poll.30s.visible>
    {{-- Minimalist Header --}}
    <div class="flex items-center justify-between mb-2 px-1">
        <div>
            <h2 class="text-[10px] font-bold theme-text opacity-30 lowercase tracking-widest">our space</h2>
            <h1 class="text-xl font-bold theme-text lowercase tracking-tighter">{{ Auth::user()->relationship->name }}</h1>
        </div>
        <div class="w-10 h-10 rounded-full bg-white/5 border theme-border flex items-center justify-center shadow-inner">
            <svg class="w-5 h-5 theme-accent" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
        </div>
    </div>

    {{-- Hero Section: Together Timer --}}
    <div class="relative overflow-hidden theme-card border theme-border rounded-3xl p-5 shadow-sm text-center"
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
        
        <div class="relative z-10 space-y-4">
            <div class="flex justify-center items-center gap-5 md:gap-10">
                <div class="flex flex-col items-center group">
                    <div class="relative">
                        <img src="{{ Auth::user()->profile_photo_url }}" class="w-12 h-12 md:w-14 md:h-14 rounded-full border-2 theme-border shadow-sm object-cover group-hover:scale-105 transition-transform">
                    </div>
                    <p class="text-[8px] font-bold mt-1.5 lowercase theme-text opacity-50">{{ Auth::user()->name }}</p>
                </div>
                
                <div class="w-8 h-8 bg-brand-500/5 rounded-full flex items-center justify-center theme-accent animate-pulse">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                </div>

                <div class="flex flex-col items-center group">
                    @if($partner)
                        <div class="relative">
                            <img src="{{ $partner->profile_photo_url }}" class="w-12 h-12 md:w-14 md:h-14 rounded-full border-2 theme-border shadow-sm object-cover group-hover:scale-105 transition-transform">
                        </div>
                        <p class="text-[8px] font-bold mt-1.5 lowercase theme-text opacity-50">{{ $partner->name }}</p>
                    @else
                        <div class="w-12 h-12 rounded-full border-2 border-dashed theme-border flex items-center justify-center bg-white/5">
                            <p class="text-[7px] opacity-30 theme-text lowercase">waiting</p>
                        </div>
                    @endif
                </div>
            </div>

            <div>
                <div class="flex items-center justify-center gap-2 flex-wrap">
                    <div class="text-center">
                        <h1 class="text-3xl md:text-4xl font-bold tracking-tighter theme-text" x-text="days"></h1>
                        <p class="text-[7px] uppercase tracking-widest opacity-30 font-bold">days</p>
                    </div>
                    <div class="text-center">
                        <h1 class="text-3xl md:text-4xl font-bold tracking-tighter theme-text" x-text="hours"></h1>
                        <p class="text-[7px] uppercase tracking-widest opacity-30 font-bold">hours</p>
                    </div>
                    <div class="text-center">
                        <h1 class="text-3xl md:text-4xl font-bold tracking-tighter theme-text" x-text="mins"></h1>
                        <p class="text-[7px] uppercase tracking-widest opacity-30 font-bold">mins</p>
                    </div>
                    <div class="text-center">
                        <h1 class="text-3xl md:text-4xl font-bold tracking-tighter theme-accent" x-text="secs"></h1>
                        <p class="text-[7px] uppercase tracking-widest opacity-30 font-bold theme-accent">secs</p>
                    </div>
                </div>
                <p class="text-[10px] opacity-40 theme-text lowercase tracking-wide mt-2">
                    since {{ $togetherStats['anniversary_formatted'] }}
                </p>
            </div>
        </div>
    </div>

    {{-- Grid 1: Savings & Plans --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Section: Shared Savings --}}
        <div class="space-y-2.5">
            <div class="flex items-center justify-between px-2">
                <h2 class="text-[9px] font-bold lowercase tracking-tight theme-text opacity-50">shared savings</h2>
                <button wire:click="$set('showAddSavingModal', true)" class="text-[9px] font-bold theme-accent uppercase tracking-widest hover:opacity-70">+ add goal</button>
            </div>

            @forelse($savings as $saving)
                <div class="theme-card border theme-border rounded-2xl p-4 shadow-sm space-y-4 relative overflow-hidden group">
                    <div class="absolute -right-8 -top-8 w-24 h-24 theme-accent-bg opacity-5 rounded-full blur-2xl group-hover:opacity-10 transition-opacity"></div>
                    
                    <div class="flex items-center justify-between relative z-10">
                        <div>
                            <h3 class="text-sm font-bold lowercase tracking-tight theme-text">{{ $saving->title }}</h3>
                            <p class="text-[9px] opacity-40 theme-text">target: <span class="font-bold">Rp {{ number_format($saving->target_amount, 0, ',', '.') }}</span></p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold theme-accent tracking-tighter">Rp {{ number_format($saving->current_amount, 0, ',', '.') }}</p>
                            <p class="text-[8px] opacity-30 theme-text font-bold uppercase tracking-widest">{{ round($saving->progress) }}% saved</p>
                        </div>
                    </div>

                    <div class="relative h-2 w-full bg-white/5 rounded-full overflow-hidden border theme-border">
                        <div class="absolute inset-y-0 left-0 theme-accent-bg transition-all duration-1000 ease-out rounded-full shadow-[0_0_10px_rgba(var(--accent-color),0.4)]"
                             style="width: {{ $saving->progress }}%"></div>
                    </div>

                    <div class="flex items-center gap-2 relative z-10">
                        <div class="relative flex-1">
                            <input type="number" wire:model="addAmount" placeholder="amount..."
                                   class="w-full bg-white/5 border theme-border rounded-xl pl-3 pr-8 py-2 text-[10px] focus:ring-brand-200 placeholder:text-gray-500 lowercase theme-text">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[8px] opacity-30 font-bold uppercase">rb</span>
                        </div>
                        <button wire:click="deposit({{ $saving->id }})" 
                                class="px-4 py-2 theme-accent-bg text-white rounded-xl text-[10px] font-bold hover:scale-105 active:scale-95 transition-all shadow-md shadow-brand-500/20">
                            save
                        </button>
                    </div>
                </div>
            @empty
                <div class="theme-card border border-dashed theme-border rounded-2xl p-6 text-center space-y-2 opacity-50">
                    <p class="text-[9px] theme-text lowercase italic">no goals yet.</p>
                </div>
            @endforelse
        </div>

        {{-- Section: Latest Plan --}}
        <div class="space-y-2.5">
            @if($latestPlan)
                <div class="flex items-center justify-between px-2">
                    <h2 class="text-[9px] font-bold lowercase tracking-tight theme-text opacity-50">latest plan</h2>
                    <a href="{{ route('planner') }}" wire:navigate class="text-[9px] font-bold theme-accent uppercase tracking-widest hover:opacity-70">view all</a>
                </div>

                <a href="{{ route('planner.detail', $latestPlan->id) }}" wire:navigate 
                   class="theme-card border theme-border rounded-2xl p-4 shadow-sm flex items-center gap-4 group active:scale-95 transition-all h-[calc(100%-1.5rem)]">
                    <div class="w-14 h-14 rounded-xl overflow-hidden bg-current/5 shrink-0 border theme-border">
                        @if($latestPlan->cover_image)
                            <img src="{{ Storage::disk('public')->url($latestPlan->cover_image) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-5 h-5 opacity-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0 space-y-1.5">
                        <h4 class="text-sm font-bold theme-text tracking-tight lowercase truncate">{{ $latestPlan->title }}</h4>
                        <div class="space-y-1">
                            <div class="h-1 bg-current/5 rounded-full overflow-hidden">
                                <div class="h-full theme-accent-bg" style="width: {{ $latestPlan->budget_progress }}%"></div>
                            </div>
                            <span class="text-[8px] font-bold theme-accent">{{ round($latestPlan->budget_progress) }}% used</span>
                        </div>
                    </div>
                </a>
            @endif
        </div>
    </div>

    {{-- Section: Our Next Big Event --}}
    <div class="space-y-2.5">
        @if($nextMilestone)
            <div class="flex items-center justify-between px-2">
                <h2 class="text-[9px] font-bold lowercase tracking-tight theme-text opacity-50">next event</h2>
                <span class="text-[8px] font-bold theme-accent uppercase tracking-widest px-2 py-0.5 bg-brand-500/10 rounded-full">{{ $daysRemainingFormatted }}</span>
            </div>

            <div class="relative p-4 theme-card border theme-border rounded-2xl overflow-hidden group shadow-sm">
                <div class="relative z-10 flex items-center justify-between mb-3">
                    <div>
                        <h3 class="text-sm font-bold lowercase tracking-tight theme-text">{{ $nextMilestone->title }}</h3>
                        <p class="text-[9px] opacity-30 theme-text lowercase">{{ $nextMilestone->event_date->format('M d, Y') }}</p>
                    </div>
                    <div class="w-8 h-8 theme-card rounded-xl shadow-sm flex items-center justify-center theme-accent border theme-border">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z"></path></svg>
                    </div>
                </div>

                <div class="relative h-1.5 w-full bg-white/5 rounded-full overflow-hidden border theme-border">
                    <div class="absolute inset-y-0 left-0 theme-accent-bg transition-all duration-1000 ease-out rounded-full"
                         style="width: {{ $milestoneProgress }}%"></div>
                </div>
            </div>
        @endif
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-2 gap-3">
        <div class="theme-card border theme-border rounded-2xl p-4 text-center group hover:bg-white/5 transition-all">
            <p class="text-xl font-bold theme-text tracking-tighter">{{ App\Models\Relationship::formatNumber($stats['total_memories']) }}</p>
            <p class="text-[8px] opacity-30 theme-text uppercase tracking-widest font-bold">memories</p>
        </div>
        <div class="theme-card border theme-border rounded-2xl p-4 text-center group hover:bg-white/5 transition-all">
            <p class="text-xl font-bold theme-text tracking-tighter">{{ App\Models\Relationship::formatNumber($stats['total_photos']) }}</p>
            <p class="text-[8px] opacity-30 theme-text uppercase tracking-widest font-bold">photos</p>
        </div>
    </div>

    {{-- Add Saving Modal --}}
    @if($showAddSavingModal)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/70 backdrop-blur-md animate-reveal">
        <div class="theme-card border theme-border rounded-3xl w-full max-w-xs p-6 shadow-2xl space-y-6">
            <div class="text-center space-y-1">
                <h3 class="text-lg font-bold lowercase tracking-tight theme-text">New Saving Goal</h3>
                <p class="text-[10px] opacity-40 theme-text lowercase italic">what are we dreaming of?</p>
            </div>

            <form wire:submit.prevent="addSaving" class="space-y-4">
                <div class="space-y-1.5">
                    <label class="text-[9px] font-bold opacity-30 uppercase tracking-widest pl-2">goal name</label>
                    <input wire:model="newSavingTitle" placeholder="e.g. bali vacation"
                           class="w-full bg-white/5 border theme-border rounded-xl px-4 py-3 text-sm focus:ring-brand-500/20 theme-text lowercase">
                </div>

                <div class="space-y-1.5">
                    <label class="text-[9px] font-bold opacity-30 uppercase tracking-widest pl-2">target amount</label>
                    <input type="number" wire:model="newSavingTarget" placeholder="e.g. 5000000"
                           class="w-full bg-white/5 border theme-border rounded-xl px-4 py-3 text-sm focus:ring-brand-500/20 theme-text">
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" wire:click="$set('showAddSavingModal', false)" 
                            class="flex-1 px-4 py-3 bg-white/5 theme-text rounded-xl text-xs font-bold hover:bg-white/10 transition-all">cancel</button>
                    <button type="submit" 
                            class="flex-1 px-4 py-3 theme-accent-bg text-white rounded-xl text-xs font-bold hover:scale-105 active:scale-95 transition-all shadow-lg shadow-brand-500/30">create</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>