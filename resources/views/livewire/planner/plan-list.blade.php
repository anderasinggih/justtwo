<div class="max-w-5xl mx-auto px-1.5 sm:px-4 pt-4 pb-32 space-y-4">
    {{-- Header --}}
    <div class="flex items-center justify-between px-2 mb-2">
        <div>
            <h2 class="text-[10px] font-bold theme-text opacity-30 lowercase tracking-widest">our journey</h2>
            <h1 class="text-xl font-bold theme-text lowercase tracking-tighter">Planner</h1>
        </div>
        <a href="{{ route('planner.create') }}" wire:navigate 
           class="w-10 h-10 rounded-full theme-accent-bg text-white flex items-center justify-center shadow-lg shadow-brand-500/20 active:scale-90 transition-all hover:scale-110">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        </a>
    </div>

    {{-- Plans Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        @forelse($plans as $plan)
            <a href="{{ route('planner.detail', $plan->id) }}" wire:navigate 
               class="theme-card border theme-border rounded-[1.5rem] overflow-hidden shadow-sm group active:scale-[0.98] transition-all mx-1 sm:mx-0">
                
                <div class="p-4 space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold lowercase tracking-tight theme-text truncate">{{ $plan->title }}</h3>
                            <p class="text-[9px] opacity-40 theme-text lowercase">
                                {{ $plan->target_date ? $plan->target_date->format('M d, Y') : 'no date set' }}
                            </p>
                        </div>
                        <span class="shrink-0 text-[8px] font-bold px-2 py-0.5 rounded-full bg-brand-500/10 theme-accent uppercase tracking-widest border border-brand-500/20">{{ $plan->status }}</span>
                    </div>

                    {{-- Budget Progress (Slim) --}}
                    @if($plan->total_budget > 0)
                        <div class="space-y-1">
                            <div class="flex justify-between text-[8px] font-bold uppercase tracking-widest opacity-40 theme-text">
                                <span>budget</span>
                                <span>{{ round($plan->budget_progress) }}%</span>
                            </div>
                            <div class="h-1 w-full bg-current/5 rounded-full overflow-hidden">
                                <div class="h-full theme-accent-bg transition-all duration-1000" style="width: {{ $plan->budget_progress }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>
            </a>
        @empty
            <div class="col-span-full py-16 text-center space-y-4">
                <div class="w-16 h-16 bg-current/5 rounded-full mx-auto flex items-center justify-center opacity-20">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <div class="space-y-1">
                    <p class="text-sm font-bold theme-text lowercase">ready for an adventure?</p>
                    <p class="text-[9px] opacity-40 theme-text lowercase italic">start creating your milestones.</p>
                </div>
                <a href="{{ route('planner.create') }}" class="inline-block text-[9px] font-bold theme-accent uppercase tracking-widest border border-brand-500/30 rounded-full px-6 py-2 hover:bg-brand-500/5 transition-all">create first plan</a>
            </div>
        @endforelse
    </div>
</div>
