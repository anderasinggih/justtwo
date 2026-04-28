<div x-data="{ 
    open(src) {
        $dispatch('open-lightbox', src)
    }
}" class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-12">
    <div class="text-center">
        <x-ui.heading level="1" size="4xl">Your gallery</x-ui.heading>
        <p class="text-gray-500 lowercase">every captured smile, archived forever.</p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse($media as $item)
            @if($item->file_path_original)
                <div class="relative group cursor-pointer aspect-[4/5] rounded-2xl overflow-hidden bg-gray-100 shadow-sm" 
                     @click="open('{{ Storage::disk('public')->url($item->file_path_original) }}')">
                    <img src="{{ Storage::disk('public')->url($item->file_path_thumbnail ?? $item->file_path_original) }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/10 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <svg class="w-8 h-8 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                        </svg>
                    </div>
                </div>
            @else
                <div class="relative aspect-[4/5] rounded-2xl overflow-hidden bg-gray-50 border border-gray-100 flex items-center justify-center">

                    <div class="text-center space-y-2">
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-brand-500 mx-auto"></div>
                        <p class="text-[10px] text-gray-400 lowercase">optimizing...</p>
                    </div>
                </div>
            @endif


        @empty
            <div class="col-span-full py-20 text-center">
                <p class="text-gray-400 lowercase italic">no photos found. start sharing some memories!</p>
            </div>
        @endforelse
    </div>
</div>
