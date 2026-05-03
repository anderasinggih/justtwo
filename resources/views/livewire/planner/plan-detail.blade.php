<div x-data="{ tab: 'itinerary' }" class="pb-32">
    {{-- Hero/Cover Section --}}
    <div class="relative h-64 w-full overflow-hidden">
        @if($plan->cover_image)
            <img src="{{ Storage::disk('public')->url($plan->cover_image) }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full bg-current/5"></div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent"></div>
        
        <div class="absolute top-8 left-4 right-4 flex items-center justify-between">
            <a href="{{ route('planner') }}" wire:navigate class="p-2 rounded-full bg-black/20 backdrop-blur-md text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <a href="{{ route('planner.create', ['plan' => $plan->id]) }}" wire:navigate class="p-2 rounded-full bg-black/20 backdrop-blur-md text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            </a>
        </div>

        <div class="absolute bottom-6 left-6 right-6">
            <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-brand-500 text-white uppercase tracking-widest mb-2 inline-block">
                {{ $plan->status }}
            </span>
            <h1 class="text-3xl font-bold text-white tracking-tighter lowercase">{{ $plan->title }}</h1>
            <p class="text-xs text-white/60 lowercase mt-1">
                {{ $plan->target_date ? $plan->target_date->format('l, F d, Y') : 'date not set' }}
            </p>
        </div>
    </div>

    <div class="max-w-xl mx-auto px-4 mt-8 space-y-8">
        {{-- Custom Tab Navigation --}}
        <div class="flex p-1 bg-current/5 rounded-2xl">
            <button @click="tab = 'itinerary'" 
                    :class="tab === 'itinerary' ? 'theme-card theme-text shadow-sm' : 'theme-text opacity-40'"
                    class="flex-1 py-2 text-[11px] font-bold uppercase tracking-widest rounded-xl transition-all">
                itinerary
            </button>
            <button @click="tab = 'budget'" 
                    :class="tab === 'budget' ? 'theme-card theme-text shadow-sm' : 'theme-text opacity-40'"
                    class="flex-1 py-2 text-[11px] font-bold uppercase tracking-widest rounded-xl transition-all">
                budget
            </button>
        </div>

        {{-- Itinerary Tab Content --}}
        <div x-show="tab === 'itinerary'" x-transition class="space-y-6">
            {{-- Add Itinerary Form --}}
            <div class="theme-card border theme-border rounded-3xl p-5 shadow-sm space-y-4">
                <h3 class="text-[10px] font-bold theme-text opacity-30 uppercase tracking-widest pl-1">add activity</h3>
                <div class="grid grid-cols-2 gap-3">
                    <input type="date" wire:model="itineraryDate" class="bg-white/5 border theme-border rounded-xl px-4 py-2 text-[10px] theme-text">
                    <input type="time" wire:model="itineraryTime" class="bg-white/5 border theme-border rounded-xl px-4 py-2 text-[10px] theme-text">
                </div>
                <input type="text" wire:model="itineraryActivity" placeholder="what's the plan?..." class="w-full bg-white/5 border theme-border rounded-xl px-4 py-2 text-[10px] theme-text lowercase">
                <button wire:click="addItinerary" class="w-full py-2.5 theme-accent-bg text-white rounded-xl text-[10px] font-bold shadow-lg shadow-brand-500/20 active:scale-95 transition-all">add to plan</button>
            </div>

            {{-- Timeline view --}}
            <div class="space-y-4 relative">
                @forelse($plan->itineraries->groupBy(fn($i) => $i->event_date->format('Y-m-d')) as $date => $items)
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="h-[1px] flex-1 bg-current/5"></div>
                            <span class="text-[9px] font-bold theme-accent uppercase tracking-widest">{{ \Carbon\Carbon::parse($date)->format('M d') }}</span>
                            <div class="h-[1px] flex-1 bg-current/5"></div>
                        </div>
                        
                        @foreach($items as $item)
                            <div class="flex gap-4 group">
                                <div class="flex flex-col items-center">
                                    <div class="w-2 h-2 rounded-full theme-accent-bg mt-2"></div>
                                    <div class="w-[1px] flex-1 bg-current/10 my-2"></div>
                                </div>
                                <div class="flex-1 theme-card border theme-border rounded-2xl p-4 relative group">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-[9px] font-bold theme-accent mb-1">{{ $item->event_time ? \Carbon\Carbon::parse($item->event_time)->format('g:i A') : 'anytime' }}</p>
                                            <h4 class="text-sm font-bold theme-text tracking-tight lowercase">{{ $item->activity }}</h4>
                                            @if($item->notes)
                                                <p class="text-[10px] opacity-40 theme-text mt-1 lowercase">{{ $item->notes }}</p>
                                            @endif
                                        </div>
                                        <button wire:click="deleteItinerary({{ $item->id }})" class="opacity-0 group-hover:opacity-100 p-1 text-red-400/50 hover:text-red-500 transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @empty
                    <div class="py-12 text-center">
                        <p class="text-xs opacity-20 theme-text lowercase italic">no activities yet. let's build our day!</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Budget Tab Content --}}
        <div x-show="tab === 'budget'" x-transition class="space-y-6">
            {{-- Budget Card Overview --}}
            <div class="theme-card border theme-border rounded-[2rem] p-6 shadow-sm space-y-6 relative overflow-hidden">
                <div class="absolute -right-8 -top-8 w-32 h-32 theme-accent-bg opacity-5 rounded-full blur-3xl"></div>
                
                <div class="flex justify-between items-center relative z-10">
                    <div>
                        <p class="text-[9px] font-bold uppercase tracking-widest opacity-30 theme-text">total spent</p>
                        <h2 class="text-3xl font-bold theme-text tracking-tighter">Rp {{ number_format($plan->spent_budget, 0, ',', '.') }}</h2>
                    </div>
                    <div class="text-right">
                        <p class="text-[9px] font-bold uppercase tracking-widest opacity-30 theme-text">remaining</p>
                        <p class="text-sm font-bold theme-accent">Rp {{ number_format($plan->total_budget - $plan->spent_budget, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="space-y-2 relative z-10">
                    <div class="flex justify-between text-[10px] font-bold lowercase opacity-40 theme-text px-1">
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
            <div class="theme-card border theme-border rounded-3xl p-5 shadow-sm space-y-4">
                <h3 class="text-[10px] font-bold theme-text opacity-30 uppercase tracking-widest pl-1">add expense</h3>
                <div class="grid grid-cols-2 gap-3">
                    <input type="text" wire:model="expenseTitle" placeholder="title (e.g. dinner)..." class="bg-white/5 border theme-border rounded-xl px-4 py-2 text-[10px] theme-text lowercase">
                    <input type="number" wire:model="expenseAmount" placeholder="amount..." class="bg-white/5 border theme-border rounded-xl px-4 py-2 text-[10px] theme-text">
                </div>
                <div class="flex gap-2">
                    <select wire:model="expenseCategory" class="flex-1 bg-white/5 border theme-border rounded-xl px-4 py-2 text-[10px] theme-text lowercase">
                        <option value="general">general</option>
                        <option value="food">food & drink</option>
                        <option value="transport">transport</option>
                        <option value="lodging">hotel/stay</option>
                        <option value="fun">entertainment</option>
                    </select>
                    <button wire:click="addExpense" class="px-6 py-2.5 theme-accent-bg text-white rounded-xl text-[10px] font-bold shadow-lg shadow-brand-500/20 active:scale-95 transition-all">add</button>
                </div>
            </div>

            {{-- Expense List --}}
            <div class="space-y-2">
                @forelse($plan->expenses as $expense)
                    <div class="theme-card border theme-border rounded-2xl p-4 flex items-center justify-between group hover:bg-white/5 transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-current/5 flex items-center justify-center theme-accent">
                                @if($expense->category === 'food') <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>
                                @elseif($expense->category === 'transport') <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
                                @else <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> @endif
                            </div>
                            <div>
                                <h4 class="text-sm font-bold theme-text lowercase tracking-tight">{{ $expense->title }}</h4>
                                <p class="text-[9px] opacity-30 theme-text uppercase tracking-widest">{{ $expense->category }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <p class="text-xs font-bold theme-text">Rp {{ number_format($expense->amount, 0, ',', '.') }}</p>
                            <button wire:click="deleteExpense({{ $expense->id }})" class="opacity-0 group-hover:opacity-100 p-1 text-red-400/50 hover:text-red-500 transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center">
                        <p class="text-xs opacity-20 theme-text lowercase italic">no expenses logged yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
