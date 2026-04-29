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
                {{-- Edit Profile --}}
                <a href="{{ route('settings', ['tab' => 'profile']) }}" wire:navigate class="flex items-center justify-between px-4 py-4 hover:bg-white/10 transition-colors border-b theme-border group">
                    <div class="flex items-center gap-4">
                        <svg class="w-5 h-5 theme-text opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold theme-text lowercase">edit profile</span>
                            <span class="text-[10px] theme-text opacity-40 lowercase">change names, bio & photos</span>
                        </div>
                    </div>
                    <svg class="w-4 h-4 opacity-20 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>

                {{-- Shared Space --}}
                <a href="{{ route('settings.space') }}" wire:navigate class="flex items-center justify-between px-4 py-4 hover:bg-white/10 transition-colors border-b theme-border group">
                    <div class="flex items-center gap-4">
                        <svg class="w-5 h-5 theme-text opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold theme-text lowercase">shared space</span>
                            <span class="text-[10px] theme-text opacity-40 lowercase">anniversary & sanctuary name</span>
                        </div>
                    </div>
                    <svg class="w-4 h-4 opacity-20 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>

                {{-- Security --}}
                <a href="{{ route('settings.security') }}" wire:navigate class="flex items-center justify-between px-4 py-4 hover:bg-white/10 transition-colors border-b theme-border group">
                    <div class="flex items-center gap-4">
                        <svg class="w-5 h-5 theme-text opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold theme-text lowercase">security</span>
                            <span class="text-[10px] theme-text opacity-40 lowercase">change password & 2fa</span>
                        </div>
                    </div>
                    <svg class="w-4 h-4 opacity-20 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>

                {{-- Privacy --}}
                <a href="{{ route('settings.privacy') }}" wire:navigate class="flex items-center justify-between px-4 py-4 hover:bg-white/10 transition-colors group">
                    <div class="flex items-center gap-4">
                        <svg class="w-5 h-5 theme-text opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold theme-text lowercase">privacy</span>
                            <span class="text-[10px] theme-text opacity-40 lowercase">visibility & comment controls</span>
                        </div>
                    </div>
                    <svg class="w-4 h-4 opacity-20 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>

        {{-- SECTION 2: PUBLIC PAGE --}}
        <div class="space-y-3">
            <h2 class="text-[11px] font-bold uppercase tracking-widest theme-text opacity-40 ml-1">public presence</h2>
            
            <div class="bg-white/5 rounded-2xl overflow-hidden border theme-border">
                <a href="{{ route('public-settings') }}" wire:navigate class="flex items-center justify-between px-4 py-4 hover:bg-white/10 transition-colors group">
                    <div class="flex items-center gap-4">
                        <svg class="w-5 h-5 theme-text opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold theme-text lowercase">public settings</span>
                            <span class="text-[10px] theme-text opacity-40 lowercase">manage landing page & themes</span>
                        </div>
                    </div>
                    <svg class="w-4 h-4 opacity-20 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>

        {{-- SECTION 3: APP INFO --}}
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
