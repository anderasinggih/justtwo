<div class="min-h-screen theme-bg pb-20">
    {{-- Header --}}
    <header class="flex items-center justify-between px-4 h-14 border-b theme-border sticky top-0 theme-bg z-30">
        <a href="{{ route('app-settings') }}" wire:navigate class="theme-text opacity-70 hover:opacity-100 transition-opacity">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h1 class="text-sm font-bold theme-text lowercase tracking-tighter">security settings</h1>
        <div class="w-6"></div>
    </header>

    <div class="max-w-xl mx-auto px-6 py-8 space-y-6">
        <div class="space-y-1.5">
            <label class="text-[10px] theme-text opacity-40 uppercase tracking-widest font-bold px-1">current password</label>
            <input type="password" wire:model="current_password" class="w-full bg-white/5 border theme-border rounded-xl px-4 py-3 text-sm theme-text focus:ring-1 focus:ring-brand-500 transition-all" placeholder="••••••••">
            @error('current_password') <p class="text-[10px] text-red-500 px-1">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-1.5">
            <label class="text-[10px] theme-text opacity-40 uppercase tracking-widest font-bold px-1">new password</label>
            <input type="password" wire:model="new_password" class="w-full bg-white/5 border theme-border rounded-xl px-4 py-3 text-sm theme-text focus:ring-1 focus:ring-brand-500 transition-all" placeholder="minimum 8 characters">
            @error('new_password') <p class="text-[10px] text-red-500 px-1">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-1.5">
            <label class="text-[10px] theme-text opacity-40 uppercase tracking-widest font-bold px-1">confirm new password</label>
            <input type="password" wire:model="new_password_confirmation" class="w-full bg-white/5 border theme-border rounded-xl px-4 py-3 text-sm theme-text focus:ring-1 focus:ring-brand-500 transition-all" placeholder="repeat new password">
        </div>

        <button wire:click="updatePassword" class="w-full py-4 bg-brand-500 text-white rounded-2xl text-xs font-bold shadow-lg shadow-brand-500/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
            update password
        </button>

        <div class="pt-6 border-t theme-border">
            <div class="flex items-center justify-between p-4 bg-white/5 border theme-border rounded-xl opacity-50">
                <div class="space-y-0.5">
                    <p class="text-xs font-bold theme-text">Two-Factor Authentication</p>
                    <p class="text-[10px] theme-text opacity-60">add an extra layer of security</p>
                </div>
                <div class="w-8 h-4 bg-gray-200/20 rounded-full relative">
                    <div class="absolute left-1 top-1 w-2 h-2 bg-white rounded-full"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Success Notification --}}
    @if (session()->has('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 3000)" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-4"
             class="fixed bottom-24 left-1/2 -translate-x-1/2 bg-gray-900 text-white px-6 py-3 rounded-full text-[11px] font-bold shadow-2xl z-50 flex items-center gap-2">
            <svg class="w-3 h-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif
</div>
