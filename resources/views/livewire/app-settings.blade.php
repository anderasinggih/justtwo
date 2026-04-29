<div class="max-w-xl mx-auto pb-20">
    {{-- Custom Header (Instagram Style) --}}
    <header class="flex items-center gap-4 px-4 h-14 sticky top-0 theme-bg z-30 border-b theme-border">
        <a href="{{ route('profile') }}" wire:navigate class="theme-text opacity-70 hover:opacity-100 transition-opacity">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h1 class="text-lg font-bold theme-text lowercase tracking-tighter">settings and privacy</h1>
    </header>

    <div class="px-4 py-6 space-y-8">
        {{-- SECTION 1: ACCOUNT --}}
        <div class="space-y-3">
            <h2 class="text-[11px] font-bold uppercase tracking-widest theme-text opacity-40 ml-1">your account</h2>
            
            <div class="bg-white/5 rounded-2xl overflow-hidden border theme-border">
                {{-- Public Settings --}}
                <a href="{{ route('public-settings') }}" wire:navigate class="flex items-center justify-between px-4 py-4 hover:bg-white/10 transition-colors border-b theme-border group">
                    <div class="flex items-center gap-4">
                        <svg class="w-5 h-5 theme-text opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold theme-text lowercase">public settings</span>
                            <span class="text-[10px] theme-text opacity-40 lowercase">manage welcome page & banners</span>
                        </div>
                    </div>
                    <svg class="w-4 h-4 opacity-20 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>

                {{-- Edit Profile --}}
                <a href="{{ route('settings') }}" wire:navigate class="flex items-center justify-between px-4 py-4 hover:bg-white/10 transition-colors group">
                    <div class="flex items-center gap-4">
                        <svg class="w-5 h-5 theme-text opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold theme-text lowercase">edit profile</span>
                            <span class="text-[10px] theme-text opacity-40 lowercase">change names, bio & photos</span>
                        </div>
                    </div>
                    <svg class="w-4 h-4 opacity-20 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>

        {{-- SECTION 2: APP INFO --}}
        <div class="space-y-3">
            <h2 class="text-[11px] font-bold uppercase tracking-widest theme-text opacity-40 ml-1">more info</h2>
            
            <div class="bg-white/5 rounded-2xl overflow-hidden border theme-border">
                <a href="{{ route('archived') }}" wire:navigate class="flex items-center justify-between px-4 py-4 hover:bg-white/10 transition-colors border-b theme-border group">
                    <div class="flex items-center gap-4">
                        <svg class="w-5 h-5 theme-text opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold theme-text lowercase">archive</span>
                            <span class="text-[10px] theme-text opacity-40 lowercase">manage hidden memories</span>
                        </div>
                    </div>
                    <svg class="w-4 h-4 opacity-20 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>

                <div class="px-4 py-4 flex items-center justify-between border-b theme-border">
                    <span class="text-sm font-bold theme-text lowercase">version</span>
                    <span class="text-xs theme-text opacity-40 font-bold">1.2.0</span>
                </div>
                <div class="px-4 py-4 flex items-center justify-between">
                    <span class="text-sm font-bold theme-text lowercase">private space for</span>
                    <span class="text-xs theme-text opacity-40 font-bold lowercase">{{ App\Models\Relationship::first()?->name }}</span>
                </div>
            </div>
        </div>

        {{-- SECTION 3: LOGIN --}}
        <div class="pt-4">
            <div class="bg-white/5 rounded-2xl overflow-hidden border theme-border">
                <button wire:click="logout" class="w-full px-4 py-4 flex items-center justify-center text-red-500 text-sm font-bold lowercase hover:bg-red-500/5 transition-colors">
                    log out
                </button>
            </div>
            <p class="text-[10px] text-center theme-text opacity-20 mt-6 lowercase tracking-tight">designed with love by justtwo</p>
        </div>
    </div>
</div>
