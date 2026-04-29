<div class="min-h-screen theme-bg pb-20">
    {{-- Header --}}
    <header class="flex items-center justify-between px-4 h-14 border-b theme-border sticky top-0 theme-bg z-30">
        <a href="{{ route('app-settings') }}" wire:navigate class="theme-text opacity-70 hover:opacity-100 transition-opacity">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h1 class="text-sm font-bold theme-text lowercase tracking-tighter">privacy settings</h1>
        <div class="w-6"></div>
    </header>

    <div class="max-w-xl mx-auto px-6 py-8 space-y-8">
        <div class="space-y-6">
            <div class="flex items-center justify-between group">
                <div class="space-y-0.5">
                    <p class="text-xs font-bold theme-text">Public Feed Visibility</p>
                    <p class="text-[10px] theme-text opacity-40 max-w-[200px] leading-relaxed">allow your memories to be seen by others in the global public feed.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model="is_public" wire:change="updatePrivacy" class="sr-only peer">
                    <div class="w-11 h-6 bg-white/10 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-500"></div>
                </label>
            </div>

            <div class="flex items-center justify-between group">
                <div class="space-y-0.5">
                    <p class="text-xs font-bold theme-text">Allow Comments</p>
                    <p class="text-[10px] theme-text opacity-40 max-w-[200px] leading-relaxed">enable or disable the ability for others to comment on your posts.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model="allow_comments" wire:change="updatePrivacy" class="sr-only peer">
                    <div class="w-11 h-6 bg-white/10 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-500"></div>
                </label>
            </div>
        </div>

        <div class="pt-6 border-t theme-border space-y-4">
            <h3 class="text-[10px] theme-text opacity-20 uppercase tracking-widest font-bold px-1">data privacy</h3>
            <p class="text-[10px] theme-text opacity-40 leading-relaxed px-1">your data is encrypted and only accessible by you and your partner. we do not sell your personal memories to third parties.</p>
            <a href="#" class="inline-block text-[10px] text-brand-500 font-bold underline px-1">read privacy policy</a>
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
             class="fixed bottom-24 left-1/2 -translate-x-1/2 bg-gray-900 text-white px-6 py-3 rounded-full text-[11px] font-bold shadow-2xl z-50 flex items-center gap-2">
            <svg class="w-3 h-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif
</div>
