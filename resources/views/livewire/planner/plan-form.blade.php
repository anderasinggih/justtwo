<div class="max-w-3xl mx-auto px-4 pt-6 pb-32">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('planner') }}" wire:navigate class="p-2 rounded-full hover:bg-current/5 transition-colors theme-text opacity-50">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h1 class="text-2xl font-bold theme-text lowercase tracking-tighter">{{ $planId ? 'edit' : 'new' }} plan</h1>
    </div>

    <form wire:submit.prevent="save" class="space-y-6">
        <div class="space-y-4">
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold opacity-30 uppercase tracking-widest pl-2">plan title</label>
                <input wire:model="title" placeholder="e.g. summer vacation in bali"
                       class="w-full bg-white/5 border theme-border rounded-2xl px-5 py-4 text-sm focus:ring-brand-200 theme-text lowercase">
                @error('title') <p class="text-[10px] text-red-500 pl-2">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold opacity-30 uppercase tracking-widest pl-2">target date</label>
                    <input type="date" wire:model="target_date"
                           class="w-full bg-white/5 border theme-border rounded-2xl px-5 py-4 text-sm focus:ring-brand-200 theme-text">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold opacity-30 uppercase tracking-widest pl-2">total budget</label>
                    <input type="number" wire:model="total_budget" placeholder="nominal..."
                           class="w-full bg-white/5 border theme-border rounded-2xl px-5 py-4 text-sm focus:ring-brand-200 theme-text">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-bold opacity-30 uppercase tracking-widest pl-2">description</label>
                <textarea wire:model="description" rows="4" placeholder="briefly describe your dream..."
                          class="w-full bg-white/5 border theme-border rounded-2xl px-5 py-4 text-sm focus:ring-brand-200 theme-text lowercase"></textarea>
            </div>
        </div>

        <button type="submit" 
                class="w-full py-4 theme-accent-bg text-white rounded-[2rem] font-bold shadow-xl shadow-brand-500/20 active:scale-95 transition-all">
            {{ $planId ? 'update' : 'create' }} plan
        </button>
    </form>
</div>
