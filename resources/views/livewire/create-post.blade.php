<div class="min-h-screen theme-bg" 
     x-data="{ 
        step: @entangle('step'),
        isEdit: @js($isEdit),
        localFiles: [],
        existingMedia: @js($existingMedia),
        croppedImages: {}, // Store base64 crops by index
        currentIndex: 0,
        cropper: null,
        isUploading: false,
        
        init() {
            if (this.isEdit && this.existingMedia.length > 0) {
                this.step = 2;
                // We don't initialize cropper for existing media unless they re-upload
            }
        },

        handleFiles(event) {
            const files = event.target.files;
            if (!files.length) return;
            
            this.localFiles = Array.from(files);
            this.step = 2;
            this.currentIndex = 0;
            this.croppedImages = {};
            this.existingMedia = []; // Clear existing if new files chosen
            
            this.$nextTick(() => {
                this.initCropper();
            });
        },

        initCropper() {
            if (this.cropper) {
                this.cropper.destroy();
            }

            const image = document.getElementById('cropper-image');
            if (!image) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                image.src = e.target.result;
                this.cropper = new Cropper(image, {
                    aspectRatio: 4 / 5,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 1,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: false,
                    cropBoxResizable: false,
                });
            };
            reader.readAsDataURL(this.localFiles[this.currentIndex]);
        },

        async selectPhoto(index) {
            if (this.localFiles.length === 0) return;

            // Save current crop before switching
            if (this.cropper) {
                const canvas = this.cropper.getCroppedCanvas({ width: 1080, height: 1350 });
                this.croppedImages[this.currentIndex] = canvas.toDataURL('image/jpeg', 0.9);
            }
            
            this.currentIndex = index;
            this.initCropper();
        },

        async submitPost() {
            this.isUploading = true;
            
            // 1. Save final crop if new files
            if (this.localFiles.length > 0 && this.cropper) {
                const canvas = this.cropper.getCroppedCanvas({ width: 1080, height: 1350 });
                this.croppedImages[this.currentIndex] = canvas.toDataURL('image/jpeg', 0.9);
            }
            
            const results = [];
            if (this.localFiles.length > 0) {
                for (let i = 0; i < this.localFiles.length; i++) {
                    if (this.croppedImages[i]) {
                        results.push(this.croppedImages[i]);
                    }
                }
                
                if (results.length === 0) {
                    this.isUploading = false;
                    alert('please wait for images to load');
                    return;
                }
            } else if (!this.isEdit) {
                // For new posts, images are mandatory
                this.isUploading = false;
                alert('please select at least one photo');
                return;
            }

            @this.savePost(results);
        }
    }">

    {{-- Cropper.js Assets --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    <style>
        .cropper-view-box { border-radius: 0; outline: 1px solid rgba(255,255,255,0.5); }
        .cropper-line, .cropper-point { display: none; }
        .cropper-container { background-color: #000; }
        [x-cloak] { display: none !important; }
    </style>

    {{-- Full Page Header --}}
    <header class="flex items-center justify-between px-6 h-14 border-b theme-border sticky top-0 theme-bg z-30">
        <a href="{{ url()->previous() }}" wire:navigate class="text-xs font-bold theme-text lowercase">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h2 class="text-sm font-bold theme-text lowercase" x-text="isEdit ? 'edit memory' : 'new memory'"></h2>
        <template x-if="step == 2">
            <button @click="submitPost" :disabled="isUploading" 
                    class="text-sm font-bold theme-accent lowercase disabled:opacity-30"
                    x-text="isEdit ? 'update' : 'share'">
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
                    <input type="file" multiple class="hidden" accept="image/*" @change="handleFiles">
                </label>
            </div>

            {{-- Step 2: Preview & Crop --}}
            <div x-show="step == 2" x-cloak class="flex flex-col">
                {{-- Cropper Container --}}
                <div class="w-full aspect-[4/5] bg-black relative overflow-hidden">
                    <template x-if="localFiles.length > 0">
                        <img id="cropper-image" class="max-w-full">
                    </template>
                    <template x-if="localFiles.length === 0 && existingMedia.length > 0">
                        <div class="w-full h-full">
                            <template x-for="(media, idx) in existingMedia">
                                <img x-show="currentIndex === idx" :src="'/storage/' + media.file_path_original" class="w-full h-full object-cover">
                            </template>
                        </div>
                    </template>
                    
                    {{-- Loading Overlay --}}
                    <div x-show="isUploading" class="absolute inset-0 z-50 bg-black/80 flex flex-col items-center justify-center space-y-4">
                        <div class="w-10 h-10 border-2 border-brand-500/30 border-t-brand-500 rounded-full animate-spin"></div>
                        <p class="text-[10px] text-white font-bold uppercase tracking-widest">posting memory...</p>
                    </div>
                </div>

                {{-- Photo Tray --}}
                <div class="p-4 flex gap-2 overflow-x-auto scrollbar-hide bg-black/5 border-b theme-border">
                    <template x-if="localFiles.length > 0">
                        <div class="flex gap-2">
                            <template x-for="(file, index) in localFiles">
                                <button @click="selectPhoto(index)" 
                                        class="shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 transition-all relative"
                                        :class="currentIndex === index ? 'border-brand-500 scale-95' : 'border-transparent opacity-50'">
                                    <img :src="URL.createObjectURL(file)" class="w-full h-full object-cover">
                                    <div x-show="croppedImages[index]" class="absolute inset-0 bg-brand-500/20 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </template>

                    <template x-if="localFiles.length === 0 && existingMedia.length > 0">
                        <div class="flex gap-2">
                            <template x-for="(media, index) in existingMedia">
                                <button @click="currentIndex = index" 
                                        class="shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 transition-all"
                                        :class="currentIndex === index ? 'border-brand-500 scale-95' : 'border-transparent opacity-50'">
                                    <img :src="'/storage/' + media.file_path_original" class="w-full h-full object-cover">
                                </button>
                            </template>
                        </div>
                    </template>
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
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
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
