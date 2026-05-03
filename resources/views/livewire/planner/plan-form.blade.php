<div class="max-w-2xl mx-auto px-1.5 sm:px-4 pt-4 pb-32">
    <div class="flex items-center gap-3 mb-6 px-2">
        <a href="{{ route('planner') }}" wire:navigate class="p-2 rounded-full hover:bg-current/5 transition-colors theme-text opacity-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h1 class="text-xl font-bold theme-text lowercase tracking-tighter">{{ $planId ? 'edit' : 'new' }} plan</h1>
    </div>

    <form wire:submit.prevent="save" class="space-y-4 px-2">
        <div class="space-y-3">
            <div class="space-y-1">
                <label class="text-[9px] font-bold opacity-30 uppercase tracking-widest pl-1">plan title</label>
                <input wire:model="title" placeholder="e.g. summer vacation in bali"
                       class="w-full bg-white/5 border theme-border rounded-xl px-4 py-3 text-xs focus:ring-brand-200 theme-text lowercase">
                @error('title') <p class="text-[9px] text-red-500 pl-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="text-[9px] font-bold opacity-30 uppercase tracking-widest pl-1">target date</label>
                    <input type="date" wire:model="target_date"
                           class="w-full bg-white/5 border theme-border rounded-xl px-4 py-3 text-[10px] focus:ring-brand-200 theme-text">
                </div>
                <div class="space-y-1">
                    <label class="text-[9px] font-bold opacity-30 uppercase tracking-widest pl-1">total budget (optional)</label>
                    <input type="number" wire:model="total_budget" placeholder="e.g. 0 if no budget"
                           class="w-full bg-white/5 border theme-border rounded-xl px-4 py-3 text-[10px] focus:ring-brand-200 theme-text">
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-[9px] font-bold opacity-30 uppercase tracking-widest pl-1">link to saving goal (optional)</label>
                <select wire:model="saving_id" class="w-full bg-white/5 border theme-border rounded-xl px-4 py-3 text-[10px] focus:ring-brand-200 theme-text lowercase">
                    <option value="">-- select saving goal --</option>
                    @foreach($savings as $saving)
                        <option value="{{ $saving->id }}">{{ $saving->title }} (Rp {{ number_format($saving->current_amount, 0, ',', '.') }})</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label class="text-[9px] font-bold opacity-30 uppercase tracking-widest pl-1">description</label>
                <textarea wire:model="description" rows="3" placeholder="briefly describe your dream..."
                          class="w-full bg-white/5 border theme-border rounded-xl px-4 py-3 text-xs focus:ring-brand-200 theme-text lowercase leading-relaxed"></textarea>
            </div>
        </div>

        <button type="submit" 
                class="w-full py-3.5 theme-accent-bg text-white rounded-2xl text-[11px] font-bold shadow-lg shadow-brand-500/20 active:scale-95 transition-all mt-2">
            {{ $planId ? 'update' : 'create' }} plan
        </button>
    </form>
</div>
