<div x-data="{ tab: 'itinerary' }" class="pb-32 pt-4">
    {{-- Clean Header Section (Compact) --}}
    <div class="max-w-5xl mx-auto px-1.5 sm:px-4 mb-4">
        <div class="flex items-center justify-between mb-4 px-2">
            <a href="{{ route('planner') }}" wire:navigate class="p-2 rounded-full bg-current/5 theme-text hover:bg-current/10 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div class="flex gap-2">
                <a href="{{ route('planner.create', ['plan' => $plan->id]) }}" wire:navigate class="p-2 rounded-full bg-current/5 theme-text hover:bg-current/10 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                </a>
            </div>
        </div>

        <div class="px-2">
            <span class="text-[8px] font-bold px-2 py-0.5 rounded-full bg-brand-500/10 theme-accent uppercase tracking-widest mb-2 inline-block border border-brand-500/20">
                {{ $plan->status }}
            </span>
            <h1 class="text-3xl font-bold theme-text tracking-tighter lowercase leading-none">{{ $plan->title }}</h1>
            <p class="text-[10px] opacity-40 theme-text lowercase mt-1.5 font-medium">
                {{ $plan->target_date ? $plan->target_date->format('l, F d, Y') : 'date not set' }}
            </p>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-1.5 sm:px-4 mt-6 space-y-6">
        {{-- Linked Saving Goal (Magic Feature) --}}
        @if($plan->saving)
            <div class="theme-card border theme-border rounded-[1.5rem] p-5 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-8 -top-8 w-24 h-24 theme-accent-bg opacity-5 rounded-full blur-2xl group-hover:opacity-10 transition-opacity"></div>
                <div class="flex justify-between items-center mb-3 relative z-10">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-brand-500/5 flex items-center justify-center theme-accent">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold uppercase tracking-widest opacity-30 theme-text">linked saving goal</p>
                            <h3 class="text-sm font-bold theme-text lowercase tracking-tight">{{ $plan->saving->title }}</h3>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-bold theme-accent">Rp {{ number_format($plan->saving->current_amount, 0, ',', '.') }}</p>
                        <p class="text-[8px] opacity-30 theme-text font-bold uppercase tracking-widest">{{ round($plan->saving->progress) }}% saved</p>
                    </div>
                </div>
                <div class="h-2 w-full bg-current/5 rounded-full overflow-hidden border theme-border relative z-10">
                    <div class="h-full theme-accent-bg shadow-[0_0_10px_rgba(var(--accent-color),0.4)]" style="width: {{ $plan->saving->progress }}%"></div>
                </div>
            </div>
        @endif

        {{-- Custom Tab Navigation (Slim) --}}
        @if($plan->total_budget > 0)
            <div class="flex p-0.5 bg-current/5 rounded-xl max-w-[280px] mx-auto">
                <button @click="tab = 'itinerary'" 
                        :class="tab === 'itinerary' ? 'theme-card theme-text shadow-sm' : 'theme-text opacity-40'"
                        class="flex-1 py-1.5 text-[10px] font-bold uppercase tracking-widest rounded-lg transition-all">
                    itinerary
                </button>
                <button @click="tab = 'budget'" 
                        :class="tab === 'budget' ? 'theme-card theme-text shadow-sm' : 'theme-text opacity-40'"
                        class="flex-1 py-1.5 text-[10px] font-bold uppercase tracking-widest rounded-lg transition-all">
                    budget
                </button>
            </div>
        @endif

        {{-- Itinerary Tab Content --}}
        <div x-show="tab === 'itinerary'" x-transition class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
            {{-- Left Column: Form (Compact) --}}
            <div class="md:col-span-5 space-y-4 md:sticky md:top-20">
                <div class="theme-card border theme-border rounded-[1.5rem] p-5 shadow-sm space-y-4">
                    <h3 class="text-[9px] font-bold theme-text opacity-30 uppercase tracking-widest pl-1">add activity</h3>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="text-[8px] font-bold opacity-30 uppercase tracking-widest pl-1">date</label>
                            <input type="date" wire:model="itineraryDate" class="w-full bg-white/5 border theme-border rounded-lg px-3 py-2 text-[10px] theme-text focus:ring-brand-200">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[8px] font-bold opacity-30 uppercase tracking-widest pl-1">time</label>
                            <input type="time" wire:model="itineraryTime" class="w-full bg-white/5 border theme-border rounded-lg px-3 py-2 text-[10px] theme-text focus:ring-brand-200">
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[8px] font-bold opacity-30 uppercase tracking-widest pl-1">activity</label>
                        <input type="text" wire:model="itineraryActivity" placeholder="what's the plan?..." class="w-full bg-white/5 border theme-border rounded-lg px-3 py-2 text-[10px] theme-text lowercase focus:ring-brand-200">
                    </div>
                    <button wire:click="addItinerary" class="w-full py-3 theme-accent-bg text-white rounded-xl text-[10px] font-bold shadow-md shadow-brand-500/20 active:scale-95 transition-all">add activity</button>
                </div>
            </div>

            {{-- Right Column: List (Compact) --}}
            <div class="md:col-span-7 space-y-4">
                @forelse($plan->itineraries->groupBy(fn($i) => $i->event_date->format('Y-m-d')) as $date => $items)
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 px-1">
                            <span class="text-[9px] font-bold theme-accent uppercase tracking-widest shrink-0">{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</span>
                            <div class="h-[1px] flex-1 bg-current/5"></div>
                        </div>
                        
                        <div class="space-y-2">
                            @foreach($items as $item)
                                <div class="flex gap-3 group">
                                    <div class="flex flex-col items-center">
                                        <button wire:click="toggleItinerary({{ $item->id }})" 
                                                class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all mt-1.5 {{ $item->is_completed ? 'bg-green-500 border-green-500 text-white shadow-[0_0_8px_rgba(34,197,94,0.3)]' : 'theme-border bg-transparent opacity-30' }}">
                                            @if($item->is_completed)
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            @endif
                                        </button>
                                        <div class="w-[1px] flex-1 bg-current/5 my-1.5"></div>
                                    </div>
                                    <div class="flex-1 theme-card border theme-border rounded-xl p-3 relative hover:bg-white/5 transition-all shadow-sm {{ $item->is_completed ? 'opacity-30' : '' }}">
                                        <div class="flex justify-between items-start gap-2">
                                            <div class="space-y-0.5">
                                                <p class="text-[8px] font-bold theme-accent uppercase tracking-wider {{ $item->is_completed ? 'line-through' : '' }}">{{ $item->event_time ? \Carbon\Carbon::parse($item->event_time)->format('g:i A') : 'anytime' }}</p>
                                                <h4 class="text-sm font-bold theme-text tracking-tight lowercase {{ $item->is_completed ? 'line-through' : '' }} leading-tight">{{ $item->activity }}</h4>
                                            </div>
                                            <button wire:click="deleteItinerary({{ $item->id }})" class="opacity-0 group-hover:opacity-100 p-1 text-red-400/30 hover:text-red-500 transition-all">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center theme-card border border-dashed theme-border rounded-2xl opacity-20">
                        <p class="text-[10px] lowercase italic">no activities yet.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Budget Tab Content (Compact) --}}
        @if($plan->total_budget > 0)
        <div x-show="tab === 'budget'" x-transition class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
            {{-- Left Column: Overview & Form --}}
            <div class="md:col-span-5 space-y-4 md:sticky md:top-20">
                {{-- Budget Overview Card --}}
                <div class="theme-card border theme-border rounded-[1.5rem] p-6 shadow-sm space-y-4 relative overflow-hidden">
                    <div class="absolute -right-8 -top-8 w-24 h-24 theme-accent-bg opacity-5 rounded-full blur-2xl"></div>
                    
                    <div class="flex justify-between items-center relative z-10">
                        <div>
                            <p class="text-[9px] font-bold uppercase tracking-widest opacity-30 theme-text">spent</p>
                            <h2 class="text-2xl font-bold theme-text tracking-tighter">Rp {{ number_format($plan->spent_budget, 0, ',', '.') }}</h2>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] font-bold uppercase tracking-widest opacity-30 theme-text">left</p>
                            <p class="text-xs font-bold theme-accent">Rp {{ number_format($plan->total_budget - $plan->spent_budget, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="space-y-1.5 relative z-10">
                        <div class="flex justify-between text-[9px] font-bold lowercase opacity-30 theme-text">
                            <span>{{ round($plan->budget_progress) }}% used</span>
                            <span>limit: {{ number_format($plan->total_budget, 0, ',', '.') }}</span>
                        </div>
                        <div class="h-2 w-full bg-current/5 rounded-full overflow-hidden">
                            <div class="h-full theme-accent-bg transition-all duration-1000" style="width: {{ $plan->budget_progress }}%"></div>
                        </div>
                    </div>
                </div>

                {{-- Add Expense Form --}}
                <div class="theme-card border theme-border rounded-2xl p-5 shadow-sm space-y-4">
                    <h3 class="text-[9px] font-bold theme-text opacity-30 uppercase tracking-widest pl-1">add expense</h3>
                    <div class="space-y-3">
                        <input type="text" wire:model="expenseTitle" placeholder="description..." class="w-full bg-white/5 border theme-border rounded-lg px-3 py-2 text-[10px] theme-text lowercase focus:ring-brand-200">
                        <div class="grid grid-cols-2 gap-3">
                            <select wire:model="expenseCategory" class="w-full bg-white/5 border theme-border rounded-lg px-3 py-2 text-[10px] theme-text lowercase focus:ring-brand-200">
                                <option value="general">general</option>
                                <option value="food">food</option>
                                <option value="transport">transport</option>
                                <option value="lodging">stay</option>
                                <option value="fun">fun</option>
                            </select>
                            <input type="number" wire:model="expenseAmount" placeholder="amount..." class="w-full bg-white/5 border theme-border rounded-lg px-3 py-2 text-[10px] theme-text focus:ring-brand-200">
                        </div>
                    </div>
                    <button wire:click="addExpense" class="w-full py-3 theme-accent-bg text-white rounded-xl text-[10px] font-bold shadow-md shadow-brand-500/20 active:scale-95 transition-all">log expense</button>
                </div>
            </div>

            {{-- Right Column: Expense List --}}
            <div class="md:col-span-7 space-y-2">
                @forelse($plan->expenses as $expense)
                    <div class="theme-card border theme-border rounded-xl p-3 flex items-center justify-between group hover:bg-white/5 transition-all shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-current/5 flex items-center justify-center theme-accent text-xs">
                                @if($expense->category === 'food') <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>
                                @elseif($expense->category === 'transport') <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                @else <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8V7m0 1v8m0 0v1"></path></svg> @endif
                            </div>
                            <div>
                                <h4 class="text-[11px] font-bold theme-text lowercase truncate max-w-[120px]">{{ $expense->title }}</h4>
                                <p class="text-[8px] opacity-20 theme-text uppercase font-bold tracking-widest">{{ $expense->category }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <p class="text-[11px] font-bold theme-text">Rp {{ number_format($expense->amount, 0, ',', '.') }}</p>
                            <button wire:click="deleteExpense({{ $expense->id }})" class="opacity-0 group-hover:opacity-100 p-1 text-red-400/30 hover:text-red-500 transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center theme-card border border-dashed theme-border rounded-2xl opacity-20">
                        <p class="text-[10px] lowercase italic">no expenses.</p>
                    </div>
                @endforelse
            </div>
        </div>
        @endif
    </div>
</div>
