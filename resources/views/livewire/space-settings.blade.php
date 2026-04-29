<div class="min-h-screen theme-bg pb-20">
    {{-- Header --}}
    <header class="flex items-center justify-between px-4 h-14 border-b theme-border sticky top-0 theme-bg z-30">
        <a href="{{ route('app-settings') }}" wire:navigate class="theme-text opacity-70 hover:opacity-100 transition-opacity">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h1 class="text-sm font-bold theme-text lowercase tracking-tighter">shared space settings</h1>
        <div class="w-6"></div>
    </header>

    <div class="max-w-xl mx-auto px-6 py-8 space-y-8">
        <div class="bg-brand-500/5 rounded-2xl p-4 border border-brand-500/10">
            <p class="text-xs theme-text font-medium leading-relaxed">this is your shared sanctuary. changes here affect both you and your partner.</p>
        </div>

        <div class="space-y-6">
            <div class="space-y-1.5">
                <label class="text-[10px] theme-text opacity-40 uppercase tracking-widest font-bold px-1">space name</label>
                <input type="text" wire:model="relationship_name" class="w-full bg-white/5 border theme-border rounded-xl px-4 py-3 text-sm theme-text focus:ring-1 focus:ring-brand-500 transition-all" placeholder="our space name">
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] theme-text opacity-40 uppercase tracking-widest font-bold px-1">anniversary date</label>
                <input type="date" wire:model="anniversary_date" class="w-full bg-white/5 border theme-border rounded-xl px-4 py-3 text-sm theme-text focus:ring-1 focus:ring-brand-500 transition-all">
            </div>

            <button wire:click="updateRelationship" class="w-full py-4 bg-white/5 border theme-border rounded-2xl text-xs font-bold theme-text hover:bg-white/10 transition-all">
                update shared space
            </button>
        </div>

        <div class="pt-8 space-y-4">
            <h3 class="text-[10px] theme-text opacity-20 uppercase tracking-widest font-bold px-1">danger zone</h3>
            <div class="space-y-3">
                <button wire:click="exportMemories" class="w-full py-3 px-4 flex items-center justify-between bg-white/5 border theme-border rounded-xl text-[11px] theme-text hover:bg-white/10 transition-all">
                    <span>export all memories (.zip)</span>
                    <svg class="w-4 h-4 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                </button>
                <button class="w-full py-3 px-4 flex items-center justify-between bg-red-500/5 border border-red-500/20 rounded-xl text-[11px] text-red-500 opacity-60 hover:opacity-100 transition-all">
                    <span>close shared space</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Success Notification --}}
    @if (session()->has('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 3000)" 
             class="fixed bottom-24 left-1/2 -translate-x-1/2 bg-gray-900 text-white px-6 py-3 rounded-full text-[11px] font-bold shadow-2xl z-50 flex items-center gap-2">
            <svg class="w-3 h-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif
</div>
