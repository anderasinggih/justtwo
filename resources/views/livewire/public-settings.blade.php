<div class="max-w-xl mx-auto px-4 py-8 pb-32 space-y-8" 
     x-data="{ activeTab: 'general' }">
    
    {{-- HEADER --}}
    <div class="flex items-center gap-4 mb-10">
        <a href="{{ route('dashboard') }}" wire:navigate class="p-2 rounded-full hover:bg-white/5 transition-all">
            <svg class="w-6 h-6 theme-text" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h1 class="text-xl font-bold tracking-tight theme-text">Settings</h1>
    </div>

    {{-- TABS --}}
    <div class="flex border-b theme-border mb-6">
        <button @click="activeTab = 'general'" :class="activeTab === 'general' ? 'border-b-2 border-brand-500 theme-text' : 'opacity-40 theme-text'" class="pb-3 px-4 text-sm font-medium transition-all lowercase">General</button>
        <button @click="activeTab = 'banners'" :class="activeTab === 'banners' ? 'border-b-2 border-brand-500 theme-text' : 'opacity-40 theme-text'" class="pb-3 px-4 text-sm font-medium transition-all lowercase">Banners</button>
    </div>

    {{-- TAB 1: GENERAL --}}
    <div x-show="activeTab === 'general'" x-cloak class="space-y-6">
        <div class="md:bg-white/5 md:rounded-3xl md:p-6 space-y-6 md:border theme-border">
            {{-- Theme --}}
            <div class="space-y-2">
                <label class="text-[9px] md:text-[10px] font-bold uppercase tracking-widest theme-text opacity-40 px-1">Theme Appearance</label>
                <div class="flex flex-wrap gap-1.5 md:gap-2">
                    @foreach(['light', 'dark', 'rose', 'midnight', 'sky', 'mint', 'lavender', 'pink', 'mix'] as $t)
                        <button type="button" 
                                wire:click="$set('theme', '{{ $t }}')"
                                class="px-3 py-1.5 md:px-4 md:py-2 rounded-lg md:rounded-xl border text-[10px] md:text-xs font-medium transition-all {{ $theme === $t ? 'bg-brand-500 border-brand-500 text-white shadow-lg shadow-brand-500/20' : 'bg-white/5 border-white/10 theme-text opacity-50 hover:opacity-100' }}">
                            {{ $t }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- About Us --}}
            <div class="space-y-2">
                <label class="text-[9px] md:text-[10px] font-bold uppercase tracking-widest theme-text opacity-40 px-1">About Us / Description</label>
                <textarea wire:model="about_us" 
                          rows="3" 
                          placeholder="Tell your story..." 
                          class="w-full bg-white/5 border theme-border rounded-xl md:rounded-2xl px-4 py-2.5 text-xs md:text-sm theme-text focus:ring-1 focus:ring-brand-500 focus:border-brand-500 transition-all resize-none"></textarea>
                @error('about_us') <p class="text-[9px] text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Links --}}
            <div class="space-y-3 md:space-y-4">
                <div class="space-y-1.5">
                    <label class="text-[9px] md:text-[10px] font-bold uppercase tracking-widest theme-text opacity-40 px-1">YouTube Playlist Link (Watch All)</label>
                    <input type="url" wire:model="youtube_url" placeholder="https://youtube.com/playlist?list=..." class="w-full bg-white/5 border theme-border rounded-xl md:rounded-2xl px-4 py-2.5 text-xs md:text-sm theme-text focus:ring-1 focus:ring-brand-500 transition-all">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[9px] md:text-[10px] font-bold uppercase tracking-widest theme-text opacity-40 px-1">Journey Video 1 (Hero)</label>
                    <input type="url" wire:model="journey_video_url" placeholder="Video URL..." class="w-full bg-white/5 border theme-border rounded-xl md:rounded-2xl px-4 py-2.5 text-xs md:text-sm theme-text focus:ring-1 focus:ring-brand-500 transition-all">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[9px] md:text-[10px] font-bold uppercase tracking-widest theme-text opacity-40 px-1">Journey Video 2</label>
                    <input type="url" wire:model="journey_video_url_2" placeholder="Video URL..." class="w-full bg-white/5 border theme-border rounded-xl md:rounded-2xl px-4 py-2.5 text-xs md:text-sm theme-text focus:ring-1 focus:ring-brand-500 transition-all">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[9px] md:text-[10px] font-bold uppercase tracking-widest theme-text opacity-40 px-1">Spotify URL</label>
                    <input type="url" wire:model="spotify_url" placeholder="Spotify Link..." class="w-full bg-white/5 border theme-border rounded-xl md:rounded-2xl px-4 py-2.5 text-xs md:text-sm theme-text focus:ring-1 focus:ring-brand-500 transition-all">
                </div>
            </div>

            <div class="pt-2">
                <button type="button" wire:click="saveGeneral" wire:loading.attr="disabled" class="w-full py-3.5 bg-brand-500 text-white rounded-xl md:rounded-2xl font-bold hover:bg-brand-600 transition-all shadow-lg shadow-brand-500/20 active:scale-95 text-xs md:text-sm">
                    <span wire:loading.remove wire:target="saveGeneral">Update Settings</span>
                    <span wire:loading wire:target="saveGeneral">Saving...</span>
                </button>
            </div>
            @if($status_general)
                <p class="text-center text-[10px] theme-text opacity-50">{{ $status_general }}</p>
            @endif
        </div>
    </div>

    {{-- TAB 2: BANNERS --}}
    <div x-show="activeTab === 'banners'" x-cloak class="space-y-4 md:space-y-6">
        @for($i = 0; $i < 5; $i++)
            <div class="md:bg-white/5 md:rounded-3xl p-0 md:p-6 md:border theme-border space-y-4"
                 x-data="{ uploading: false, progress: 0 }"
                 x-on:livewire-upload-start="uploading = true"
                 x-on:livewire-upload-finish="uploading = false"
                 x-on:livewire-upload-error="uploading = false"
                 x-on:livewire-upload-progress="progress = $event.detail.progress">
                
                <div class="flex items-center justify-between border-b theme-border md:border-0 pb-2 md:pb-0">
                    <h3 class="text-[9px] md:text-xs font-bold uppercase tracking-widest theme-text opacity-40">Slot #{{ $i + 1 }}</h3>
                    @if(isset($existing_banners[$i]))
                        <button type="button" wire:click="removeExistingBanner({{ $i }})" class="text-[9px] md:text-[10px] text-red-500 font-bold hover:underline">Remove</button>
                    @endif
                </div>

                <div class="space-y-4">
                    {{-- Media Content --}}
                    <div>
                        @if (isset($existing_banners[$i]))
                            <div class="rounded-xl md:rounded-2xl overflow-hidden border theme-border bg-black/10 w-24 md:w-32" style="aspect-ratio: 16/9;">
                                @php
                                    $ext = pathinfo($existing_banners[$i], PATHINFO_EXTENSION);
                                    $isVid = in_array(strtolower($ext), ['mp4', 'mov', 'webm', 'ogg']);
                                @endphp
                                @if ($isVid)
                                    <video src="{{ Storage::url($existing_banners[$i]) }}" class="w-full h-full object-cover" autoplay loop muted playsinline></video>
                                @else
                                    <img src="{{ Storage::url($existing_banners[$i]) }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                        @elseif(isset($new_banners[$i]))
                            <div class="rounded-xl md:rounded-2xl overflow-hidden border theme-border relative bg-black/20 w-24 md:w-32" style="aspect-ratio: 16/9;">
                                @if (str_starts_with($new_banners[$i]->getMimeType(), 'video/'))
                                    <div class="w-full h-full flex flex-col items-center justify-center gap-1 md:gap-2">
                                        <svg class="w-5 h-5 md:w-8 md:h-8 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        <span class="text-[7px] md:text-[8px] font-bold uppercase opacity-40">Video</span>
                                    </div>
                                @else
                                    <img src="{{ $new_banners[$i]->temporaryUrl() }}" class="w-full h-full object-cover">
                                @endif
                                <button type="button" wire:click="removeNewBanner({{ $i }})" class="absolute top-1 right-1 p-0.5 md:p-1 bg-red-500 rounded-full text-white shadow-lg">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        @else
                            <label class="rounded-xl md:rounded-2xl border-2 border-dashed theme-border flex flex-col items-center justify-center cursor-pointer hover:bg-white/5 transition-all w-24 md:w-32" style="aspect-ratio: 16/9;">
                                <input type="file" wire:model="new_banners.{{ $i }}" class="hidden" accept="image/*,video/*">
                                <svg class="w-6 h-6 md:w-8 md:h-8 opacity-20 theme-text" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span class="text-[7px] md:text-[8px] font-bold uppercase opacity-40 mt-1 md:mt-2">Upload</span>
                            </label>
                        @endif

                        {{-- Progress Bar --}}
                        <div x-show="uploading" class="mt-2 w-24 md:w-32">
                            <div class="h-1 md:h-1.5 w-full bg-white/10 rounded-full overflow-hidden">
                                <div class="h-full bg-brand-500 transition-all duration-300" :style="'width: ' + progress + '%'"></div>
                            </div>
                            <p class="text-[7px] md:text-[8px] font-bold theme-text opacity-50 mt-1" x-text="progress + '%'"></p>
                        </div>
                    </div>

                    {{-- Text Fields --}}
                    <div class="space-y-2 md:space-y-3">
                        <div class="space-y-1">
                            <label class="text-[8px] md:text-[9px] font-bold uppercase opacity-30 px-1">Title</label>
                            <input type="text" wire:model="banner_titles.{{ $i }}" placeholder="Main Message" class="w-full bg-white/5 border theme-border rounded-lg md:rounded-xl px-3 py-1.5 md:px-4 md:py-2.5 text-[10px] md:text-xs theme-text focus:ring-1 focus:ring-brand-500 transition-all">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[8px] md:text-[9px] font-bold uppercase opacity-30 px-1">Subtitle</label>
                            <textarea wire:model="banner_subtitles.{{ $i }}" rows="2" placeholder="Sub-message..." class="w-full bg-white/5 border theme-border rounded-lg md:rounded-xl px-3 py-1.5 md:px-4 md:py-2.5 text-[9px] md:text-[10px] theme-text focus:ring-1 focus:ring-brand-500 transition-all resize-none"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        @endfor

        <div class="pt-2">
            <button type="button" wire:click="saveBanners" wire:loading.attr="disabled" class="w-full py-4 bg-brand-500 text-white rounded-xl md:rounded-3xl font-bold hover:bg-brand-600 transition-all shadow-lg shadow-brand-500/20 active:scale-95 text-xs md:text-sm">
                <span wire:loading.remove wire:target="saveBanners">Save All Banners</span>
                <span wire:loading wire:target="saveBanners">Saving...</span>
            </button>
        </div>
        @if($status_banners)
            <p class="text-center text-[10px] theme-text opacity-50">{{ $status_banners }}</p>
        @endif
    </div>
</div>
