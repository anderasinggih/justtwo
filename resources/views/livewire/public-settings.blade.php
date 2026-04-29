<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 pb-32 space-y-12">
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" wire:navigate class="p-2 rounded-full bg-white/5 border theme-border theme-text hover:bg-white/10 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-lg md:text-2xl font-bold tracking-tighter lowercase theme-text">public settings</h1>
                <p class="text-[10px] md:text-sm theme-text opacity-50 lowercase">customize your landing page</p>
            </div>
        </div>
        <a href="{{ route('welcome') }}" target="_blank" class="text-xs bg-white/10 theme-border border px-4 py-2 rounded-full theme-text hover:bg-white/20 transition-all lowercase">view live site</a>
    </div>

    {{-- SECTION 1: TEXT & THEME --}}
    <div class="py-4 md:py-6 border-b theme-border">
        <h2 class="text-xs md:text-sm font-bold lowercase theme-text mb-4 md:mb-6 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-brand-500"></span>
            text & appearance
        </h2>

        <div class="space-y-6">
            {{-- Theme Selector --}}
            <div class="space-y-1.5 md:space-y-3">
                <label class="text-[9px] md:text-[10px] font-bold uppercase tracking-widest theme-text opacity-40 ml-2">theme</label>
                <div class="flex flex-wrap gap-2 md:gap-3 ml-2">
                    @foreach(['light', 'dark', 'rose', 'midnight'] as $t)
                        <button type="button" 
                                wire:click="$set('theme', '{{ $t }}')"
                                class="px-8 py-2 md:px-10 md:py-2.5 rounded-full border text-[10px] md:text-xs font-bold transition-all lowercase {{ $theme === $t ? 'bg-brand-500 border-brand-500 text-white shadow-lg shadow-brand-500/20' : 'bg-white/5 border-white/10 theme-text opacity-40 hover:opacity-100' }}">
                            {{ $t }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- About Us --}}
            <div class="space-y-1.5 md:space-y-3">
                <label class="text-[9px] md:text-[10px] font-bold uppercase tracking-widest theme-text opacity-40 ml-2">about us / description</label>
                <div class="ml-2">
                    <textarea wire:model="about_us" 
                              rows="4" 
                              placeholder="write about us here..." 
                              class="w-full bg-white/5 border theme-border rounded-xl md:rounded-2xl px-6 py-3 text-xs theme-text focus:ring-brand-500/20 focus:border-brand-500/30 transition-all lowercase resize-none"></textarea>
                    @error('about_us') <p class="text-[10px] text-red-500 mt-1 lowercase">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- YouTube Link --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4">
                <div class="space-y-1.5 md:space-y-3">
                    <label class="text-[9px] md:text-[10px] font-bold uppercase tracking-widest theme-text opacity-40 ml-2">youtube journey link (button)</label>
                    <div class="ml-2">
                        <input type="url" 
                               wire:model="youtube_url" 
                               placeholder="https://youtube.com/..." 
                               class="w-full bg-white/5 border theme-border rounded-xl md:rounded-2xl px-6 py-3 text-xs theme-text focus:ring-brand-500/20 focus:border-brand-500/30 transition-all lowercase">
                        @error('youtube_url') <p class="text-[10px] text-red-500 mt-1 lowercase">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="space-y-1.5 md:space-y-3">
                    <label class="text-[9px] md:text-[10px] font-bold uppercase tracking-widest theme-text opacity-40 ml-2">journey video 1 (left/top)</label>
                    <div class="ml-2">
                        <input type="url" 
                               wire:model="journey_video_url" 
                               placeholder="https://youtube.com/watch?v=..." 
                               class="w-full bg-white/5 border theme-border rounded-xl md:rounded-2xl px-6 py-3 text-xs theme-text focus:ring-brand-500/20 focus:border-brand-500/30 transition-all lowercase">
                        @error('journey_video_url') <p class="text-[10px] text-red-500 mt-1 lowercase">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="space-y-1.5 md:space-y-3">
                    <label class="text-[9px] md:text-[10px] font-bold uppercase tracking-widest theme-text opacity-40 ml-2">journey video 2 (right/bottom)</label>
                    <div class="ml-2">
                        <input type="url" 
                               wire:model="journey_video_url_2" 
                               placeholder="https://youtube.com/watch?v=..." 
                               class="w-full bg-white/5 border theme-border rounded-xl md:rounded-2xl px-6 py-3 text-xs theme-text focus:ring-brand-500/20 focus:border-brand-500/30 transition-all lowercase">
                        @error('journey_video_url_2') <p class="text-[10px] text-red-500 mt-1 lowercase">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="space-y-1.5 md:space-y-3">
                    <label class="text-[9px] md:text-[10px] font-bold uppercase tracking-widest theme-text opacity-40 ml-2">spotify playlist/song link</label>
                    <div class="ml-2">
                        <input type="url" 
                               wire:model="spotify_url" 
                               placeholder="https://open.spotify.com/..." 
                               class="w-full bg-white/5 border theme-border rounded-xl md:rounded-2xl px-6 py-3 text-xs theme-text focus:ring-brand-500/20 focus:border-brand-500/30 transition-all lowercase">
                        @error('spotify_url') <p class="text-[10px] text-red-500 mt-1 lowercase">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="col-span-full ml-3">
                    <p class="text-[8px] md:text-[9px] theme-text opacity-30 lowercase italic">
                        *journey link will be used for the button. embed links will replace the image banner if provided. you can provide one or two videos.
                    </p>
                </div>
            </div>

            <div class="pt-1 md:pt-4">
                <button type="button" wire:click="saveGeneral" wire:loading.attr="disabled" class="w-full py-2 md:py-3 bg-brand-500 text-white rounded-xl md:rounded-2xl font-bold hover:bg-brand-600 active:scale-[0.98] transition-all lowercase disabled:opacity-50 text-xs md:text-sm">
                    <span wire:loading.remove wire:target="saveGeneral">save changes</span>
                    <span wire:loading wire:target="saveGeneral">saving...</span>
                </button>
                @if($status_general)
                    <p class="text-center text-[10px] mt-2 lowercase theme-text opacity-50">{{ $status_general }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- SECTION 2: BANNERS & TEXTS --}}
    <div class="py-4 md:py-6">
        <h2 class="text-xs md:text-sm font-bold lowercase theme-text mb-4 md:mb-6 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-brand-500"></span>
            banner images & messages
        </h2>

        <div class="space-y-8">
            {{-- Banner Slots --}}
            @for($i = 0; $i < 5; $i++)
                <div class="py-4 md:py-6 border-b theme-border last:border-0 space-y-3 md:space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold uppercase tracking-widest theme-text opacity-40">banner #{{ $i + 1 }}</span>
                        @if(isset($existing_banners[$i]))
                            <button type="button" wire:click="removeExistingBanner({{ $i }})" class="text-[10px] text-red-500 hover:underline">remove image</button>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        {{-- Image Preview/Upload --}}
                        <div class="md:col-span-1">
                            @if (isset($existing_banners[$i]))
                                <div class="aspect-video rounded-xl overflow-hidden border theme-border relative group">
                                    @php
                                        $ext = pathinfo($existing_banners[$i], PATHINFO_EXTENSION);
                                        $isVid = in_array(strtolower($ext), ['mp4', 'mov', 'webm', 'ogg']);
                                    @endphp
                                    @if ($isVid)
                                        <video src="{{ Storage::url($existing_banners[$i]) }}"
                                            class="w-full h-full object-cover" muted></video>
                                    @else
                                        <img src="{{ Storage::url($existing_banners[$i]) }}"
                                            class="w-full h-full object-cover">
                                    @endif
                                </div>
                            @elseif(isset($new_banners[$i]))
                                <div class="aspect-video rounded-xl overflow-hidden border theme-border bg-black/20 flex items-center justify-center relative">
                                    @if (str_starts_with($new_banners[$i]->getMimeType(), 'video/'))
                                        <div class="flex flex-col items-center gap-1">
                                            <svg class="w-6 h-6 theme-text opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                            <span class="text-[8px] theme-text opacity-50 uppercase tracking-tighter">video ready</span>
                                        </div>
                                    @else
                                        <img src="{{ $new_banners[$i]->temporaryUrl() }}"
                                            class="w-full h-full object-cover">
                                    @endif
                                    <button type="button" wire:click="removeNewBanner({{ $i }})"
                                        class="absolute top-2 right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center shadow-lg hover:bg-red-600 transition-all z-10">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            @else
                                <label
                                    class="aspect-video rounded-xl border-2 border-dashed theme-border flex flex-col items-center justify-center cursor-pointer hover:bg-white/5 transition-all">
                                    <input type="file" wire:model="new_banners.{{ $i }}" class="hidden"
                                        accept="image/*,video/*">
                                    <svg class="w-6 h-6 theme-text opacity-30" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    <span class="text-[8px] theme-text opacity-30 mt-1 uppercase tracking-tighter">upload
                                        16:9</span>
                                </label>
                            @endif
                        </div>

                        {{-- Text Fields --}}
                        <div class="md:col-span-3 space-y-3">
                            <textarea wire:model="banner_titles.{{ $i }}" rows="2" class="w-full bg-white/5 border theme-border rounded-xl px-4 py-2 text-xs theme-text resize-none" placeholder="banner title #{{ $i + 1 }}"></textarea>
                            <textarea wire:model="banner_subtitles.{{ $i }}" rows="2" class="w-full bg-white/5 border theme-border rounded-xl px-4 py-2 text-[10px] theme-text resize-none" placeholder="banner subtitle #{{ $i + 1 }}"></textarea>
                        </div>
                    </div>
                </div>
            @endfor
            
            <div wire:loading wire:target="new_banners" class="text-[10px] theme-text opacity-50 lowercase ml-2 animate-pulse">processing uploads...</div>
            @error('new_banners.*') <p class="text-[10px] text-red-500 ml-2 mt-1">{{ $message }}</p> @enderror

            <div class="pt-2">
                <button type="button" wire:click="saveBanners" wire:loading.attr="disabled" class="w-full py-3 bg-brand-500 text-white rounded-2xl font-bold hover:bg-brand-600 active:scale-[0.98] transition-all lowercase disabled:opacity-50 shadow-lg shadow-brand-500/20">
                    <span wire:loading.remove wire:target="saveBanners">save all banners & text</span>
                    <span wire:loading wire:target="saveBanners">saving...</span>
                </button>
                @if($status_banners)
                    <p class="text-center text-[10px] mt-2 lowercase theme-text opacity-50">{{ $status_banners }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
