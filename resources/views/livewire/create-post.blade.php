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
                captured_at: null,
                lat: null,
                lon: null,
                crop: null
            }));

            this.isExtracting = true;
            
            const processedItems = await Promise.all(newItems.map(async (item, i) => {
                const data = await window.extractExifData(item.file);
                if (data) {
                    item.captured_at = data.captured_at;
                    item.lat = data.lat;
                    item.lon = data.lon;
                    
                    if (i === 0 && data.lat && data.lon && !item.location) {
                        try {
                            const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${data.lat}&lon=${data.lon}&zoom=18`, {
                                headers: { 'User-Agent': 'JustTwo-App' }
                            });
                            const geo = await res.json();
                            if (geo.address) {
                                const addr = geo.address;
                                const city = addr.city || addr.town || addr.village || addr.suburb;
                                const road = addr.road || addr.neighbourhood;
                                item.location = [road, city].filter(Boolean).join(', ').toLowerCase();
                            }
                        } catch (e) { console.error('Geocode error', e); }
                    }
                }
                return item;
            }));

            this.localItems = processedItems;
            
            if (this.localItems.length > 0 && this.localItems[0].location && !this.$wire.location) {
                this.$wire.location = this.localItems[0].location;
            }

            this.isExtracting = false;
            this.$nextTick(() => this.initCropper());
        },

        async addMoreFiles(event) {
            const files = Array.from(event.target.files);
            if (!files.length) return;

            this.isExtracting = true;
            const promises = files.map(async (file) => {
                const data = await window.extractExifData(file);
                this.localItems.push({
                    id: Date.now() + Math.random(),
                    file: file,
                    url: URL.createObjectURL(file),
                    location: '',
                    captured_at: data?.captured_at || null,
                    lat: data?.lat || null,
                    lon: data?.lon || null,
                    crop: null
                });
            });
            await Promise.all(promises);
            this.isExtracting = false;
        },

        initCropper() {
            if (this.cropper) this.cropper.destroy();
            const image = document.getElementById('cropper-image');
            const item = this.localItems[this.currentIndex];
            if (!image || !item) return;

            image.src = item.url;
            this.cropper = new Cropper(image, {
                aspectRatio: 4 / 5,
                viewMode: 3, 
                dragMode: 'move',
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
        },

        async selectPhoto(index) {
            if (this.localItems.length === 0) return;
            if (this.cropper) {
                const canvas = this.cropper.getCroppedCanvas({ width: 1440, height: 1800 });
                this.localItems[this.currentIndex].crop = canvas.toDataURL('image/jpeg', 0.9);
            }
            this.currentIndex = index;
            this.initCropper();
        },

        async submitPost() {
            this.isUploading = true;
            try {
                if (this.localItems.length > 0 && this.cropper) {
                    const canvas = this.cropper.getCroppedCanvas({ width: 1440, height: 1800 });
                    this.localItems[this.currentIndex].crop = canvas.toDataURL('image/jpeg', 0.9);
                }
                
                const processImage = (fileOrUrl) => new Promise((resolve, reject) => {
                    const img = new Image();
                    const timeout = setTimeout(() => {
                        console.error('Image processing timed out');
                        resolve(null);
                    }, 10000);

                    img.onload = () => {
                        clearTimeout(timeout);
                        try {
                            const canvas = document.createElement('canvas');
                            const ctx = canvas.getContext('2d');
                            const maxDim = 2000;
                            let w = img.width;
                            let h = img.height;
                            if (w > h) {
                                if (w > maxDim) { h *= maxDim / w; w = maxDim; }
                            } else {
                                if (h > maxDim) { w *= maxDim / h; h = maxDim; }
                            }
                            canvas.width = w;
                            canvas.height = h;
                            ctx.drawImage(img, 0, 0, w, h);
                            resolve(canvas.toDataURL('image/jpeg', 0.9));
                        } catch (e) {
                            console.error('Canvas error', e);
                            resolve(null);
                        }
                    };
                    img.onerror = () => {
                        clearTimeout(timeout);
                        console.error('Image load error');
                        resolve(null);
                    };
                    img.src = typeof fileOrUrl === 'string' ? fileOrUrl : URL.createObjectURL(fileOrUrl);
                });

                for (let item of this.localItems) {
                    await new Promise(r => setTimeout(r, 50));
                    const processed = await processImage(item.crop || item.file);
                    if (processed) item.crop = processed;
                }
                
                const results = this.localItems.filter(item => item.crop).map(item => item.crop);
                const locations = this.localItems.map(item => item.location || null);
                const capturedDates = this.localItems.map(item => item.captured_at || null);
                const lats = this.localItems.map(item => item.lat || null);
                const lons = this.localItems.map(item => item.lon || null);
                const keepIds = this.existingMedia.map(m => m.id);

                if (results.length === 0 && keepIds.length === 0 && !this.isEdit) {
                    this.isUploading = false;
                    alert('please select at least one photo');
                    return;
                }

                try {
                    await @this.savePost(results, keepIds, locations, capturedDates, lats, lons);
                } catch (e) {
                    this.isUploading = false;
                    if (e.status !== 422) {
                        console.error(e);
                        alert('failed to post memory.');
                    }
                }
            } catch (e) {
                this.isUploading = false;
                console.error(e);
            }
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
        window.extractExifData = async (file) => {
            return new Promise((resolve) => {
                if (!file.type.startsWith('image/')) {
                    resolve({ location: null, captured_at: null, lat: null, lon: null });
                    return;
                }
                
                EXIF.getData(file, function() {
                    const lat = EXIF.getTag(this, "GPSLatitude");
                    const lon = EXIF.getTag(this, "GPSLongitude");
                    const latRef = EXIF.getTag(this, "GPSLatitudeRef") || "N";
                    const lonRef = EXIF.getTag(this, "GPSLongitudeRef") || "E";
                    const dateTime = EXIF.getTag(this, "DateTimeOriginal") || EXIF.getTag(this, "DateTime");

                    let result = { location: null, captured_at: null, lat: null, lon: null };

                    if (dateTime) {
                        const parts = dateTime.split(/[: ]/);
                        if (parts.length >= 6) {
                            result.captured_at = `${parts[0]}-${parts[1]}-${parts[2]} ${parts[3]}:${parts[4]}:${parts[5]}`;
                        }
                    }

                    if (!lat || !lon) {
                        resolve(result);
                        return;
                    }

                    const toDecimal = (number, ref) => {
                        let dec = number[0] + number[1] / 60 + number[2] / 3600;
                        if (ref === "S" || ref === "W") dec = dec * -1;
                        return dec;
                    };

                    result.lat = toDecimal(lat, latRef);
                    result.lon = toDecimal(lon, lonRef);
                    resolve(result);
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

    <header class="flex items-center justify-between px-6 h-14 border-b theme-border sticky top-0 theme-bg z-30">
        <div class="flex items-center">
            <button x-show="step == 2" @click="step = 1; localFiles = []" class="text-xs font-bold theme-text lowercase">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <a x-show="step == 1" href="{{ route('dashboard') }}" wire:navigate class="text-xs font-bold theme-text lowercase">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
        </div>
        <h2 class="text-sm font-bold theme-text lowercase" x-text="step == 2 ? (isEdit ? 'edit post' : 'new post') : 'share'"></h2>
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

    <div class="max-w-4xl mx-auto px-1.5 sm:px-4">
        <div x-show="step == 1" class="flex flex-col items-center justify-center py-8 px-6 text-center space-y-12">
            <div class="space-y-2">
                <h3 class="text-2xl font-bold theme-text lowercase">share a moment</h3>
                <p class="text-xs opacity-40 theme-text lowercase">capture memories, plan future, or start saving.</p>
            </div>

            <div class="w-full max-w-xs space-y-4">
                <label class="group relative flex items-center gap-4 p-5 bg-white/5 border theme-border rounded-3xl cursor-pointer hover:bg-brand-500/5 hover:border-brand-500/30 transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-brand-500/10 flex items-center justify-center text-brand-500 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-bold theme-text lowercase">from gallery</p>
                        <p class="text-[10px] theme-text opacity-40 lowercase">select your best shots.</p>
                    </div>
                    <input type="file" multiple class="hidden" accept="image/*" @change="handleFiles">
                </label>

                <a href="{{ route('planner.create') }}" wire:navigate
                        class="w-full group relative flex items-center gap-4 p-5 bg-white/5 border theme-border rounded-3xl cursor-pointer hover:bg-emerald-500/5 hover:border-emerald-500/30 transition-all text-left">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-bold theme-text lowercase">plan something</p>
                        <p class="text-[10px] theme-text opacity-40 lowercase">trip or milestone.</p>
                    </div>
                </a>

                <a href="{{ route('savings.create') }}" wire:navigate
                        class="w-full group relative flex items-center gap-4 p-5 bg-white/5 border theme-border rounded-3xl cursor-pointer hover:bg-amber-500/5 hover:border-amber-500/30 transition-all text-left">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/10 flex items-center justify-center text-amber-500 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.407 2.67 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.407-2.67-1M12 16V7"></path></svg>
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-bold theme-text lowercase">start saving goal</p>
                        <p class="text-[10px] theme-text opacity-40 lowercase">for your future dreams.</p>
                    </div>
                </a>
            </div>
        </div>

        <div x-show="step == 2" x-cloak class="flex flex-col">
            <div class="w-full aspect-[4/5] bg-black relative overflow-hidden">
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
                
                <div x-show="localItems[currentIndex]?.location" x-cloak
                     class="absolute bottom-4 left-4 z-20 flex items-center gap-1.5 px-3 py-1.5 bg-black/40 backdrop-blur-md rounded-full border border-white/10">
                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span class="text-[10px] text-white font-bold lowercase tracking-wide" x-text="localItems[currentIndex]?.location"></span>
                </div>

                <div x-show="isUploading || isExtracting" class="absolute inset-0 z-50 bg-black/80 flex flex-col items-center justify-center space-y-4">
                    <div class="w-10 h-10 border-2 border-brand-500/30 border-t-brand-500 rounded-full animate-spin"></div>
                    <p class="text-[10px] text-white font-bold uppercase tracking-widest" x-text="isExtracting ? 'detecting locations...' : 'posting...'"></p>
                </div>
            </div>

            <div class="p-4 flex gap-2 overflow-x-auto scrollbar-hide bg-black/5 border-b theme-border">
                <template x-if="localItems.length > 0">
                    <div class="flex gap-2">
                        <div class="flex gap-2" x-init="new Sortable($el, { animation: 150, draggable: '.draggable-item', onEnd: (evt) => reorder(evt.oldIndex, evt.newIndex) })">
                            <template x-for="(item, index) in localItems" :key="item.id">
                                <div class="draggable-item shrink-0 relative">
                                    <button @click="selectPhoto(index)" 
                                            class="w-16 h-16 rounded-lg overflow-hidden border-2 transition-all"
                                            :class="currentIndex === index ? 'border-brand-500 scale-95' : 'border-transparent opacity-50'">
                                        <img :src="item.url" class="w-full h-full object-cover">
                                    </button>
                                    <button @click.stop="removePhoto(index)" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center shadow-lg">
                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <label class="shrink-0 w-16 h-16 rounded-lg border-2 border-dashed theme-border flex items-center justify-center cursor-pointer">
                            <svg class="w-6 h-6 theme-text opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            <input type="file" multiple class="hidden" accept="image/*" @change="addMoreFiles">
                        </label>
                    </div>
                </template>
            </div>

            <div class="p-6 space-y-6 pb-32">
                <div class="flex items-center justify-between p-4 bg-white/5 border theme-border rounded-2xl cursor-pointer" x-on:click="$wire.is_public = !$wire.is_public">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-brand-500/10 flex items-center justify-center text-brand-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold theme-text lowercase">public memory</p>
                            <p class="text-[10px] theme-text opacity-40 lowercase">show on welcome page</p>
                        </div>
                    </div>
                    <input type="checkbox" wire:model="is_public" class="sr-only">
                    <div class="w-9 h-5 rounded-full transition-all" :class="$wire.is_public ? 'bg-brand-500' : 'bg-gray-200'">
                        <div class="w-4 h-4 bg-white rounded-full mt-0.5 ml-0.5 transition-all" :style="$wire.is_public ? 'transform: translateX(100%)' : ''"></div>
                    </div>
                </div>

                <div class="flex items-center gap-3 px-1">
                    <div class="w-8 h-8 rounded-full bg-white/5 border theme-border flex items-center justify-center opacity-40">
                        <svg class="w-4 h-4 theme-text" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <input wire:model="location" placeholder="add location" class="flex-1 bg-transparent border-none focus:ring-0 text-sm theme-text p-0 lowercase">
                </div>
            </div>
        </div>
    </div>
</div>
