<div class="max-w-5xl mx-auto px-1.5 sm:px-4 pt-4 space-y-5 pb-32" wire:poll.30s.visible>
    {{-- Minimalist Header --}}
    <div class="flex items-center justify-between mb-2 px-1">
        <div>
            <h2 class="text-[10px] font-bold theme-text opacity-30 lowercase tracking-widest leading-none mb-1">welcome back</h2>
            <h1 class="text-xl font-bold theme-text lowercase tracking-tighter">Together</h1>
        </div>
        <div class="flex -space-x-2">
            @foreach($partners as $partner)
                <div class="w-8 h-8 rounded-full border-2 theme-border overflow-hidden bg-current/5">
                    @if($partner->profile_photo_path)
                        <img src="{{ Storage::disk('public')->url($partner->profile_photo_path) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center theme-text opacity-20 text-[10px] font-bold uppercase">
                            {{ substr($partner->name, 0, 1) }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Anniversary Timer Card --}}
    <div class="theme-card border theme-border rounded-[2.5rem] p-8 shadow-sm relative overflow-hidden group">
        <div class="absolute -right-16 -top-16 w-64 h-64 theme-accent-bg opacity-5 rounded-full blur-3xl group-hover:opacity-10 transition-opacity"></div>
        <div class="relative z-10 space-y-6">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-[10px] font-bold theme-text opacity-30 uppercase tracking-[0.2em] mb-2">Together Since</h2>
                    <p class="text-xs font-bold theme-text lowercase opacity-60">{{ $togetherStats['anniversary_formatted'] }}</p>
                </div>
                <div class="text-right">
                    <p class="text-4xl font-bold theme-text tracking-tighter leading-none">{{ $togetherStats['total_days'] }}</p>
                    <p class="text-[10px] font-bold theme-accent uppercase tracking-widest mt-1">days of love</p>
                </div>
            </div>

            {{-- Milestone Progress --}}
            @if($nextMilestone)
            <div class="space-y-3 pt-4 border-t theme-border">
                <div class="flex justify-between items-end">
                    <div>
                        <p class="text-[9px] font-bold theme-accent uppercase tracking-widest mb-1">{{ $nextMilestone->title }}</p>
                        <p class="text-[11px] font-bold theme-text lowercase opacity-40">{{ $daysRemainingFormatted }}</p>
                    </div>
                    <p class="text-[10px] font-bold theme-text opacity-30">{{ round($milestoneProgress) }}%</p>
                </div>
                <div class="h-1.5 w-full bg-current/5 rounded-full overflow-hidden">
                    <div class="h-full theme-accent-bg shadow-[0_0_15px_rgba(var(--accent-color),0.4)]" style="width: {{ $milestoneProgress }}%"></div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        {{-- Left Column: Savings & Events --}}
        <div class="lg:col-span-7 space-y-5">
            {{-- Shared Savings --}}
            <section class="space-y-3">
                <div class="flex items-center justify-between px-1">
                    <h3 class="text-[10px] font-bold theme-text opacity-30 uppercase tracking-widest">shared savings</h3>
                </div>
                <div class="grid grid-cols-1 gap-3">
                    @forelse($savings as $saving)
                        <div x-data="{ showAdd: false }" class="theme-card border theme-border rounded-[1.5rem] p-4 shadow-sm group hover:shadow-md transition-all">
                            <div @click="showAdd = !showAdd" class="cursor-pointer">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-brand-500/5 flex items-center justify-center theme-accent text-lg">
                                            {!! $saving->icon ?: '💰' !!}
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-bold theme-text lowercase tracking-tight">{{ $saving->title }}</h4>
                                            <p class="text-[9px] opacity-30 theme-text uppercase font-bold tracking-widest">target: Rp {{ number_format($saving->target_amount, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold theme-text tracking-tighter">Rp {{ number_format($saving->current_amount, 0, ',', '.') }}</p>
                                        <p class="text-[9px] theme-accent font-bold uppercase tracking-widest">{{ round($saving->progress) }}% saved</p>
                                    </div>
                                </div>
                                <div class="h-1.5 w-full bg-current/5 rounded-full overflow-hidden">
                                    <div class="h-full theme-accent-bg transition-all duration-1000" style="width: {{ $saving->progress }}%"></div>
                                </div>
                            </div>

                            {{-- Expandable Add Amount Form --}}
                            <div x-show="showAdd" x-collapse x-cloak class="mt-4 pt-4 border-t theme-border">
                                <div class="flex gap-2">
                                    <div class="relative flex-1">
                                        <input type="number" 
                                               wire:model="savingAmounts.{{ $saving->id }}"
                                               placeholder="amount..." 
                                               class="w-full bg-white/5 border theme-border rounded-xl px-4 py-2.5 text-xs theme-text focus:ring-brand-200">
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[9px] font-bold opacity-20 uppercase">RB</span>
                                    </div>
                                    <button wire:click="addSaving({{ $saving->id }})" 
                                            class="px-5 py-2.5 theme-accent-bg text-white rounded-xl text-[10px] font-bold shadow-lg shadow-brand-500/20 active:scale-95 transition-all">
                                        save
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-10 text-center theme-card border border-dashed theme-border rounded-[1.5rem] opacity-20">
                            <p class="text-xs lowercase italic">no saving goals yet.</p>
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Upcoming Events --}}
            <section class="space-y-3">
                <div class="flex items-center justify-between px-1">
                    <h3 class="text-[10px] font-bold theme-text opacity-30 uppercase tracking-widest">upcoming events</h3>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @forelse($upcomingEvents as $event)
                        <div class="theme-card border theme-border rounded-[1.2rem] p-3 shadow-sm text-center space-y-1 group">
                            <p class="text-[8px] font-bold theme-accent uppercase tracking-widest">{{ $event->date->format('M d') }}</p>
                            <h4 class="text-[11px] font-bold theme-text lowercase tracking-tight truncate">{{ $event->title }}</h4>
                            <p class="text-[8px] opacity-30 theme-text lowercase">{{ $event->date->diffForHumans() }}</p>
                        </div>
                    @empty
                        <div class="col-span-full py-6 text-center theme-card border border-dashed theme-border rounded-[1.2rem] opacity-20">
                            <p class="text-[9px] lowercase italic">no upcoming events.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>

        {{-- Right Column: Upcoming Plans --}}
        <div class="lg:col-span-5 space-y-5">
            <section class="space-y-3">
                <div class="flex items-center justify-between px-1">
                    <h3 class="text-[10px] font-bold theme-text opacity-30 uppercase tracking-widest">latest plans</h3>
                    <a href="{{ route('planner') }}" wire:navigate class="text-[9px] font-bold theme-accent uppercase tracking-widest hover:opacity-70 transition-opacity">view all</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 gap-3">
                    @forelse($upcomingPlans as $plan)
                        <a href="{{ route('planner.detail', $plan->id) }}" wire:navigate 
                           class="theme-card border theme-border rounded-[1.5rem] p-4 shadow-sm flex items-center gap-4 group hover:shadow-md transition-all">
                            <div class="w-12 h-12 shrink-0 rounded-2xl bg-brand-500/5 flex items-center justify-center theme-accent text-xl group-hover:scale-110 transition-transform">
                                {{ $plan->icon }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="text-sm font-bold theme-text lowercase tracking-tight truncate">{{ $plan->title }}</h4>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[9px] opacity-30 theme-text lowercase">{{ $plan->target_date ? $plan->target_date->format('M d') : 'anytime' }}</span>
                                    <span class="w-1 h-1 rounded-full bg-current opacity-10"></span>
                                    <span class="text-[9px] theme-accent font-bold uppercase tracking-widest">{{ round($plan->budget_progress) }}% ready</span>
                                </div>
                            </div>
                            <svg class="w-4 h-4 opacity-10 group-hover:opacity-100 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    @empty
                        <div class="py-12 text-center theme-card border border-dashed theme-border rounded-[1.5rem] opacity-20">
                            <p class="text-xs lowercase italic">no active plans.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</div>