<div class="max-w-3xl mx-auto px-4 pt-6 pb-32">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('planner') }}" wire:navigate class="p-2 rounded-full hover:bg-current/5 transition-colors theme-text opacity-50">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h1 class="text-2xl font-bold theme-text lowercase tracking-tighter">{{ $planId ? 'edit' : 'new' }} plan</h1>
    </div>

    <form wire:submit.prevent="save" class="space-y-6">
        {{-- Cover Image Upload --}}
        <div class="relative h-48 w-full bg-current/5 rounded-[2.5rem] overflow-hidden border theme-border group">
            @if($cover_image)
                <img src="{{ $cover_image->temporaryUrl() }}" class="w-full h-full object-cover">
            @endif
            <label class="absolute inset-0 flex flex-col items-center justify-center cursor-pointer bg-black/0 group-hover:bg-black/20 transition-all">
                <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span class="text-[10px] text-white font-bold uppercase tracking-widest mt-2 opacity-0 group-hover:opacity-100 transition-opacity">upload cover</span>
                <input type="file" wire:model="cover_image" class="hidden">
            </label>
        </div>

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
