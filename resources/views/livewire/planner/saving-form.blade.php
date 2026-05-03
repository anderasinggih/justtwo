<div class="max-w-2xl mx-auto px-1.5 sm:px-4 pt-4 pb-32">
    <div class="flex items-center gap-3 mb-6 px-2">
        <a href="{{ route('dashboard') }}" wire:navigate class="p-2 rounded-full hover:bg-current/5 transition-colors theme-text opacity-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h1 class="text-xl font-bold theme-text lowercase tracking-tighter">{{ $savingId ? 'edit' : 'new' }} saving goal</h1>
    </div>

    <form wire:submit.prevent="save" class="space-y-4 px-2" x-data="{ 
        rawTarget: @entangle('target_amount').live 
    }">
        <div class="space-y-3">
            {{-- Icon Selection --}}
            <div class="space-y-1">
                <label class="text-[9px] font-bold opacity-30 uppercase tracking-widest pl-1">goal icon</label>
                <div class="flex gap-2 py-1 overflow-x-auto scrollbar-hide">
                    @foreach(['💰', '🏠', '✈️', '💍', '🚗', '🎓', '🏥', '🎉', '🎁'] as $emoji)
                        <button type="button" @click="$wire.set('icon', '{{ $emoji }}')" 
                                class="w-10 h-10 shrink-0 rounded-xl border-2 transition-all flex items-center justify-center text-xl"
                                :class="$wire.icon === '{{ $emoji }}' ? 'theme-accent-border bg-current/5 scale-110' : 'theme-border opacity-50'">
                            {{ $emoji }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Title --}}
            <div class="space-y-1">
                <label class="text-[9px] font-bold opacity-30 uppercase tracking-widest pl-1">goal title</label>
                <input wire:model="title" placeholder="e.g. bali trip fund"
                       class="w-full bg-white/5 border theme-border rounded-xl px-4 py-3 text-xs focus:ring-brand-200 theme-text lowercase @error('title') border-red-500/50 @enderror">
                @error('title') <p class="text-[9px] text-red-500 pl-1">{{ $message }}</p> @enderror
            </div>

            {{-- Target Amount with Masking --}}
            <div class="space-y-1">
                <label class="text-[9px] font-bold opacity-30 uppercase tracking-widest pl-1">target amount</label>
                <div class="relative">
                    <input type="text" 
                           inputmode="numeric"
                           x-mask:dynamic="'Rp ' + $money($input, '.', ',')"
                           x-on:input="rawTarget = $event.target.value.replace(/[^\d]/g, '')"
                           x-bind:value="rawTarget"
                           placeholder="Rp 0"
                           class="w-full bg-white/5 border theme-border rounded-xl px-4 py-3 text-xs focus:ring-brand-200 theme-text">
                </div>
                @error('target_amount') <p class="text-[9px] text-red-500 pl-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <button type="submit" wire:loading.attr="disabled"
                class="w-full py-3.5 theme-accent-bg text-white rounded-2xl text-[11px] font-bold shadow-lg shadow-brand-500/20 active:scale-95 transition-all mt-4 flex items-center justify-center gap-2">
            <span wire:loading.remove>{{ $savingId ? 'update' : 'create' }} goal</span>
            <span wire:loading class="flex items-center gap-2">
                <svg class="animate-spin h-3 w-3 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                processing...
            </span>
        </button>
    </form>
</div>
