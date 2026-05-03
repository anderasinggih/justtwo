<div class="max-w-2xl mx-auto px-2 pt-4 space-y-3.5 pb-32">
    {{-- Relationship Header (Compact) --}}
    <div class="theme-card border theme-border rounded-[1.5rem] p-4 shadow-sm flex items-center justify-between group">
        <div class="flex items-center gap-3">
            <div class="flex -space-x-3">
                <div class="w-10 h-10 rounded-full border-2 theme-border overflow-hidden bg-current/5 shadow-sm">
                    @if($user->profile_photo_path)
                        <img src="{{ Storage::disk('public')->url($user->profile_photo_path) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center theme-text opacity-20 text-[10px] font-bold uppercase">{{ substr($user->name, 0, 1) }}</div>
                    @endif
                </div>
                <div class="w-10 h-10 rounded-full border-2 theme-border overflow-hidden bg-current/5 shadow-sm">
                    @if($partner?->profile_photo_path)
                        <img src="{{ Storage::disk('public')->url($partner->profile_photo_path) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center theme-text opacity-20 text-[10px] font-bold uppercase">{{ substr($partner?->name ?? '?', 0, 1) }}</div>
                    @endif
                </div>
            </div>
            <div>
                <h2 class="text-[9px] font-bold theme-text opacity-30 uppercase tracking-[0.2em] leading-none mb-1">relationship</h2>
                <h1 class="text-sm font-bold theme-text lowercase tracking-tight">{{ $relationship->name }}</h1>
            </div>
        </div>
        <div class="w-8 h-8 rounded-full bg-brand-500/5 flex items-center justify-center theme-accent">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
        </div>
    </div>

    {{-- Stats Grid (Mini) --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
        <div class="theme-card border theme-border rounded-2xl p-3 text-center">
            <p class="text-sm font-bold theme-text tracking-tighter">{{ number_format($stats['days']) }}</p>
            <p class="text-[7px] font-bold opacity-30 theme-text uppercase tracking-widest">days</p>
        </div>
        <div class="theme-card border theme-border rounded-2xl p-3 text-center">
            <p class="text-sm font-bold theme-text tracking-tighter">{{ number_format($stats['memories']) }}</p>
            <p class="text-[7px] font-bold opacity-30 theme-text uppercase tracking-widest">posts</p>
        </div>
        <div class="theme-card border theme-border rounded-2xl p-3 text-center">
            <p class="text-sm font-bold theme-text tracking-tighter">{{ number_format($stats['photos']) }}</p>
            <p class="text-[7px] font-bold opacity-30 theme-text uppercase tracking-widest">photos</p>
        </div>
        <div class="theme-card border theme-border rounded-2xl p-3 text-center">
            <p class="text-sm font-bold theme-text tracking-tighter">Rp {{ number_format($stats['savings']/1000, 0) }}k</p>
            <p class="text-[7px] font-bold opacity-30 theme-text uppercase tracking-widest">saved</p>
        </div>
    </div>

    {{-- Theme Customizer (Small) --}}
    <div class="theme-card border theme-border rounded-[1.5rem] p-4 space-y-3">
        <div class="flex items-center justify-between px-1">
            <h3 class="text-[9px] font-bold theme-text opacity-30 uppercase tracking-widest">app appearance</h3>
            <span class="text-[8px] font-bold theme-accent uppercase tracking-widest">{{ Auth::user()->relationship->theme }}</span>
        </div>
        <div class="flex flex-wrap gap-3 justify-between px-1">
            @foreach($themes as $theme)
                <button wire:click="setTheme('{{ $theme['id'] }}')" 
                        class="group flex flex-col items-center gap-1.5 focus:outline-none">
                    <div class="w-7 h-7 rounded-full border-2 transition-all group-hover:scale-110 active:scale-90 {{ Auth::user()->relationship->theme === $theme['id'] ? 'theme-border shadow-[0_0_10px_rgba(var(--accent-color),0.3)] scale-110' : 'border-transparent opacity-40' }}"
                         style="background-color: {{ $theme['color'] }};">
                    </div>
                </button>
            @endforeach
        </div>
    </div>

    {{-- Privacy & Settings (Compact) --}}
    <div class="theme-card border theme-border rounded-[1.5rem] p-4 space-y-3">
        <div class="flex items-center justify-between px-1">
            <h3 class="text-[9px] font-bold theme-text opacity-30 uppercase tracking-widest">settings</h3>
        </div>
        <div class="space-y-1">
            <button wire:click="togglePublic" class="w-full flex items-center justify-between p-2 rounded-xl hover:bg-current/5 transition-all text-left group">
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-lg bg-brand-500/5 flex items-center justify-center theme-accent">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold theme-text lowercase">public profile</p>
                        <p class="text-[8px] opacity-30 theme-text">allow others to see your journey</p>
                    </div>
                </div>
                <div class="w-8 h-4 rounded-full relative transition-colors {{ $relationship->is_public ? 'theme-accent-bg' : 'bg-current/10' }}">
                    <div class="absolute top-0.5 transition-all w-3 h-3 bg-white rounded-full {{ $relationship->is_public ? 'left-[18px]' : 'left-0.5' }}"></div>
                </div>
            </button>

            <a href="{{ route('settings.public') }}" wire:navigate class="w-full flex items-center justify-between p-2 rounded-xl hover:bg-current/5 transition-all text-left group">
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-lg bg-brand-500/5 flex items-center justify-center theme-accent">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold theme-text lowercase">customize journey</p>
                        <p class="text-[8px] opacity-30 theme-text">edit public page content</p>
                    </div>
                </div>
                <svg class="w-3 h-3 opacity-20 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>
    </div>

    {{-- History / Milestones (Compact List) --}}
    <div class="theme-card border theme-border rounded-[1.5rem] p-4 space-y-3">
        <div class="flex items-center justify-between px-1">
            <h3 class="text-[9px] font-bold theme-text opacity-30 uppercase tracking-widest">our history</h3>
        </div>
        <div class="space-y-2">
            @forelse($milestones as $milestone)
                <div class="flex items-center justify-between p-2 bg-current/5 rounded-xl border theme-border border-dashed">
                    <div class="flex items-center gap-2.5">
                        <div class="text-[10px] theme-accent font-bold">#</div>
                        <div>
                            <p class="text-[10px] font-bold theme-text lowercase tracking-tight">{{ $milestone->title }}</p>
                            <p class="text-[7px] opacity-30 theme-text uppercase tracking-widest">{{ $milestone->event_date->format('M Y') }}</p>
                        </div>
                    </div>
                    <div class="text-[7px] font-bold opacity-20 theme-text uppercase">{{ $milestone->event_date->diffForHumans() }}</div>
                </div>
            @empty
                <p class="text-[9px] opacity-20 italic text-center py-2">history is being written...</p>
            @endforelse
        </div>
    </div>

    {{-- Logout (Danger Zone) --}}
    <button wire:click="logout" class="w-full py-3 theme-card border border-red-500/20 text-red-500/50 rounded-2xl text-[10px] font-bold uppercase tracking-widest hover:bg-red-500/5 transition-all active:scale-95">
        sign out of our space
    </button>
</div>
