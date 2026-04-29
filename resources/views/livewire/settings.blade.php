<div class="max-w-2xl mx-auto py-6 px-4 space-y-8">
    <div class="text-center">
        <h1 class="text-2xl font-bold tracking-tight lowercase theme-text">Settings</h1>
        <p class="text-xs opacity-50 theme-text lowercase">customize your space and account.</p>
    </div>

    <div class="space-y-8">
        {{-- Shared Space --}}
        <section class="space-y-4">
            <div class="px-1">
                <h2 class="text-sm font-bold lowercase theme-text">shared space</h2>
                <p class="text-[10px] opacity-40 theme-text lowercase">this affects both you and your partner.</p>
            </div>
            
            <div class="theme-card rounded-2xl border theme-border shadow-sm p-6">
                <form wire:submit.prevent="updateRelationship" class="space-y-4">
                    <div>
                        <label class="block text-[10px] opacity-40 theme-text mb-1 lowercase">space name</label>
                        <x-ui.input wire:model="relationship_name" class="text-sm py-2 bg-white/5 border theme-border theme-text" />
                    </div>
                    <div>
                        <label class="block text-[10px] opacity-40 theme-text mb-1 lowercase">our anniversary</label>
                        <x-ui.input type="date" wire:model="anniversary_date" class="text-sm py-2 bg-white/5 border theme-border theme-text" />
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold opacity-40 theme-text mb-3 uppercase tracking-widest px-1">theme</label>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach([
                                'light' => ['label' => 'light', 'color' => 'bg-white', 'text' => 'text-gray-900'],
                                'dark' => ['label' => 'dark', 'color' => 'bg-black', 'text' => 'text-white'],
                                'rose' => ['label' => 'rose', 'color' => 'bg-rose-50', 'text' => 'text-rose-900'],
                                'midnight' => ['label' => 'midnight', 'color' => 'bg-slate-900', 'text' => 'text-blue-100']
                            ] as $value => $cfg)
                                <button type="button" wire:click="$set('theme', '{{ $value }}')" 
                                        class="relative rounded-xl p-3 border-2 transition-all duration-300 {{ $theme === $value ? 'border-brand-500 ring-2 ring-brand-50' : 'theme-border' }} {{ $cfg['color'] }}">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded-full border border-black/5 {{ $cfg['color'] }}"></div>
                                        <span class="text-[10px] font-bold uppercase tracking-tighter {{ $cfg['text'] }}">{{ $cfg['label'] }}</span>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    @if(session('success'))
                        <p class="text-xs text-green-500 lowercase">{{ session('success') }}</p>
                    @endif

                    <div class="flex justify-end">
                        <x-ui.button type="submit" size="sm" class="text-xs py-2">save changes</x-ui.button>
                    </div>
                </form>
            </div>
        </section>

        {{-- Profile --}}
        <section class="space-y-4">
            <div class="px-1">
                <h2 class="text-sm font-bold lowercase theme-text">your profile</h2>
                <p class="text-[10px] opacity-40 theme-text lowercase">manage your identity.</p>
            </div>

            <div class="theme-card rounded-2xl border theme-border shadow-sm p-6">
                <form wire:submit.prevent="updateProfile" class="space-y-4">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="relative group cursor-pointer" onclick="document.getElementById('profile_photo_input').click()">
                            <img src="{{ $profile_photo ? $profile_photo->temporaryUrl() : Auth::user()->profile_photo_url }}" 
                                 class="w-16 h-16 rounded-full object-cover border-2 theme-border shadow-sm">
                            <div class="absolute inset-0 bg-black/40 rounded-full opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                            </div>
                            <input type="file" id="profile_photo_input" wire:model="profile_photo" class="hidden" accept="image/*">
                        </div>
                        <div>
                            <p class="text-xs font-bold lowercase theme-text">{{ Auth::user()->name }}</p>
                            <button type="button" onclick="document.getElementById('profile_photo_input').click()" class="text-[10px] theme-accent font-bold lowercase">change photo</button>
                        </div>
                        <div wire:loading wire:target="profile_photo" class="text-[10px] theme-accent lowercase animate-pulse">uploading...</div>
                        @error('profile_photo') <p class="text-[10px] text-red-500 lowercase">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] opacity-40 theme-text mb-1 lowercase">display name</label>
                        <x-ui.input wire:model="user_name" class="text-sm py-2 bg-white/5 border theme-border theme-text" />
                    </div>

                    @if(session('profile_success'))
                        <p class="text-xs text-green-500 lowercase">{{ session('profile_success') }}</p>
                    @endif

                    <div class="flex justify-end">
                        <x-ui.button type="submit" size="sm" class="text-xs py-2">update</x-ui.button>
                    </div>
                </form>
            </div>
        </section>

        {{-- Invite & Data --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="theme-card rounded-2xl border theme-border shadow-sm p-4 space-y-2">
                <h3 class="text-[10px] font-bold opacity-40 theme-text uppercase tracking-widest">invite code</h3>
                <div class="flex items-center justify-between">
                    <p class="font-mono text-xl font-bold theme-accent">{{ Auth::user()->relationship->invite_code }}</p>
                    <button onclick="navigator.clipboard.writeText('{{ Auth::user()->relationship->invite_code }}'); alert('copied!')" 
                            class="text-[10px] font-bold theme-accent lowercase">copy</button>
                </div>
            </div>

            <div class="theme-card rounded-2xl border theme-border shadow-sm p-4 space-y-2">
                <h3 class="text-[10px] font-bold opacity-40 theme-text uppercase tracking-widest">export memories</h3>
                <button wire:click="exportMemories" class="w-full text-left flex items-center justify-between group">
                    <span class="text-[10px] opacity-40 theme-text lowercase">download all photos</span>
                    <svg class="w-4 h-4 opacity-20 theme-text group-hover:theme-accent transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                </button>
            </div>
        </div>
    </div>
</div>
