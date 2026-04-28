<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-12">
    <div class="text-center space-y-4">
        <x-ui.heading level="1" size="4xl">Shared journal</x-ui.heading>
        <p class="text-gray-500 lowercase">pour your heart out. these words are for our eyes only.</p>
    </div>

    {{-- New Entry Form --}}
    <div class="bg-white border border-gray-100 rounded-[3rem] p-10 shadow-sm relative overflow-hidden group">
        <div class="absolute -right-20 -top-20 w-64 h-64 bg-brand-50 rounded-full blur-3xl opacity-30 group-hover:opacity-50 transition-opacity"></div>
        
        <form wire:submit.prevent="saveEntry" class="relative z-10 space-y-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-6">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">mood</p>
                    <div class="flex gap-2 p-1 bg-gray-50 rounded-2xl">
                        @foreach(['❤️', '🥰', '✨', '📸', '🌙', '🌊', '🍷', '🥂'] as $emoji)
                            <button type="button" wire:click="$set('mood', '{{ $emoji }}')" 
                                    class="text-xl p-2 rounded-xl transition-all hover:bg-white hover:shadow-sm {{ $mood === $emoji ? 'bg-white shadow-sm scale-110' : 'opacity-40 hover:opacity-100' }}">
                                {{ $emoji }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <input 
                    wire:model="title" 
                    type="text" 
                    placeholder="title of your entry (optional)" 
                    class="w-full border-none focus:ring-0 text-2xl font-semibold placeholder:text-gray-200 bg-transparent lowercase p-0"
                >
                <textarea 
                    wire:model="content" 
                    placeholder="write something beautiful..." 
                    class="w-full border-none focus:ring-0 text-xl placeholder:text-gray-300 resize-none bg-transparent min-h-[200px] lowercase leading-relaxed p-0"
                ></textarea>
                @error('content') <span class="text-xs text-red-400 lowercase block">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center justify-between pt-6 border-t border-gray-50">
                <p class="text-xs text-gray-400 lowercase italic">automatically saved to your private cloud.</p>
                <x-ui.button type="submit" class="px-12 py-4 text-base rounded-full shadow-lg shadow-brand-100">
                    save to journal
                </x-ui.button>
            </div>
        </form>
    </div>

    {{-- Search & List --}}
    <div class="space-y-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <x-ui.heading level="2" size="xl">Past entries</x-ui.heading>
            <div class="relative w-full md:w-72">
                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-gray-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input wire:model.live="search" placeholder="search thoughts..." class="w-full bg-white border border-gray-100 rounded-full pl-12 pr-4 py-3 text-sm focus:ring-brand-200 focus:border-brand-300 lowercase shadow-sm">
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8">
            @forelse($entries as $entry)
                <div class="bg-white border border-gray-100 rounded-[2.5rem] p-8 shadow-sm hover:shadow-md transition-all group relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1 h-full bg-brand-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    
                    <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
                        <div class="space-y-4 flex-1">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">{{ $entry->mood ?? '✨' }}</span>
                                <p class="text-xs text-gray-400 font-medium uppercase tracking-widest">{{ $entry->created_at->format('M d, Y') }} — {{ $entry->user->id === Auth::id() ? 'you' : $entry->user->name }}</p>
                            </div>
                            @if($entry->title)
                                <h3 class="text-xl font-bold text-gray-900 lowercase">{{ $entry->title }}</h3>
                            @endif
                            <p class="text-gray-700 leading-relaxed lowercase whitespace-pre-wrap">{{ $entry->content }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-20 text-center bg-gray-50/50 rounded-[3rem] border border-dashed border-gray-200">
                    <p class="text-gray-400 lowercase italic">no journal entries yet. start writing your shared story.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
