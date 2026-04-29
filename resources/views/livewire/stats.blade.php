<div class="max-w-xl mx-auto py-8 px-4 space-y-8 pb-20">
    <header class="flex items-center gap-4">
        <a href="{{ route('profile') }}" wire:navigate class="theme-text opacity-50">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <h1 class="text-lg font-bold theme-text lowercase tracking-tighter">insights</h1>
            <p class="text-[10px] theme-text opacity-40 lowercase">our journey by the numbers</p>
        </div>
    </header>

    {{-- Top Overview - Compact 2x2 Grid --}}
    <div class="grid grid-cols-2 gap-3">
        <div class="bg-white/5 border theme-border rounded-[2rem] p-6 text-center">
            <p class="text-3xl font-bold theme-text mb-0.5">{{ $daysTogether }}</p>
            <p class="text-[9px] theme-text opacity-30 font-bold uppercase tracking-widest">days</p>
        </div>
        <div class="bg-white/5 border theme-border rounded-[2rem] p-6 text-center">
            <p class="text-3xl font-bold theme-text mb-0.5">{{ $totalMemories }}</p>
            <p class="text-[9px] theme-text opacity-30 font-bold uppercase tracking-widest">memories</p>
        </div>
        <div class="bg-white/5 border theme-border rounded-[2rem] p-6 text-center">
            <p class="text-3xl font-bold theme-text mb-0.5">{{ $totalPhotos }}</p>
            <p class="text-[9px] theme-text opacity-30 font-bold uppercase tracking-widest">photos</p>
        </div>
        <div class="bg-white/5 border theme-border rounded-[2rem] p-6 text-center">
            <p class="text-3xl font-bold theme-text mb-0.5">{{ $typeStats['journal'] ?? 0 }}</p>
            <p class="text-[9px] theme-text opacity-30 font-bold uppercase tracking-widest">journals</p>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="space-y-4">
        {{-- Activity Chart --}}
        <div class="bg-white/5 border theme-border rounded-[2.5rem] p-8 space-y-6">
            <h2 class="text-xs font-bold theme-text lowercase opacity-40 tracking-widest text-center">monthly activity</h2>
            <div class="h-32 flex items-end gap-2 px-2">
                @php $max = $monthlyTrend->max('count') ?: 1; @endphp
                @foreach($monthlyTrend as $trend)
                    <div class="flex-1 flex flex-col items-center gap-2 group">
                        <div class="w-full bg-brand-500/10 rounded-t-lg transition-all duration-500 group-hover:bg-brand-500/30 relative" style="height: {{ ($trend->count / $max) * 100 }}%"></div>
                        <span class="text-[8px] theme-text opacity-20 font-bold uppercase tracking-tighter">{{ Carbon\Carbon::parse($trend->month)->format('M') }}</span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- Contributor Breakdown --}}
    <div class="bg-brand-500/5 border border-brand-500/10 rounded-[2.5rem] p-8 relative overflow-hidden">
        <div class="absolute -right-20 -bottom-20 w-40 h-40 bg-brand-500/10 rounded-full blur-3xl opacity-50"></div>
        
        <div class="relative z-10 space-y-8">
            <div class="text-center space-y-1">
                <h2 class="text-sm font-bold theme-text lowercase">shared contribution</h2>
                <p class="text-[10px] theme-text opacity-40 lowercase">building our space together</p>
            </div>

            <div class="flex items-center justify-center gap-12">
                @foreach($userActivity as $name => $stats)
                    <div class="text-center space-y-3">
                        <div class="w-16 h-16 bg-white/10 rounded-full border-2 border-white/5 mx-auto flex items-center justify-center overflow-hidden">
                             <img src="https://ui-avatars.com/api/?name={{ urlencode($name) }}&background=random" class="w-full h-full object-cover opacity-80">
                        </div>
                        <div>
                            <p class="text-[11px] font-bold theme-text lowercase">{{ $name }}</p>
                            <p class="text-[9px] theme-text opacity-30 lowercase">{{ $stats['posts'] }} posts</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
