<div class="max-w-2xl mx-auto px-1.5 sm:px-4 pt-4 pb-32">
    <div class="flex items-center gap-3 mb-6 px-2">
        <a href="{{ route('planner') }}" wire:navigate class="p-2 rounded-full hover:bg-current/5 transition-colors theme-text opacity-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h1 class="text-xl font-bold theme-text lowercase tracking-tighter">{{ $planId ? 'edit' : 'new' }} plan</h1>
    </div>

    <form wire:submit.prevent="save" class="space-y-4 px-2" x-data="{ 
        rawBudget: @entangle('total_budget').live,
        get isDreamPlan() { return !this.rawBudget || this.rawBudget == 0 }
    }">
        <div class="space-y-3">
            {{-- Title --}}
            <div class="space-y-1">
                <label class="text-[9px] font-bold opacity-30 uppercase tracking-widest pl-1">plan title</label>
                <input wire:model.blur="title" placeholder="e.g. summer vacation in bali"
                       class="w-full bg-white/5 border theme-border rounded-xl px-4 py-3 text-xs focus:ring-brand-200 theme-text lowercase @error('title') border-red-500/50 @enderror">
                @error('title') <p class="text-[9px] text-red-500 pl-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                {{-- Target Date --}}
                <div class="space-y-1">
                    <label class="text-[9px] font-bold opacity-30 uppercase tracking-widest pl-1">target date</label>
                    <input type="date" wire:model.live="target_date"
                           class="w-full bg-white/5 border theme-border rounded-xl px-4 py-3 text-[10px] focus:ring-brand-200 theme-text @error('target_date') border-red-500/50 @enderror">
                    @error('target_date') <p class="text-[9px] text-red-500 pl-1">{{ $message }}</p> @enderror
                </div>

                {{-- Total Budget with Masking --}}
                <div class="space-y-1">
                    <div class="flex items-center justify-between px-1">
                        <label class="text-[9px] font-bold opacity-30 uppercase tracking-widest">budget</label>
                        <template x-if="isDreamPlan">
                            <span class="text-[8px] theme-accent font-bold lowercase tracking-widest opacity-60">dream plan</span>
                        </template>
                    </div>
                    <div class="relative">
                        <input type="text" 
                               x-mask:dynamic="'Rp ' + $money($input, '.', ',')"
                               x-on:input="rawBudget = $event.target.value.replace(/[^\d]/g, '')"
                               x-bind:value="rawBudget"
                               placeholder="Rp 0"
                               class="w-full bg-white/5 border theme-border rounded-xl px-4 py-3 text-[10px] focus:ring-brand-200 theme-text">
                    </div>
                    @error('total_budget') <p class="text-[9px] text-red-500 pl-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                {{-- Priority --}}
                <div class="space-y-1">
                    <label class="text-[9px] font-bold opacity-30 uppercase tracking-widest pl-1">priority</label>
                    <select wire:model="priority" class="w-full bg-white/5 border theme-border rounded-xl px-4 py-3 text-[10px] focus:ring-brand-200 theme-text lowercase">
                        <option value="low">low priority</option>
                        <option value="medium">medium priority</option>
                        <option value="high">high priority</option>
                    </select>
                </div>

                {{-- Status --}}
                <div class="space-y-1">
                    <label class="text-[9px] font-bold opacity-30 uppercase tracking-widest pl-1">status</label>
                    <select wire:model="status" class="w-full bg-white/5 border theme-border rounded-xl px-4 py-3 text-[10px] focus:ring-brand-200 theme-text lowercase">
                        <option value="draft">drafting</option>
                        <option value="ongoing">ongoing</option>
                        <option value="completed">completed</option>
                        <option value="cancelled">cancelled</option>
                    </select>
                </div>
            </div>

            {{-- Category & Saving Link --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="text-[9px] font-bold opacity-30 uppercase tracking-widest pl-1">category</label>
                    <select wire:model="category" class="w-full bg-white/5 border theme-border rounded-xl px-4 py-3 text-[10px] focus:ring-brand-200 theme-text lowercase">
                        <option value="">-- select category --</option>
                        <option value="travel">travel & vacation ✈️</option>
                        <option value="wedding">wedding planning 💍</option>
                        <option value="home">home & living 🏠</option>
                        <option value="finance">finance & investment 💰</option>
                        <option value="celebration">celebration & event 🎉</option>
                        <option value="lifestyle">shopping & lifestyle 🛍️</option>
                        <option value="health">health & fitness 🏥</option>
                        <option value="education">education & growth 📚</option>
                        <option value="other">other memories ✨</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-[9px] font-bold opacity-30 uppercase tracking-widest pl-1">saving link</label>
                    <select wire:model.live="saving_id" class="w-full bg-white/5 border theme-border rounded-xl px-4 py-3 text-[10px] focus:ring-brand-200 theme-text lowercase">
                        <option value="">-- select saving goal --</option>
                        @forelse($savings as $saving)
                            <option value="{{ $saving->id }}">{{ $saving->title }}</option>
                        @empty
                            <option value="" disabled>no active saving goals</option>
                        @endforelse
                    </select>
                </div>
            </div>

            {{-- Description --}}
            <div class="space-y-1">
                <label class="text-[9px] font-bold opacity-30 uppercase tracking-widest pl-1">description</label>
                <textarea wire:model="description" rows="3" placeholder="briefly describe your dream..."
                          class="w-full bg-white/5 border theme-border rounded-xl px-4 py-3 text-xs focus:ring-brand-200 theme-text lowercase leading-relaxed"></textarea>
                @error('description') <p class="text-[9px] text-red-500 pl-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <button type="submit" wire:loading.attr="disabled"
                class="w-full py-3.5 theme-accent-bg text-white rounded-2xl text-[11px] font-bold shadow-lg shadow-brand-500/20 active:scale-95 transition-all mt-4 flex items-center justify-center gap-2">
            <span wire:loading.remove>{{ $planId ? 'update' : 'create' }} plan</span>
            <span wire:loading class="flex items-center gap-2">
                <svg class="animate-spin h-3 w-3 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                processing...
            </span>
        </button>
    </form>
</div>
