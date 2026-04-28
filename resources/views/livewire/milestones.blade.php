<div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-16">
    <div class="text-center space-y-4">
        <x-ui.heading level="1" size="4xl">Our milestones</x-ui.heading>
        <p class="text-gray-500 lowercase">counting every step of our journey, from the first meet to forever.</p>
    </div>

    {{-- Add Milestone Form --}}
    <div x-data="{ open: false }">
        <div class="flex justify-center mb-8">
            <button @click="open = !open" class="group flex items-center gap-3 bg-white border border-gray-100 rounded-full pl-6 pr-8 py-3 shadow-sm hover:shadow-md transition-all">
                <div class="w-10 h-10 bg-brand-50 text-brand-500 rounded-full flex items-center justify-center transition-transform group-hover:rotate-90">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </div>
                <span class="text-sm font-semibold text-gray-700 lowercase">add a new milestone</span>
            </button>
        </div>

        <div x-show="open" x-transition class="bg-white border border-gray-100 rounded-[3rem] p-10 shadow-sm mb-12">
            <form wire:submit.prevent="saveMilestone" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-6 mb-2">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">category</p>
                        <div class="flex gap-4">
                            @foreach($categories as $key => $icon)
                                <button type="button" wire:click="$set('category', '{{ $key }}')" 
                                        class="text-2xl transition-transform hover:scale-125 {{ $category === $key ? 'scale-125' : 'grayscale opacity-30' }}">
                                    {{ $icon }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest px-2">what happened?</label>
                    <x-ui.input wire:model="title" placeholder="first date, first trip, etc." class="bg-gray-50/50 border-transparent focus:bg-white text-lg py-4 px-6 rounded-2xl" />
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest px-2">when was it?</label>
                    <x-ui.input type="date" wire:model="event_date" class="bg-gray-50/50 border-transparent focus:bg-white text-lg py-4 px-6 rounded-2xl" />
                </div>

                <div class="md:col-span-2 space-y-2">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest px-2">tell the story</label>
                    <textarea 
                        wire:model="description" 
                        placeholder="a little note about this memory..." 
                        class="w-full bg-gray-50/50 border-transparent focus:bg-white focus:ring-brand-200 rounded-[2rem] p-6 text-lg lowercase resize-none min-h-[120px]"
                    ></textarea>
                </div>

                <div class="md:col-span-2 flex justify-end gap-4 pt-4">
                    <x-ui.button type="button" @click="open = false" variant="outline" class="px-8 py-3 rounded-full">cancel</x-ui.button>
                    <x-ui.button type="submit" class="px-12 py-3 rounded-full shadow-lg shadow-brand-100">save milestone</x-ui.button>
                </div>
            </form>
        </div>
    </div>

    {{-- Timeline --}}
    <div class="relative">
        <div class="absolute left-1/2 -translate-x-px h-full w-0.5 bg-gradient-to-b from-brand-100 via-gray-100 to-transparent"></div>
        
        <div class="space-y-20 relative">
            @forelse($milestones as $milestone)
                <div class="flex items-center gap-12 {{ $loop->index % 2 == 0 ? 'flex-row' : 'flex-row-reverse' }}">
                    <div class="flex-1 {{ $loop->index % 2 == 0 ? 'text-right' : 'text-left' }}">
                        <div class="group inline-block w-full max-w-md">
                            <div class="bg-white border border-gray-100 rounded-[2.5rem] p-8 shadow-sm transition-all duration-500 group-hover:shadow-xl group-hover:-translate-y-1 relative overflow-hidden">
                                <div class="absolute top-0 {{ $loop->index % 2 == 0 ? 'right-0' : 'left-0' }} w-1 h-full bg-brand-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                
                                <div class="flex items-center gap-3 mb-4 {{ $loop->index % 2 == 0 ? 'justify-end' : 'justify-start' }}">
                                    <span class="text-xs font-bold text-brand-500 uppercase tracking-widest">{{ $milestone->event_date->format('M d, Y') }}</span>
                                    <span class="text-xl">{{ $categories[$milestone->category] ?? '✨' }}</span>
                                </div>

                                <h3 class="text-2xl font-bold text-gray-900 lowercase mb-3">{{ $milestone->title }}</h3>
                                
                                @if($milestone->description)
                                    <p class="text-gray-600 lowercase leading-relaxed line-clamp-3">{{ $milestone->description }}</p>
                                @endif

                                <div class="mt-6 flex items-center gap-4 {{ $loop->index % 2 == 0 ? 'justify-end' : 'justify-start' }}">
                                    <div class="px-4 py-2 bg-gray-50 rounded-full">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">
                                            @php
                                                $days = abs(now()->startOfDay()->diffInDays($milestone->event_date->startOfDay()));
                                                $isPast = now() > $milestone->event_date;
                                            @endphp
                                            {{ $days }} days {{ $isPast ? 'ago' : 'to go' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="relative z-10 flex items-center justify-center">
                        <div class="w-14 h-14 rounded-full bg-white border-4 border-brand-50 shadow-lg flex items-center justify-center transition-transform duration-500 group-hover:scale-110">
                            <div class="w-4 h-4 rounded-full bg-brand-500 animate-pulse"></div>
                        </div>
                    </div>

                    <div class="flex-1 hidden md:block"></div>
                </div>
            @empty
                <div class="py-20 text-center bg-gray-50/50 rounded-[3rem] border border-dashed border-gray-200">
                    <p class="text-gray-400 lowercase italic">no milestones recorded yet. start your journey together.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
