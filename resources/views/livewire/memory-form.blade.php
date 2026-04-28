<div class="bg-white border border-gray-100 rounded-[2rem] p-6 shadow-sm"
     x-data="{ 
        cropping: false,
        currentFile: null,
        cropper: null,
        
        init() {
            this.$watch('cropping', value => {
                if (!value && this.cropper) {
                    this.cropper.destroy();
                    this.cropper = null;
                }
            });
        },

        handleFiles(event) {
            const file = event.target.files[0];
            if (!file) return;
            
            this.currentFile = file;
            const reader = new FileReader();
            reader.onload = (e) => {
                this.cropping = true;
                this.$nextTick(() => {
                    const image = this.$refs.cropImage;
                    image.src = e.target.result;
                    this.cropper = new Cropper(image, {
                        aspectRatio: 4/5,
                        viewMode: 2,
                        dragMode: 'move',
                        guides: true,
                        center: true,
                        highlight: false,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        toggleDragModeOnDblclick: false,
                    });
                });
            };
            reader.readAsDataURL(file);
        },

        saveCrop() {
            const canvas = this.cropper.getCroppedCanvas({
                width: 1080,
                height: 1350,
            });
            
            canvas.toBlob((blob) => {
                @this.upload('photos', blob, 
                    (uploadedFilename) => {
                        this.cropping = false;
                    }, 
                    () => {
                        // Error
                    }, 
                    (event) => {
                        // Progress
                    }
                );
            }, 'image/jpeg', 0.9);
        }
     }">
    
    {{-- Cropper Modal --}}
    <div x-show="cropping" 
         x-cloak
         class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 backdrop-blur-md p-4"
         x-transition>
        <div class="bg-white rounded-[2.5rem] overflow-hidden max-w-lg w-full shadow-2xl">
            <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                <x-ui.heading level="3" size="lg">Crop your memory</x-ui.heading>
                <button @click="cropping = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="bg-gray-100 aspect-[4/5] relative overflow-hidden">
                <img x-ref="cropImage" class="max-w-full">
            </div>

            <div class="p-6 flex gap-4">
                <x-ui.button @click="cropping = false" variant="ghost" class="flex-1">cancel</x-ui.button>
                <x-ui.button @click="saveCrop()" variant="primary" class="flex-1">done</x-ui.button>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6">
        <div class="flex items-start gap-4">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&size=40&background=random" class="w-10 h-10 rounded-full shadow-sm">
            <div class="flex-1 space-y-4">
                <input 
                    wire:model="title" 
                    type="text" 
                    placeholder="add a title (optional)" 
                    class="w-full border-none focus:ring-0 text-xl font-semibold placeholder:text-gray-200 bg-transparent lowercase p-0"
                >
                <textarea 
                    wire:model="content" 
                    placeholder="what's on your mind today?" 
                    class="w-full border-none focus:ring-0 text-lg placeholder:text-gray-300 resize-none bg-transparent lowercase min-h-[100px] p-0"
                ></textarea>
                @error('content') <span class="text-xs text-red-400 lowercase block">{{ $message }}</span> @enderror
            </div>
        </div>

        @if($photos)
            <div class="grid grid-cols-4 md:grid-cols-6 gap-3 px-14">
                @foreach($photos as $index => $photo)
                    @if($photo)
                        <div class="relative group aspect-[4/5]">
                            <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover rounded-xl border border-gray-50 shadow-sm">
                            <button type="button" wire:click="$set('photos.{{ $index }}', null)" class="absolute -top-2 -right-2 bg-white rounded-full p-1 shadow-md opacity-0 group-hover:opacity-100 transition-all hover:bg-brand-50">
                                <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        <div class="flex items-center justify-between pt-4 border-t border-gray-50 px-2">
            <div class="flex items-center gap-1">
                <label class="cursor-pointer p-2 text-gray-400 hover:text-brand-500 hover:bg-brand-50 rounded-full transition-all group relative">
                    <input type="file" @change="handleFiles" class="hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">add photo</span>
                </label>
                
                <div class="h-6 w-px bg-gray-100 mx-2"></div>

                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" type="button" class="p-2 text-gray-400 hover:text-brand-500 hover:bg-brand-50 rounded-full transition-all group relative">
                        @if($mood)
                            <span class="text-lg leading-none">{{ $mood }}</span>
                        @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        @endif
                        <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">set mood</span>
                    </button>
                    
                    <div x-show="open" @click.away="open = false" class="absolute bottom-full mb-2 left-0 bg-white border border-gray-100 rounded-2xl shadow-xl p-2 flex gap-1 z-50">
                        @foreach(['❤️', '🥰', '✨', '📸', '🌙', '🌊', '🍷', '🥂'] as $emoji)
                            <button @click="$wire.set('mood', '{{ $emoji }}'); open = false" type="button" class="p-2 hover:bg-brand-50 rounded-xl transition-colors text-lg leading-none">
                                {{ $emoji }}
                            </button>
                        @endforeach
                        <button @click="$wire.set('mood', ''); open = false" type="button" class="p-2 hover:bg-gray-50 rounded-xl transition-colors">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>

                <div class="h-6 w-px bg-gray-100 mx-2"></div>


                <select wire:model="type" class="border-none bg-transparent text-xs text-gray-400 focus:ring-0 lowercase cursor-pointer hover:text-gray-600 font-medium">
                    <option value="photo">photo</option>
                    <option value="gallery">gallery</option>
                    <option value="story">story</option>
                    <option value="travel">travel</option>
                    <option value="note">note</option>
                </select>
                <div class="h-6 w-px bg-gray-100 mx-2"></div>

                <label class="flex items-center gap-2 cursor-pointer group">
                    <div class="relative">
                        <input type="checkbox" wire:model="is_public" class="sr-only">
                        <div class="w-8 h-4 bg-gray-100 rounded-full transition-colors group-hover:bg-gray-200" :class="{ 'bg-brand-500': @entangle('is_public') }"></div>
                        <div class="absolute left-1 top-1 w-2 h-2 bg-white rounded-full transition-transform" :class="{ 'translate-x-4': @entangle('is_public') }"></div>
                    </div>
                    <span class="text-[10px] font-bold theme-text opacity-40 lowercase tracking-tight">share to public</span>
                </label>
            </div>

            <div class="flex items-center gap-4">
                <div wire:loading wire:target="photos" class="flex items-center gap-2">
                    <div class="animate-spin rounded-full h-3 w-3 border-b-2 border-brand-500"></div>
                    <span class="text-[10px] text-gray-400 lowercase">uploading...</span>
                </div>

                <x-ui.button type="submit" size="sm" class="px-8 py-2.5 rounded-full shadow-lg shadow-brand-100" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">share memory</span>
                    <span wire:loading wire:target="save">sharing...</span>
                </x-ui.button>
            </div>
        </div>
    </form>
</div>


