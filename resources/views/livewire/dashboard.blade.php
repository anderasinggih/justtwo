<div class="max-w-5xl mx-auto px-1.5 sm:px-4 pt-4 space-y-3.5 pb-32" wire:poll.30s.visible>
    {{-- Minimalist Header --}}
    <div class="flex items-center justify-between mb-1 px-1">
        <div>
            <h2 class="text-[10px] font-bold theme-text opacity-30 lowercase tracking-widest leading-none mb-1">welcome back</h2>
            <h1 class="text-xl font-bold theme-text lowercase tracking-tighter">{{ $relationship->name ?? 'Together' }}</h1>
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

    {{-- Hero Section: Together Timer --}}
    <div class="relative overflow-hidden theme-card border theme-border rounded-[2.5rem] p-7 shadow-sm text-center"
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
        
        <div class="absolute -right-24 -top-24 w-48 h-48 bg-brand-500/5 rounded-full blur-3xl opacity-40"></div>
        <div class="absolute -left-24 -bottom-24 w-48 h-48 theme-accent-bg opacity-5 rounded-full blur-3xl"></div>
        
        <div class="relative z-10 space-y-4">
            <div class="flex justify-center items-center gap-6 md:gap-10">
                @php $currentPartner = $partners->where('id', Auth::id())->first(); @endphp
                <div class="flex flex-col items-center">
                    <div class="w-12 h-12 md:w-14 md:h-14 rounded-full border-2 theme-border overflow-hidden shadow-sm">
                        @if($currentPartner?->profile_photo_path)
                            <img src="{{ Storage::disk('public')->url($currentPartner->profile_photo_path) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-current/5 flex items-center justify-center theme-text opacity-20 font-bold uppercase text-xs">{{ substr(Auth::user()->name, 0, 1) }}</div>
                        @endif
                    </div>
                    <p class="text-[8px] font-bold mt-1.5 lowercase theme-text opacity-40">{{ Auth::user()->name }}</p>
                </div>
                
                <div class="flex flex-col items-center">
                    <div class="w-9 h-9 bg-brand-500/5 rounded-full flex items-center justify-center theme-accent animate-pulse">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                    </div>
                </div>

                @php $otherPartner = $partners->where('id', '!=', Auth::id())->first(); @endphp
                <div class="flex flex-col items-center">
                    <div class="w-12 h-12 md:w-14 md:h-14 rounded-full border-2 theme-border overflow-hidden shadow-sm">
                        @if($otherPartner?->profile_photo_path)
                            <img src="{{ Storage::disk('public')->url($otherPartner->profile_photo_path) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-current/5 flex items-center justify-center theme-text opacity-20 font-bold uppercase text-xs">?</div>
                        @endif
                    </div>
                    <p class="text-[8px] font-bold mt-1.5 lowercase theme-text opacity-40">{{ $otherPartner?->name ?? 'wait' }}</p>
                </div>
            </div>

            <div class="pt-1">
                <div class="flex items-center justify-center gap-4 md:gap-8">
                    <div class="text-center min-w-[40px]">
                        <h1 class="text-2xl md:text-3xl font-bold tracking-tighter theme-text" x-text="days"></h1>
                        <p class="text-[7px] font-bold theme-text opacity-20 uppercase tracking-widest">days</p>
                    </div>
                    <div class="text-center min-w-[40px]">
                        <h1 class="text-2xl md:text-3xl font-bold tracking-tighter theme-text" x-text="hours"></h1>
                        <p class="text-[7px] font-bold theme-text opacity-20 uppercase tracking-widest">hours</p>
                    </div>
                    <div class="text-center min-w-[40px]">
                        <h1 class="text-2xl md:text-3xl font-bold tracking-tighter theme-text" x-text="mins"></h1>
                        <p class="text-[7px] font-bold theme-text opacity-20 uppercase tracking-widest">mins</p>
                    </div>
                    <div class="text-center min-w-[40px]">
                        <h1 class="text-2xl md:text-3xl font-bold tracking-tighter theme-accent" x-text="secs"></h1>
                        <p class="text-[7px] font-bold theme-text opacity-20 uppercase tracking-widest">secs</p>
                    </div>
                </div>
                <p class="text-[8px] opacity-30 theme-text lowercase tracking-wide mt-3 font-medium">
                    since {{ $togetherStats['anniversary_formatted'] }}
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-3.5">
        {{-- Left Column: Savings & Events --}}
        <div class="lg:col-span-7 space-y-3.5">
            {{-- Shared Savings --}}
            <section class="space-y-2.5">
                <div class="flex items-center justify-between px-1">
                    <h3 class="text-[10px] font-bold theme-text opacity-30 uppercase tracking-widest">shared savings</h3>
                </div>
                <div class="grid grid-cols-1 gap-2.5">
                    @forelse($savings as $saving)
                        <div x-data="{ showAdd: false }" class="theme-card border theme-border rounded-[1.5rem] p-5 shadow-sm group hover:shadow-md transition-all relative overflow-hidden">
                            <div @click="showAdd = !showAdd" class="cursor-pointer relative z-10">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex items-center gap-3.5">
                                        <div class="w-10 h-10 rounded-2xl bg-brand-500/5 flex items-center justify-center theme-accent text-xl shadow-inner">
                                            {!! $saving->icon ?: '💰' !!}
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-bold theme-text lowercase tracking-tight">{{ $saving->title }}</h4>
                                            <p class="text-[9px] opacity-30 theme-text uppercase font-bold tracking-widest">target: Rp {{ number_format($saving->target_amount, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold theme-text tracking-tighter">Rp {{ number_format($saving->current_amount, 0, ',', '.') }}</p>
                                        <div class="flex items-center justify-end gap-1">
                                            <span class="w-1 h-1 rounded-full theme-accent-bg animate-pulse"></span>
                                            <p class="text-[9px] theme-accent font-bold uppercase tracking-widest">{{ round($saving->progress) }}% saved</p>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Gamified Progress Bar --}}
                                <div class="relative h-3 w-full bg-current/5 rounded-full p-0.5 overflow-hidden">
                                    {{-- Moving Shimmer Effect --}}
                                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full animate-[shimmer_2s_infinite] pointer-events-none"></div>
                                    
                                    <div class="h-full rounded-full transition-all duration-1000 ease-out relative" 
                                         style="width: {{ $saving->progress }}%; 
                                                background: linear-gradient(90deg, var(--accent-color) 0%, #f43f5e 100%);
                                                box-shadow: 0 0 15px rgba(244, 63, 94, 0.3);">
                                        
                                        {{-- Moving Mascot/Icon --}}
                                        <div class="absolute -right-2 -top-1 w-5 h-5 flex items-center justify-center transform group-hover:scale-125 transition-transform">
                                            <svg class="w-4 h-4 text-white animate-[twinkle_1.5s_infinite] drop-shadow-[0_0_10px_rgba(255,255,255,0.9)]" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 0L14.59 9.41L24 12L14.59 14.59L12 24L9.41 14.59L0 12L9.41 9.41L12 0Z"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Expandable Add Amount Form --}}
                            <div x-show="showAdd" x-collapse x-cloak class="mt-4 pt-4 border-t theme-border relative z-10">
                                @if($saving->current_amount >= $saving->target_amount)
                                    <div class="flex items-center justify-center py-2 gap-2 bg-emerald-500/10 rounded-xl border border-emerald-500/20">
                                        <span class="text-xs font-bold text-emerald-500 lowercase tracking-tight">goal reached! well done. 🏆</span>
                                    </div>
                                @else
                                    <div class="flex gap-2">
                                        <div class="relative flex-1" x-data="{ 
                                            displayAmount: '',
                                            rawAmount: @entangle('savingAmounts.' . $saving->id)
                                        }" x-init="$watch('rawAmount', v => { if(!v) displayAmount = '' })">
                                            <input type="text" 
                                                   inputmode="numeric"
                                                   x-model="displayAmount"
                                                   x-mask:dynamic="'Rp ' + $money($input, '.', ',')"
                                                   x-on:input="rawAmount = displayAmount.replace(/[^\d]/g, '')"
                                                   placeholder="Rp 0" 
                                                   class="w-full bg-white/5 border theme-border rounded-xl px-4 py-2.5 text-xs theme-text focus:ring-brand-200 placeholder:text-[10px] placeholder:opacity-30">
                                            
                                            @if (session()->has('saving-success-' . $saving->id))
                                                <span class="absolute -top-6 right-0 text-[10px] theme-accent font-bold animate-bounce">
                                                    {{ session('saving-success-' . $saving->id) }}
                                                </span>
                                            @endif
                                            @if (session()->has('saving-error-' . $saving->id))
                                                <span class="absolute -top-6 right-0 text-[10px] text-red-500 font-bold">
                                                    {{ session('saving-error-' . $saving->id) }}
                                                </span>
                                            @endif
                                        </div>
                                        <button wire:click="addSaving({{ $saving->id }})" 
                                                class="px-6 py-2.5 theme-accent-bg text-white rounded-xl text-[10px] font-bold shadow-lg shadow-brand-500/20 active:scale-95 transition-all">
                                            save
                                        </button>
                                    </div>
                                @endif
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
            <section class="space-y-2.5">
                <div class="flex items-center justify-between px-1">
                    <h3 class="text-[10px] font-bold theme-text opacity-30 uppercase tracking-widest">upcoming events</h3>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                    @forelse($upcomingEvents as $event)
                        <div class="theme-card border theme-border rounded-[1.2rem] p-3 shadow-sm text-center space-y-0.5 group">
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
        <div class="lg:col-span-5 space-y-3.5">
            <section class="space-y-2.5">
                <div class="flex items-center justify-between px-1">
                    <h3 class="text-[10px] font-bold theme-text opacity-30 uppercase tracking-widest">latest plans</h3>
                    <a href="{{ route('planner') }}" wire:navigate class="text-[9px] font-bold theme-accent uppercase tracking-widest hover:opacity-70 transition-opacity">view all</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 gap-2.5">
                    @forelse($upcomingPlans as $plan)
                        <a href="{{ route('planner.detail', $plan->id) }}" wire:navigate 
                           class="theme-card border theme-border rounded-[1.5rem] overflow-hidden shadow-sm group hover:shadow-md transition-all flex flex-col">
                            <div class="p-4 flex-1">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-10 h-10 shrink-0 rounded-2xl bg-brand-500/5 flex items-center justify-center theme-accent text-lg group-hover:scale-110 transition-transform shadow-inner">
                                        {{ $plan->icon }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between">
                                            <h4 class="text-sm font-bold theme-text lowercase tracking-tight truncate">{{ $plan->title }}</h4>
                                            <span class="text-[9px] theme-accent font-bold uppercase tracking-widest">{{ round($plan->budget_progress) }}%</span>
                                        </div>
                                        <p class="text-[9px] opacity-30 theme-text lowercase mt-0.5">{{ $plan->target_date ? $plan->target_date->format('M d, Y') : 'anytime' }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Integrated Progress Bar at the bottom edge --}}
                            <div class="h-1.5 w-full bg-current/[0.03] relative">
                                <div class="h-full theme-accent-bg transition-all duration-1000" 
                                     style="width: {{ max(2, $plan->budget_progress) }}%; 
                                            background: linear-gradient(90deg, var(--accent-color) 0%, #10b981 100%);">
                                </div>
                            </div>
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
    
    <style>
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        @keyframes twinkle {
            0%, 100% { opacity: 1; transform: scale(1) rotate(0deg); filter: drop-shadow(0 0 5px rgba(255,255,255,0.8)); }
            50% { opacity: 0.7; transform: scale(1.2) rotate(15deg); filter: drop-shadow(0 0 15px rgba(255,255,255,1)); }
        }
    </style>
</div>