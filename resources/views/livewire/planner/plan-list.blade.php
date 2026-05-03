<div class="max-w-6xl mx-auto px-1.5 sm:px-4 pt-6 pb-32 space-y-8">
    {{-- Header --}}
    <div class="flex items-center justify-between px-3">
        <div>
            <h2 class="text-xs font-bold theme-text opacity-30 lowercase tracking-widest">our journey</h2>
            <h1 class="text-3xl font-bold theme-text lowercase tracking-tighter">Planner</h1>
        </div>
        <a href="{{ route('planner.create') }}" wire:navigate 
           class="w-12 h-12 rounded-full theme-accent-bg text-white flex items-center justify-center shadow-xl shadow-brand-500/20 active:scale-90 transition-all hover:scale-110">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        </a>
    </div>

    {{-- Plans Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($plans as $plan)
            <a href="{{ route('planner.detail', $plan->id) }}" wire:navigate 
               class="theme-card border theme-border rounded-[2rem] overflow-hidden shadow-sm group active:scale-[0.98] transition-all mx-1.5 sm:mx-0">
                @if($plan->cover_image)
                    <div class="h-32 w-full relative">
                        <img src="{{ Storage::disk('public')->url($plan->cover_image) }}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    </div>
                @else
                    <div class="h-24 w-full bg-current/5 flex items-center justify-center">
                        <svg class="w-10 h-10 opacity-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                    </div>
                @endif

                <div class="p-6 space-y-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-lg font-bold lowercase tracking-tight theme-text">{{ $plan->title }}</h3>
                            <p class="text-[10px] opacity-40 theme-text lowercase">
                                {{ $plan->target_date ? $plan->target_date->format('M d, Y') : 'no date set' }}
                            </p>
                        </div>
                        <span class="text-[9px] font-bold px-2 py-1 rounded-full bg-brand-500/10 theme-accent uppercase tracking-widest">{{ $plan->status }}</span>
                    </div>

                    {{-- Budget Progress --}}
                    @if($plan->total_budget > 0)
                        <div class="space-y-1.5">
                            <div class="flex justify-between text-[9px] font-bold uppercase tracking-widest opacity-40 theme-text">
                                <span>budget status</span>
                                <span>{{ round($plan->budget_progress) }}%</span>
                            </div>
                            <div class="h-1.5 w-full bg-current/5 rounded-full overflow-hidden">
                                <div class="h-full theme-accent-bg transition-all duration-1000" style="width: {{ $plan->budget_progress }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>
            </a>
        @empty
            <div class="col-span-full py-24 text-center space-y-6">
                <div class="w-24 h-24 bg-current/5 rounded-full mx-auto flex items-center justify-center opacity-20">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <div class="space-y-2">
                    <p class="text-base font-bold theme-text lowercase">ready to plan your next adventure?</p>
                    <p class="text-[11px] opacity-40 theme-text lowercase italic">start creating your milestones together.</p>
                </div>
                <a href="{{ route('planner.create') }}" class="inline-block text-[11px] font-bold theme-accent uppercase tracking-widest border border-brand-500/30 rounded-full px-8 py-3 hover:bg-brand-500/5 transition-all">create first plan</a>
            </div>
        @endforelse
    </div>
</div>
