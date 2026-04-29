<div class="min-h-screen theme-bg pb-20">
    {{-- Header --}}
    <header class="flex items-center justify-between px-4 h-14 border-b theme-border sticky top-0 theme-bg z-30">
        <a href="{{ route('profile') }}" wire:navigate class="text-xs font-bold theme-text lowercase">cancel</a>
        <h1 class="text-sm font-bold theme-text lowercase tracking-tighter">edit profile</h1>
        <button wire:click="updateProfile" class="text-xs font-bold text-blue-500 lowercase">done</button>
    </header>

    <div class="max-w-xl mx-auto px-4 py-8 space-y-12">
        {{-- Profile Photo Section --}}
        <div class="flex flex-col items-center gap-4">
            <div class="relative">
                <img src="{{ $profile_photo ? $profile_photo->temporaryUrl() : Auth::user()->profile_photo_url }}" 
                     class="w-24 h-24 rounded-full border theme-border object-cover">
                <div wire:loading wire:target="profile_photo" class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center">
                    <div class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                </div>
            </div>
            <label class="cursor-pointer">
                <span class="text-sm font-bold text-blue-500 lowercase">change profile photo</span>
                <input type="file" wire:model="profile_photo" class="hidden" accept="image/*">
            </label>
            @error('profile_photo') <p class="text-[10px] text-red-500 lowercase">{{ $message }}</p> @enderror
        </div>

        {{-- Form Fields --}}
        <div class="space-y-6">
            <div class="space-y-1 border-b theme-border pb-2">
                <label class="text-[10px] theme-text opacity-40 uppercase tracking-widest font-bold">name</label>
                <input type="text" wire:model="user_name" class="w-full bg-transparent border-none focus:ring-0 theme-text p-0 text-sm lowercase" placeholder="name">
                @error('user_name') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-1 border-b theme-border pb-2">
                <label class="text-[10px] theme-text opacity-40 uppercase tracking-widest font-bold">bio</label>
                <textarea wire:model="bio" rows="2" class="w-full bg-transparent border-none focus:ring-0 theme-text p-0 text-sm lowercase resize-none leading-relaxed" placeholder="write something about yourself..."></textarea>
                @error('bio') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Relationship Settings --}}
        <div class="pt-8 space-y-8">
            <div class="flex items-center gap-2">
                <div class="h-px flex-1 bg-gray-100/10"></div>
                <span class="text-[10px] font-bold theme-text opacity-20 uppercase tracking-widest">shared space</span>
                <div class="h-px flex-1 bg-gray-100/10"></div>
            </div>

            <div class="space-y-6">
                <div class="space-y-1 border-b theme-border pb-2">
                    <label class="text-[10px] theme-text opacity-40 uppercase tracking-widest font-bold">space name</label>
                    <input type="text" wire:model="relationship_name" class="w-full bg-transparent border-none focus:ring-0 theme-text p-0 text-sm lowercase" placeholder="our space name">
                </div>

                <div class="space-y-1 border-b theme-border pb-2">
                    <label class="text-[10px] theme-text opacity-40 uppercase tracking-widest font-bold">anniversary date</label>
                    <input type="date" wire:model="anniversary_date" class="w-full bg-transparent border-none focus:ring-0 theme-text p-0 text-sm lowercase">
                </div>

                <button wire:click="updateRelationship" class="w-full py-3 bg-white/5 border theme-border rounded-xl text-xs font-bold theme-text hover:bg-white/10 transition-all lowercase">
                    save shared settings
                </button>
            </div>
        </div>

        {{-- Export Section --}}
        <div class="pt-12">
            <button wire:click="exportMemories" class="w-full py-3 text-red-500 text-xs font-bold lowercase border border-red-500/20 rounded-xl hover:bg-red-500/5 transition-all">
                export all memories (.zip)
            </button>
        </div>
    </div>

    @if (session()->has('profile_success') || session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
             class="fixed bottom-24 left-1/2 -translate-x-1/2 bg-gray-900 text-white px-6 py-3 rounded-full text-xs shadow-2xl z-50">
            {{ session('profile_success') ?? session('success') }}
        </div>
    @endif
</div>
