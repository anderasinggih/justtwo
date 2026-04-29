<div class="min-h-screen theme-bg" 
     x-data="{ 
        step: @entangle('step'),
        localPreviews: [],
        uploadProgress: 0,
        isUploading: false,
        
        handleFiles(event) {
            const files = event.target.files;
            if (!files.length) return;
            
            this.isUploading = true;
            this.step = 2; // Instant transition
            
            // Generate local previews
            for (let i = 0; i < files.length; i++) {
                this.localPreviews.push(URL.createObjectURL(files[i]));
            }
        }
     }"
     x-on:livewire-upload-start="isUploading = true"
     x-on:livewire-upload-finish="isUploading = false; uploadProgress = 0"
     x-on:livewire-upload-error="isUploading = false"
     x-on:livewire-upload-progress="uploadProgress = $event.detail.progress">

    {{-- Full Page Header --}}
    <header class="flex items-center justify-between px-6 h-14 border-b theme-border sticky top-0 theme-bg z-30">
        <a href="{{ url()->previous() }}" wire:navigate class="text-sm font-bold theme-text lowercase">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h2 class="text-sm font-bold theme-text lowercase">new memory</h2>
        <template x-if="step == 2">
            <button wire:click="submit" :disabled="isUploading" 
                    class="text-sm font-bold theme-accent lowercase disabled:opacity-30">
                share
            </button>
        </template>
        <template x-if="step == 1">
            <div class="w-6"></div>
        </template>
    </header>

    <div class="max-w-xl mx-auto">
        {{-- Body --}}
        <div>
            {{-- Step 1: Select Photos --}}
            <div x-show="step == 1" class="flex flex-col items-center justify-center py-32 px-10 text-center space-y-6">
                <div class="w-24 h-24 rounded-full bg-white/5 border theme-border flex items-center justify-center theme-accent">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold theme-text lowercase">share a new moment</h3>
                    <p class="text-xs opacity-40 theme-text lowercase mt-2">select photos to get started.</p>
                </div>
                <label class="cursor-pointer theme-accent-bg text-white px-8 py-3 rounded-2xl text-sm font-bold hover:opacity-90 transition-all lowercase shadow-lg shadow-brand-500/20">
                    choose from gallery
                    <input type="file" wire:model="photos" multiple class="hidden" accept="image/*" @change="handleFiles">
                </label>
            </div>

            {{-- Step 2: Preview & Info --}}
            <div x-show="step == 2" x-cloak class="flex flex-col">
                {{-- Photo Preview --}}
                <div class="w-full aspect-[4/5] bg-black relative group overflow-hidden">
                    {{-- Uploading Overlay --}}
                    <div x-show="isUploading" class="absolute inset-0 z-20 bg-black/60 flex flex-col items-center justify-center space-y-4">
                        <div class="w-48 h-1 bg-white/20 rounded-full overflow-hidden">
                            <div class="h-full bg-brand-500 transition-all duration-300" :style="`width: ${uploadProgress}%`"></div>
                        </div>
                        <p class="text-[10px] text-white font-bold uppercase tracking-widest" x-text="`uploading ${uploadProgress}%`"></p>
                    </div>

                    <div class="flex overflow-x-auto snap-x snap-mandatory h-full scrollbar-hide">
                        {{-- Use local previews if photos are still uploading --}}
                        <template x-for="url in localPreviews">
                            <div class="shrink-0 w-full h-full snap-center flex items-center justify-center overflow-hidden">
                                <img :src="url" class="w-full h-full object-cover">
                            </div>
                        </template>
                    </div>
                    
                    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-1.5" x-show="localPreviews.length > 1">
                        <template x-for="(url, index) in localPreviews" :key="index">
                            <div class="w-1 h-1 rounded-full transition-all duration-300"
                                 :class="index === 0 ? 'bg-white' : 'bg-white/40'"></div>
                        </template>
                    </div>
                </div>

                {{-- Form Fields --}}
                <div class="p-6 space-y-8 pb-32">
                    <div class="flex items-start gap-4">
                        <img src="{{ Auth::user()->profile_photo_url }}" class="w-10 h-10 rounded-full border theme-border object-cover">
                        <textarea 
                            wire:model="caption" 
                            placeholder="write something about this memory..." 
                            class="flex-1 bg-transparent border-none focus:ring-0 text-sm theme-text p-0 resize-none min-h-[120px] lowercase leading-relaxed"
                        ></textarea>
                    </div>

                    <div class="space-y-6 border-t theme-border pt-6">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-white/5 border theme-border flex items-center justify-center opacity-40">
                                <svg class="w-4 h-4 theme-text" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <input 
                                wire:model="location" 
                                placeholder="add location" 
                                class="flex-1 bg-transparent border-none focus:ring-0 text-sm theme-text p-0 lowercase"
                            >
                        </div>

                        <div class="flex items-center justify-between p-4 bg-white/5 border theme-border rounded-2xl group cursor-pointer" x-on:click="$wire.is_public = !$wire.is_public">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-brand-500/10 flex items-center justify-center text-brand-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012-2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold theme-text lowercase">public memory</p>
                                    <p class="text-[10px] theme-text opacity-40 lowercase">show this on our welcome page</p>
                                </div>
                            </div>
                            <div class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="is_public" class="sr-only peer">
                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand-500"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
