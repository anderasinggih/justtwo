<div class="min-h-screen theme-bg select-none" 
     x-data="{ 
        step: @entangle('step'),
        isEdit: @js($isEdit),
        localItems: [], // { id, file, url, location, crop }
        existingMedia: @js($existingMedia),
        currentIndex: 0,
        cropper: null,
        isUploading: false,
        isExtracting: false,
        
        init() {
            if (this.isEdit && this.existingMedia.length > 0) {
                this.step = 2;
            }
        },

        reorder(oldIndex, newIndex) {
            if (oldIndex === newIndex) return;
            const items = [...this.localItems];
            const item = items.splice(oldIndex, 1)[0];
            items.splice(newIndex, 0, item);
            this.localItems = items;

            if (this.currentIndex === oldIndex) {
                this.currentIndex = newIndex;
            } else if (this.currentIndex > oldIndex && this.currentIndex <= newIndex) {
                this.currentIndex--;
            } else if (this.currentIndex < oldIndex && this.currentIndex >= newIndex) {
                this.currentIndex++;
            }
            
            this.$nextTick(() => this.initCropper());
        },

        async handleFiles(event) {
            const files = Array.from(event.target.files);
            if (!files.length) return;
            
            this.step = 2;
            this.currentIndex = 0;
            this.existingMedia = [];
            
            const newItems = files.map((file, i) => ({
                id: Date.now() + Math.random(),
                file: file,
                url: URL.createObjectURL(file),
                location: '',
                crop: null
            }));

            this.localItems = newItems;

            this.isExtracting = true;
            for (let i = 0; i < this.localItems.length; i++) {
                const loc = await window.extractLocation(this.localItems[i].file);
                if (loc) {
                    this.localItems[i].location = loc.toLowerCase();
                    if (i === 0 && !this.$wire.location) {
                        this.$wire.location = loc.toLowerCase();
                    }
                }
            }
            this.isExtracting = false;
            
            this.$nextTick(() => this.initCropper());
        },

        async addMoreFiles(event) {
            const files = Array.from(event.target.files);
            if (!files.length) return;

            this.isExtracting = true;
            for (const file of files) {
                const loc = await window.extractLocation(file);
                this.localItems.push({
                    id: Date.now() + Math.random(),
                    file: file,
                    url: URL.createObjectURL(file),
                    location: loc ? loc.toLowerCase() : '',
                    crop: null
                });
            }
            this.isExtracting = false;
        },

        initCropper() {
            if (this.cropper) this.cropper.destroy();
            const image = document.getElementById('cropper-image');
            if (!image || !this.localItems[this.currentIndex]) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                image.src = e.target.result;
                this.cropper = new Cropper(image, {
                    aspectRatio: 4 / 5,
                    viewMode: 3, // Fill container
                    dragMode: 'move', // Image moves, not box
                    autoCropArea: 1,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: false,
                    cropBoxResizable: false,
                    toggleDragModeOnDblclick: false,
                    background: false,
                    modal: true,
                });
            };
            reader.readAsDataURL(this.localItems[this.currentIndex].file);
        },

        async selectPhoto(index) {
            if (this.localItems.length === 0) return;
            if (this.cropper) {
                const canvas = this.cropper.getCroppedCanvas({ width: 1080, height: 1350 });
                this.localItems[this.currentIndex].crop = canvas.toDataURL('image/jpeg', 0.9);
            }
            this.currentIndex = index;
            this.initCropper();
        },

        async submitPost() {
            this.isUploading = true;
            if (this.localItems.length > 0 && this.cropper) {
                const canvas = this.cropper.getCroppedCanvas({ width: 1080, height: 1350 });
                this.localItems[this.currentIndex].crop = canvas.toDataURL('image/jpeg', 0.9);
            }
            
            const results = this.localItems.map(item => item.crop).filter(c => c);
            const locations = this.localItems.map(item => item.location || null);
            const keepIds = this.existingMedia.map(m => m.id);

            if (results.length === 0 && keepIds.length === 0 && !this.isEdit && !this.$wire.is_secret) {
                this.isUploading = false;
                alert('please select at least one photo');
                return;
            }

            @this.savePost(results, keepIds, locations);
        },

        removePhoto(index) {
            if (this.localItems.length === 1) {
                this.localItems = [];
                if (!this.isEdit) this.step = 1;
                if (this.cropper) this.cropper.destroy();
                return;
            }
            this.localItems.splice(index, 1);
            if (this.currentIndex === index) {
                this.currentIndex = Math.max(0, index - 1);
                this.initCropper();
            } else if (this.currentIndex > index) {
                this.currentIndex--;
            }
        },

        removeExistingPhoto(index) {
            this.existingMedia.splice(index, 1);
            if (this.currentIndex === index) {
                this.currentIndex = Math.max(0, index - 1);
            } else if (this.currentIndex > index) {
                this.currentIndex--;
            }
            
            if (this.existingMedia.length === 0 && this.localItems.length === 0) {
                this.step = 1;
            }
        }
    }">

    {{-- Cropper.js & SortableJS Assets --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/exif-js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <script>
        window.extractLocation = async (file) => {
            return new Promise((resolve) => {
                EXIF.getData(file, function() {
                    const lat = EXIF.getTag(this, "GPSLatitude");
                    const lon = EXIF.getTag(this, "GPSLongitude");
                    const latRef = EXIF.getTag(this, "GPSLatitudeRef") || "N";
                    const lonRef = EXIF.getTag(this, "GPSLongitudeRef") || "E";

                    if (!lat || !lon) {
                        resolve(null);
                        return;
                    }

                    const toDecimal = (number, ref) => {
                        let dec = number[0] + number[1] / 60 + number[2] / 3600;
                        if (ref === "S" || ref === "W") dec = dec * -1;
                        return dec;
                    };

                    const decimalLat = toDecimal(lat, latRef);
                    const decimalLon = toDecimal(lon, lonRef);

                    // Reverse Geocode using Nominatim (Free)
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${decimalLat}&lon=${decimalLon}&zoom=18&addressdetails=1`, {
                        headers: { 'User-Agent': 'JustTwo-App' }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.address) {
                            const addr = data.address;
                            let parts = [];
                            
                            // Specific point of interest
                            const poi = addr.amenity || addr.historic || addr.shop || addr.tourism || addr.leisure || addr.building;
                            if (poi) parts.push(poi);
                            
                            // Road / Street
                            if (addr.road) parts.push(addr.road);
                            
                            // Local area
                            const local = addr.suburb || addr.neighbourhood || addr.village || addr.hamlet;
                            if (local) parts.push(local);
                            
                            // City/Town
                            const city = addr.city || addr.town || addr.municipality;
                            if (city) parts.push(city);

                            // Fallback if parts is empty
                            let loc = parts.length > 0 ? parts.join(', ') : data.display_name.split(',').slice(0, 2).join(', ');
                            
                            // Limit length to keep it clean
                            if (loc.length > 50) {
                                loc = loc.substring(0, 47) + '...';
                            }

                            resolve(loc);
                        } else {
                            resolve(null);
                        }
                    })
                    .catch(() => resolve(null));
                });
            });
        };
    </script>

    <style>
        .cropper-view-box { border-radius: 0; outline: 1px solid rgba(255,255,255,0.3) !important; }
        .cropper-line, .cropper-point { display: none; }
        .cropper-container { background-color: #000; }
        .cropper-modal { opacity: 1 !important; background-color: #000 !important; }
        .cropper-bg { background-image: none !important; }
        [x-cloak] { display: none !important; }
    </style>

    {{-- Full Page Header --}}
    <header class="flex items-center justify-between px-6 h-14 border-b theme-border sticky top-0 theme-bg z-30">
        <div class="flex items-center">
            {{-- Back to Dashboard (Step 1) or Back to Step 1 (Step 2) --}}
            <button x-show="step == 2" @click="step = 1; $wire.is_secret = false; localFiles = []" class="text-xs font-bold theme-text lowercase">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <a x-show="step == 1" href="{{ route('dashboard') }}" wire:navigate class="text-xs font-bold theme-text lowercase">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
        </div>
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
            {{-- Step 1: Select Type --}}
            <div x-show="step == 1" class="flex flex-col items-center justify-center py-20 px-6 text-center space-y-12">
                <div class="space-y-2">
                    <h3 class="text-2xl font-bold theme-text lowercase">what's on your mind?</h3>
                    <p class="text-xs opacity-40 theme-text lowercase">choose how you want to share this moment.</p>
                </div>

                <div class="w-full max-w-sm space-y-4">
                    {{-- Normal Gallery Post --}}
                    <label class="group relative flex items-center gap-4 p-5 bg-white/5 border theme-border rounded-3xl cursor-pointer hover:bg-brand-500/5 hover:border-brand-500/30 transition-all">
                        <div class="w-12 h-12 rounded-2xl bg-brand-500/10 flex items-center justify-center text-brand-500 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div class="text-left">
                            <p class="text-sm font-bold theme-text lowercase">choose from gallery</p>
                            <p class="text-[10px] theme-text opacity-40 lowercase">share your photos and stories.</p>
                        </div>
                        <input type="file" multiple class="hidden" accept="image/*" @change="handleFiles">
                    </label>

                    {{-- Secret Note Post --}}
                    <button type="button"
                            @click="step = 2; $wire.is_secret = true" 
                            class="w-full group relative flex items-center gap-4 p-5 bg-white/5 border theme-border rounded-3xl cursor-pointer hover:bg-indigo-500/5 hover:border-indigo-500/30 transition-all text-left">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 flex items-center justify-center text-indigo-500 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <div class="text-left">
                            <p class="text-sm font-bold theme-text lowercase">create secret note</p>
                            <p class="text-[10px] theme-text opacity-40 lowercase">lock a message for later.</p>
                        </div>
                    </button>
                </div>
            </div>

            {{-- Step 2: Preview & Crop --}}
            <div x-show="step == 2" x-cloak class="flex flex-col">
                {{-- Cropper Container (Hide for Secret Note) --}}
                <div x-show="!$wire.is_secret" class="w-full aspect-[4/5] bg-black relative overflow-hidden">
                    <template x-if="localItems.length > 0">
                        <img id="cropper-image" class="max-w-full">
                    </template>
                    <template x-if="localItems.length === 0 && existingMedia.length > 0">
                        <div class="w-full h-full">
                            <template x-for="(media, idx) in existingMedia" :key="idx">
                                <img x-show="currentIndex === idx" :src="'/storage/' + media.file_path_original" class="w-full h-full object-cover">
                            </template>
                        </div>
                    </template>
                    
                    {{-- Location Badge --}}
                    <div x-show="localItems[currentIndex]?.location" x-cloak
                         class="absolute bottom-4 left-4 z-20 flex items-center gap-1.5 px-3 py-1.5 bg-black/40 backdrop-blur-md rounded-full border border-white/10">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span class="text-[10px] text-white font-bold lowercase tracking-wide" x-text="localItems[currentIndex].location"></span>
                    </div>

                    {{-- Loading Overlay --}}
                    <div x-show="isUploading || isExtracting" class="absolute inset-0 z-50 bg-black/80 flex flex-col items-center justify-center space-y-4">
                        <div class="w-10 h-10 border-2 border-brand-500/30 border-t-brand-500 rounded-full animate-spin"></div>
                        <p class="text-[10px] text-white font-bold uppercase tracking-widest" x-text="isExtracting ? 'detecting locations...' : 'posting memory...'"></p>
                    </div>
                </div>

                <div x-show="!$wire.is_secret" class="p-4 flex gap-2 overflow-x-auto scrollbar-hide bg-black/5 border-b theme-border">
                    <template x-if="localItems.length > 0">
                        <div class="flex gap-2">
                            <div class="flex gap-2" 
                                 x-init="new Sortable($el, { 
                                     animation: 150, 
                                     ghostClass: 'opacity-10', 
                                     draggable: '.draggable-item',
                                     onEnd: (evt) => {
                                         // Temporarily disable the actual DOM move by Sortable 
                                         // to let Alpine handle the re-render from the array update
                                         const item = evt.item;
                                         const parent = item.parentNode;
                                         if (evt.newIndex > evt.oldIndex) {
                                             parent.insertBefore(item, parent.children[evt.oldIndex]);
                                         } else {
                                             parent.insertBefore(item, parent.children[evt.oldIndex + 1]);
                                         }
                                         
                                         // Now update Alpine state
                                         reorder(evt.oldIndex, evt.newIndex);
                                     } 
                                 })">
                                <template x-for="(item, index) in localItems" :key="item.id">
                                    <div class="draggable-item shrink-0 relative cursor-grab active:cursor-grabbing">
                                        <button @click="selectPhoto(index)" 
                                                class="w-16 h-16 rounded-lg overflow-hidden border-2 transition-all relative"
                                                :class="currentIndex === index ? 'border-brand-500 scale-95' : 'border-transparent opacity-50'">
                                            <img :src="item.url" class="w-full h-full object-cover">
                                            <div x-show="item.crop" class="absolute inset-0 bg-brand-500/20 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                            </div>
                                        </button>
                                        <button @click.stop="removePhoto(index)" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center shadow-lg hover:bg-red-600 transition-all z-10">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>

                            {{-- Add More Button --}}
                            <label class="shrink-0 w-16 h-16 rounded-lg border-2 border-dashed theme-border flex items-center justify-center cursor-pointer hover:bg-white/5 transition-colors">
                                <svg class="w-6 h-6 theme-text opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                <input type="file" multiple class="hidden" accept="image/*" @change="addMoreFiles">
                            </label>
                        </div>
                    </template>

                    <template x-if="localItems.length === 0 && existingMedia.length > 0">
                        <div class="flex gap-2">
                            <template x-for="(media, index) in existingMedia" :key="media.id">
                                <div class="shrink-0 relative">
                                    <button @click="currentIndex = index" 
                                            class="w-16 h-16 rounded-lg overflow-hidden border-2 transition-all"
                                            :class="currentIndex === index ? 'border-brand-500 scale-95' : 'border-transparent opacity-50'">
                                        <img :src="'/storage/' + media.file_path_original" class="w-full h-full object-cover">
                                    </button>
                                    <button @click.stop="removeExistingPhoto(index)" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center shadow-lg hover:bg-red-600 transition-all z-10">
                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
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
                            placeholder="{{ $is_secret ? 'write your secret note here...' : 'write something about this memory...' }}" 
                            class="flex-1 bg-transparent border-none focus:ring-0 text-sm theme-text p-0 resize-none min-h-[150px] lowercase leading-relaxed select-text"
                        ></textarea>
                    </div>

                    <div class="space-y-6 border-t theme-border pt-6">
                        {{-- Normal Post Fields --}}
                        <div x-show="!$wire.is_secret" class="space-y-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-white/5 border theme-border flex items-center justify-center opacity-40">
                                    <svg class="w-4 h-4 theme-text" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                                <input 
                                    wire:model="location" 
                                    placeholder="add location" 
                                    class="flex-1 bg-transparent border-none focus:ring-0 text-sm theme-text p-0 lowercase select-text"
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

                        {{-- Secret Note Fields --}}
                        <div x-show="$wire.is_secret" class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-indigo-500/5 border border-indigo-500/20 rounded-2xl group cursor-pointer" x-on:click="$wire.is_secret = !$wire.is_secret; if(!$wire.is_secret) step = 1">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-500/10 flex items-center justify-center text-indigo-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold theme-text lowercase">secret note</p>
                                        <p class="text-[10px] theme-text opacity-40 lowercase">lock this memory for your partner</p>
                                    </div>
                                </div>
                                <div class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model="is_secret" class="sr-only peer">
                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-500"></div>
                                </div>
                            </div>

                            <div class="p-5 bg-indigo-500/5 border border-indigo-500/20 rounded-3xl space-y-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-500/10 flex items-center justify-center text-indigo-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <p class="text-xs font-bold text-indigo-500 uppercase tracking-widest">unlock time</p>
                                </div>
                                
                                <input type="datetime-local" 
                                       wire:model="unlock_at"
                                       class="w-full bg-white/5 border theme-border rounded-xl px-4 py-3 text-sm theme-text focus:ring-indigo-500/20 focus:border-indigo-500/30 transition-all"
                                       required>
                                
                                <p class="text-[10px] theme-text opacity-40 leading-relaxed lowercase">this note will stay locked and blurred until the time you've set above.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
