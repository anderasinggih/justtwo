<div class="max-w-xl mx-auto pb-12" x-data="{ tab: 'posts' }">
    {{-- IG Style Top Header --}}
    <header class="flex items-center justify-between px-4 h-14 sticky top-0 theme-bg z-30">
        <div class="w-10">
            <a href="{{ route('memories.create') }}" wire:navigate class="theme-text hover:opacity-70 transition-opacity">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            </a>
        </div>
        
        <div class="flex items-center gap-1">
            <h1 class="text-sm font-bold theme-text lowercase tracking-tighter">{{ Auth::user()->name }}</h1>
        </div>

        <div class="w-10 flex justify-end">
            <a href="{{ route('app-settings') }}" wire:navigate class="theme-text hover:opacity-70 transition-opacity">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </a>
        </div>
    </header>

    {{-- Profile Info Section --}}
    <div class="px-4 pt-6 pb-8">
        <div class="flex items-center gap-6 md:gap-10 mb-6">
            {{-- Avatar Group --}}
            <div class="relative flex shrink-0">
                <img src="{{ $user->profile_photo_url }}" 
                     class="w-20 h-20 md:w-24 md:h-24 rounded-full border-2 theme-border shadow-md object-cover relative z-10" alt="{{ $user->name }}">
                @if($partner)
                <img src="{{ $partner->profile_photo_url }}" 
                     class="w-16 h-16 md:w-20 md:h-20 rounded-full border-2 theme-border shadow-md object-cover -ml-6 mt-4 relative z-0" alt="{{ $partner->name }}">
                @endif
            </div>

            {{-- Stats (Instagram Style) --}}
            <div class="flex-1 flex justify-around items-center">
                <div class="text-center">
                    <p class="text-sm font-bold theme-text">{{ App\Models\Relationship::formatNumber($postsCount) }}</p>
                    <p class="text-[10px] opacity-40 theme-text lowercase">posts</p>
                </div>
                <div class="text-center">
                    <p class="text-sm font-bold theme-text">{{ App\Models\Relationship::formatNumber($daysTogether) }}</p>
                    <p class="text-[10px] opacity-40 theme-text lowercase">days</p>
                </div>
                <div class="text-center">
                    <p class="text-sm font-bold theme-text">{{ App\Models\Relationship::formatNumber($milestonesCount) }}</p>
                    <p class="text-[10px] opacity-40 theme-text lowercase">moments</p>
                </div>
            </div>
        </div>

        {{-- Bio --}}
        <div class="space-y-1">
            <h2 class="text-sm font-bold lowercase tracking-tight theme-text">{{ $user->name }}</h2>
            @if($user->bio)
                <p class="text-xs theme-text lowercase whitespace-pre-line leading-relaxed">{{ $user->bio }}</p>
            @endif
        </div>

        {{-- Action Buttons --}}
        <div class="flex gap-2 mt-6">
            <a href="{{ route('settings') }}" wire:navigate 
               class="flex-1 text-center py-1.5 bg-white/5 border theme-border theme-text text-[11px] font-bold rounded-lg transition-colors lowercase hover:bg-white/10">
                edit profile
            </a>
            <a href="{{ route('stats') }}" wire:navigate 
               class="flex-1 text-center py-1.5 bg-white/5 border theme-border theme-text text-[11px] font-bold rounded-lg transition-colors lowercase hover:bg-white/10">
                view stats
            </a>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="border-t theme-border flex justify-center gap-16">
        <button @click="tab = 'posts'" 
                class="border-t -mt-px py-3 flex items-center gap-2 transition-all"
                :class="tab === 'posts' ? 'theme-text border-white' : 'theme-text opacity-20 border-transparent'">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
        </button>
        <button @click="tab = 'bookmarks'" 
                class="border-t -mt-px py-3 flex items-center gap-2 transition-all"
                :class="tab === 'bookmarks' ? 'theme-text border-white' : 'theme-text opacity-20 border-transparent'">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
        </button>
    </div>

    {{-- Post Grid (Posts Tab) --}}
    <div x-show="tab === 'posts'" class="grid grid-cols-3 gap-0.5">
        @forelse($posts as $post)
            <a href="{{ route('timeline') }}?post={{ $post->id }}" wire:navigate 
               class="relative aspect-[4/5] bg-white/5 overflow-hidden">
                @php
                    $media = $post->media->first();
                @endphp
                @if($media)
                    <img src="{{ Storage::disk('public')->url($media->file_path_thumbnail ?? $media->file_path_original) }}" 
                         class="w-full h-full object-cover" alt="{{ $post->title }}">
                @else
                    <div class="w-full h-full flex items-center justify-center theme-text opacity-20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                @endif
                
                @if($post->media->count() > 1)
                <div class="absolute top-1.5 right-1.5 text-white/90">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 2H8a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2V4a2 2 0 00-2-2zM4 6H2v14a2 2 0 002 2h14v-2H4V6z"/></svg>
                </div>
                @endif
            </a>
        @empty
            <div class="col-span-3 py-20 text-center">
                <p class="text-xs theme-text opacity-20 lowercase italic">no shared memories yet.</p>
            </div>
        @endforelse
    </div>

    {{-- Bookmark Grid (Bookmarks Tab) --}}
    <div x-show="tab === 'bookmarks'" x-cloak class="grid grid-cols-3 gap-0.5">
        @forelse($bookmarkedPosts as $post)
            <a href="{{ route('timeline') }}?post={{ $post->id }}" wire:navigate 
               class="relative aspect-[4/5] bg-white/5 overflow-hidden">
                @php
                    $media = $post->media->first();
                @endphp
                @if($media)
                    <img src="{{ Storage::disk('public')->url($media->file_path_thumbnail ?? $media->file_path_original) }}" 
                         class="w-full h-full object-cover" alt="{{ $post->title }}">
                @else
                    <div class="w-full h-full flex items-center justify-center theme-text opacity-20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                @endif
            </a>
        @empty
            <div class="col-span-3 py-20 text-center">
                <div class="mb-4 flex justify-center">
                    <div class="w-16 h-16 rounded-full border-2 theme-border flex items-center justify-center opacity-20">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                    </div>
                </div>
                <h3 class="text-sm font-bold theme-text lowercase">save memories</h3>
                <p class="text-[10px] theme-text opacity-40 lowercase mt-1">memories you save will appear here.</p>
            </div>
        @endforelse
    </div>
</div>
