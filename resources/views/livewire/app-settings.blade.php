<div class="max-w-xl mx-auto px-4 pt-6 pb-20 space-y-8">
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('profile') }}" wire:navigate class="p-2 theme-text opacity-50 hover:opacity-100 transition-opacity -ml-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h1 class="text-xl font-bold theme-text lowercase tracking-tighter">settings</h1>
    </div>

    <div class="space-y-2">
        <h2 class="text-[10px] font-bold uppercase tracking-widest theme-text opacity-40 ml-2 mb-4">account & public</h2>
        
        <div class="bg-white/5 border theme-border rounded-[2rem] overflow-hidden">
            {{-- Public Settings --}}
            <a href="{{ route('public-settings') }}" wire:navigate class="flex items-center justify-between px-6 py-5 hover:bg-white/10 transition-colors border-b theme-border group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-brand-50 rounded-full flex items-center justify-center text-brand-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-bold theme-text lowercase">public settings</span>
                        <span class="text-[10px] theme-text opacity-40 lowercase">manage welcome page & banners</span>
                    </div>
                </div>
                <svg class="w-4 h-4 opacity-20 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>

            {{-- Edit Profile --}}
            <a href="{{ route('settings') }}" wire:navigate class="flex items-center justify-between px-6 py-5 hover:bg-white/10 transition-colors group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-gray-50 rounded-full flex items-center justify-center text-gray-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-bold theme-text lowercase">edit profile</span>
                        <span class="text-[10px] theme-text opacity-40 lowercase">change names & photos</span>
                    </div>
                </div>
                <svg class="w-4 h-4 opacity-20 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>
    </div>

    <div class="space-y-2">
        <h2 class="text-[10px] font-bold uppercase tracking-widest theme-text opacity-40 ml-2 mb-4">app info</h2>
        
        <div class="bg-white/5 border theme-border rounded-[2rem] overflow-hidden">
            <div class="px-6 py-5 flex items-center justify-between">
                <span class="text-sm font-medium theme-text lowercase opacity-60">version</span>
                <span class="text-xs font-bold theme-text">1.2.0</span>
            </div>
            <div class="px-6 py-5 border-t theme-border flex items-center justify-between">
                <span class="text-sm font-medium theme-text lowercase opacity-60">private space for</span>
                <span class="text-xs font-bold theme-text lowercase">{{ App\Models\Relationship::first()?->name }}</span>
            </div>
        </div>
    </div>

    <div class="pt-6">
        <button wire:click="logout" class="w-full py-4 bg-red-500/10 text-red-500 rounded-[2rem] font-bold hover:bg-red-500/20 transition-all lowercase">
            logout
        </button>
    </div>
</div>
