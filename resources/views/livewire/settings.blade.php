<div class="min-h-screen theme-bg pb-20">
    {{-- Header --}}
    <header class="flex items-center justify-between px-4 h-14 border-b theme-border sticky top-0 theme-bg z-30">
        <a href="{{ route('app-settings') }}" wire:navigate class="theme-text opacity-70 hover:opacity-100 transition-opacity">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h1 class="text-sm font-bold theme-text lowercase tracking-tighter">edit profile</h1>
        <div class="w-6"></div>
    </header>

    <div class="max-w-xl mx-auto px-6 py-8 space-y-10">
        <div class="flex flex-col items-center gap-4">
            <div class="relative group">
                <img src="{{ $profile_photo ? $profile_photo->temporaryUrl() : Auth::user()->profile_photo_url }}" 
                     class="w-24 h-24 rounded-full border-2 theme-border object-cover shadow-xl group-hover:opacity-80 transition-all">
                <label class="absolute inset-0 cursor-pointer flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all">
                    <span class="bg-black/60 text-white text-[9px] px-2 py-1 rounded-full uppercase font-bold tracking-tighter">edit</span>
                    <input type="file" wire:model="profile_photo" class="hidden" accept="image/*">
                </label>
                <div wire:loading wire:target="profile_photo" class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center">
                    <div class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                </div>
            </div>
            <div class="text-center">
                <p class="text-xs font-bold theme-text">{{ Auth::user()->email }}</p>
                <p class="text-[10px] theme-text opacity-40 lowercase">your personal identity</p>
            </div>
        </div>

        <div class="space-y-6">
            <div class="space-y-1.5">
                <label class="text-[10px] theme-text opacity-40 uppercase tracking-widest font-bold px-1">name</label>
                <input type="text" wire:model="user_name" class="w-full bg-white/5 border theme-border rounded-xl px-4 py-3 text-sm theme-text focus:ring-1 focus:ring-brand-500 focus:border-brand-500 transition-all" placeholder="your name">
                @error('user_name') <p class="text-[10px] text-red-500 px-1">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] theme-text opacity-40 uppercase tracking-widest font-bold px-1">bio</label>
                <textarea wire:model="bio" rows="3" class="w-full bg-white/5 border theme-border rounded-xl px-4 py-3 text-sm theme-text focus:ring-1 focus:ring-brand-500 focus:border-brand-500 transition-all resize-none" placeholder="tell your story..."></textarea>
                @error('bio') <p class="text-[10px] text-red-500 px-1">{{ $message }}</p> @enderror
            </div>

            <button wire:click="updateProfile" class="w-full py-4 bg-brand-500 text-white rounded-2xl text-xs font-bold shadow-lg shadow-brand-500/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                save profile
            </button>
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
