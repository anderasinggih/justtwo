<div x-data="{ tab: 'itinerary' }" class="pb-32 pt-8">
    {{-- Clean Header Section --}}
    <div class="max-w-5xl mx-auto px-1.5 sm:px-4 mb-8">
        <div class="flex items-center justify-between mb-6">
            <a href="{{ route('planner') }}" wire:navigate class="p-2.5 rounded-full bg-current/5 theme-text hover:bg-current/10 transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div class="flex gap-2">
                <a href="{{ route('planner.create', ['plan' => $plan->id]) }}" wire:navigate class="p-2.5 rounded-full bg-current/5 theme-text hover:bg-current/10 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                </a>
            </div>
        </div>

        <div>
            <span class="text-[9px] font-bold px-3 py-1 rounded-full bg-brand-500/10 theme-accent uppercase tracking-widest mb-3 inline-block">
                {{ $plan->status }}
            </span>
            <h1 class="text-4xl font-bold theme-text tracking-tighter lowercase">{{ $plan->title }}</h1>
            <p class="text-xs opacity-40 theme-text lowercase mt-2 font-medium">
                {{ $plan->target_date ? $plan->target_date->format('l, F d, Y') : 'date not set' }}
            </p>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-1.5 sm:px-4 mt-8 space-y-10">
        {{-- Custom Tab Navigation --}}
        <div class="flex p-1 bg-current/5 rounded-2xl max-w-sm mx-auto">
            <button @click="tab = 'itinerary'" 
                    :class="tab === 'itinerary' ? 'theme-card theme-text shadow-sm' : 'theme-text opacity-40'"
                    class="flex-1 py-2.5 text-[11px] font-bold uppercase tracking-widest rounded-xl transition-all">
                itinerary
            </button>
            <button @click="tab = 'budget'" 
                    :class="tab === 'budget' ? 'theme-card theme-text shadow-sm' : 'theme-text opacity-40'"
                    class="flex-1 py-2.5 text-[11px] font-bold uppercase tracking-widest rounded-xl transition-all">
                budget
            </button>
        </div>

        {{-- Itinerary Tab Content --}}
        <div x-show="tab === 'itinerary'" x-transition class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
            {{-- Left Column: Form --}}
            <div class="md:col-span-5 space-y-6 sticky top-24">
                <div class="theme-card border theme-border rounded-[2rem] p-6 shadow-sm space-y-5">
                    <h3 class="text-[10px] font-bold theme-text opacity-30 uppercase tracking-widest pl-1">add activity</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-bold opacity-30 uppercase tracking-widest pl-1">date</label>
                            <input type="date" wire:model="itineraryDate" class="w-full bg-white/5 border theme-border rounded-xl px-4 py-3 text-xs theme-text focus:ring-brand-200">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-bold opacity-30 uppercase tracking-widest pl-1">time</label>
                            <input type="time" wire:model="itineraryTime" class="w-full bg-white/5 border theme-border rounded-xl px-4 py-3 text-xs theme-text focus:ring-brand-200">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-bold opacity-30 uppercase tracking-widest pl-1">activity name</label>
                        <input type="text" wire:model="itineraryActivity" placeholder="what's the plan?..." class="w-full bg-white/5 border theme-border rounded-xl px-4 py-3 text-xs theme-text lowercase focus:ring-brand-200">
                    </div>
                    <button wire:click="addItinerary" class="w-full py-4 theme-accent-bg text-white rounded-2xl text-xs font-bold shadow-xl shadow-brand-500/20 active:scale-95 transition-all">add to plan</button>
                </div>
            </div>

            {{-- Right Column: List --}}
            <div class="md:col-span-7 space-y-6">
                @forelse($plan->itineraries->groupBy(fn($i) => $i->event_date->format('Y-m-d')) as $date => $items)
                    <div class="space-y-4">
                        <div class="flex items-center gap-4">
                            <span class="text-xs font-bold theme-accent uppercase tracking-widest shrink-0">{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</span>
                            <div class="h-[1px] flex-1 bg-current/10"></div>
                        </div>
                        
                        <div class="space-y-3">
                            @foreach($items as $item)
                                <div class="flex gap-4 group">
                                    <div class="flex flex-col items-center">
                                        <div class="w-2.5 h-2.5 rounded-full theme-accent-bg mt-2.5 shadow-[0_0_8px_rgba(var(--accent-color),0.5)]"></div>
                                        <div class="w-[1px] flex-1 bg-current/10 my-2"></div>
                                    </div>
                                    <div class="flex-1 theme-card border theme-border rounded-2xl p-5 relative hover:bg-white/5 transition-all shadow-sm">
                                        <div class="flex justify-between items-start">
                                            <div class="space-y-1">
                                                <p class="text-[10px] font-bold theme-accent uppercase tracking-wider">{{ $item->event_time ? \Carbon\Carbon::parse($item->event_time)->format('g:i A') : 'anytime' }}</p>
                                                <h4 class="text-base font-bold theme-text tracking-tight lowercase">{{ $item->activity }}</h4>
                                                @if($item->notes)
                                                    <p class="text-xs opacity-40 theme-text mt-2 lowercase leading-relaxed">{{ $item->notes }}</p>
                                                @endif
                                            </div>
                                            <button wire:click="deleteItinerary({{ $item->id }})" class="opacity-0 group-hover:opacity-100 p-2 text-red-400/50 hover:text-red-500 transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="py-20 text-center theme-card border border-dashed theme-border rounded-[2rem] opacity-40">
                        <p class="text-sm lowercase italic">no activities yet. let's build our day!</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Budget Tab Content --}}
        <div x-show="tab === 'budget'" x-transition class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
            {{-- Left Column: Overview & Form --}}
            <div class="md:col-span-5 space-y-6 sticky top-24">
                {{-- Budget Overview Card --}}
                <div class="theme-card border theme-border rounded-[2.5rem] p-8 shadow-sm space-y-6 relative overflow-hidden">
                    <div class="absolute -right-8 -top-8 w-32 h-32 theme-accent-bg opacity-5 rounded-full blur-3xl"></div>
                    
                    <div class="flex justify-between items-center relative z-10">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest opacity-30 theme-text">total spent</p>
                            <h2 class="text-3xl font-bold theme-text tracking-tighter">Rp {{ number_format($plan->spent_budget, 0, ',', '.') }}</h2>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-bold uppercase tracking-widest opacity-30 theme-text">remaining</p>
                            <p class="text-sm font-bold theme-accent">Rp {{ number_format($plan->total_budget - $plan->spent_budget, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="space-y-2 relative z-10">
                        <div class="flex justify-between text-[11px] font-bold lowercase opacity-40 theme-text px-1">
                            <span>{{ round($plan->budget_progress) }}% used</span>
                            <span>limit: {{ number_format($plan->total_budget, 0, ',', '.') }}</span>
                        </div>
                        <div class="h-3 w-full bg-current/5 rounded-full overflow-hidden border theme-border">
                            <div class="h-full theme-accent-bg transition-all duration-1000 ease-out shadow-[0_0_15px_rgba(var(--accent-color),0.4)]" 
                                 style="width: {{ $plan->budget_progress }}%"></div>
                        </div>
                    </div>
                </div>

                {{-- Add Expense Form --}}
                <div class="theme-card border theme-border rounded-[2rem] p-6 shadow-sm space-y-5">
                    <h3 class="text-[10px] font-bold theme-text opacity-30 uppercase tracking-widest pl-1">add expense</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-bold opacity-30 uppercase tracking-widest pl-1">description</label>
                            <input type="text" wire:model="expenseTitle" placeholder="e.g. romantic dinner..." class="w-full bg-white/5 border theme-border rounded-xl px-4 py-3 text-xs theme-text lowercase focus:ring-brand-200">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1.5">
                                <label class="text-[9px] font-bold opacity-30 uppercase tracking-widest pl-1">category</label>
                                <select wire:model="expenseCategory" class="w-full bg-white/5 border theme-border rounded-xl px-4 py-3 text-xs theme-text lowercase focus:ring-brand-200">
                                    <option value="general">general</option>
                                    <option value="food">food & drink</option>
                                    <option value="transport">transport</option>
                                    <option value="lodging">hotel/stay</option>
                                    <option value="fun">entertainment</option>
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[9px] font-bold opacity-30 uppercase tracking-widest pl-1">amount</label>
                                <input type="number" wire:model="expenseAmount" placeholder="amount..." class="w-full bg-white/5 border theme-border rounded-xl px-4 py-3 text-xs theme-text focus:ring-brand-200">
                            </div>
                        </div>
                    </div>
                    <button wire:click="addExpense" class="w-full py-4 theme-accent-bg text-white rounded-2xl text-xs font-bold shadow-xl shadow-brand-500/20 active:scale-95 transition-all">log expense</button>
                </div>
            </div>

            {{-- Right Column: Expense List --}}
            <div class="md:col-span-7 space-y-3">
                <div class="flex items-center justify-between px-2 mb-4">
                    <h2 class="text-[11px] font-bold lowercase tracking-tight theme-text opacity-50">recent expenses</h2>
                </div>
                @forelse($plan->expenses as $expense)
                    <div class="theme-card border theme-border rounded-2xl p-5 flex items-center justify-between group hover:bg-white/5 transition-all shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-current/5 flex items-center justify-center theme-accent group-hover:scale-110 transition-transform">
                                @if($expense->category === 'food') <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>
                                @elseif($expense->category === 'transport') <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                @elseif($expense->category === 'lodging') <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                @else <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> @endif
                            </div>
                            <div>
                                <h4 class="text-sm font-bold theme-text lowercase tracking-tight">{{ $expense->title }}</h4>
                                <p class="text-[10px] opacity-30 theme-text uppercase tracking-widest font-bold">{{ $expense->category }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-6">
                            <p class="text-sm font-bold theme-text">Rp {{ number_format($expense->amount, 0, ',', '.') }}</p>
                            <button wire:click="deleteExpense({{ $expense->id }})" class="opacity-0 group-hover:opacity-100 p-2 text-red-400/50 hover:text-red-500 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="py-20 text-center theme-card border border-dashed theme-border rounded-[2rem] opacity-40">
                        <p class="text-sm lowercase italic">no expenses logged yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
