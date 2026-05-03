<div class="max-w-6xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-12">
    <div class="text-center space-y-4">
        <x-ui.heading level="1" size="4xl">Memory calendar</x-ui.heading>
        <p class="text-gray-500 lowercase">your shared history, organized by time.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        {{-- Calendar Grid --}}
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white border border-gray-100 rounded-[3rem] p-8 shadow-sm">
                <div class="flex items-center justify-between mb-8">
                    <button wire:click="prevMonth" class="p-2 hover:bg-gray-50 rounded-full transition-colors">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    <h2 class="text-2xl font-bold lowercase text-gray-900">{{ $monthName }} {{ $year }}</h2>
                    <button wire:click="nextMonth" class="p-2 hover:bg-gray-50 rounded-full transition-colors">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>

                <div class="grid grid-cols-7 gap-2">
                    @foreach(['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'] as $day)
                        <div class="text-center text-[10px] font-bold text-gray-300 uppercase tracking-widest mb-4">{{ $day }}</div>
                    @endforeach

                    @foreach($calendar as $item)
                        <div class="aspect-square relative">
                            @if($item)
                                <button 
                                    wire:click="selectDate('{{ $item['date'] }}')"
                                    class="w-full h-full flex items-center justify-center rounded-2xl transition-all relative group
                                    {{ $selectedDate === $item['date'] ? 'bg-brand-500 text-white shadow-lg shadow-brand-100' : 'hover:bg-gray-50 text-gray-700' }}
                                    {{ $item['is_today'] && $selectedDate !== $item['date'] ? 'border-2 border-brand-100' : '' }}"
                                >
                                    <span class="text-sm font-medium">{{ $item['day'] }}</span>
                                    
                                    {{-- Indicators --}}
                                    <div class="absolute bottom-2 left-1/2 -translate-x-1/2 flex gap-1">
                                        @if($item['has_memories'])
                                            <div class="w-1 h-1 rounded-full {{ $selectedDate === $item['date'] ? 'bg-white/60' : 'bg-brand-400' }}"></div>
                                        @endif
                                        @if($item['has_milestones'])
                                            <div class="w-1 h-1 rounded-full {{ $selectedDate === $item['date'] ? 'bg-white/60' : 'bg-amber-400' }}"></div>
                                        @endif
                                    </div>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Day Details --}}
        <div class="space-y-8">
            <div class="bg-romantic-sand/30 border border-romantic-sand/50 rounded-[2.5rem] p-8 shadow-sm">
                <div class="space-y-6">
                    <div class="pb-6 border-b border-romantic-sand/50">
                        <p class="text-[10px] font-bold text-romantic-slate/50 uppercase tracking-widest mb-1">selected date</p>
                        <h3 class="text-2xl font-bold text-romantic-slate lowercase">{{ $selectedDateFormatted }}</h3>
                    </div>

                    @if($selectedMemories->isEmpty() && $selectedMilestones->isEmpty())
                        <div class="py-12 text-center space-y-4">
                            <div class="w-16 h-16 bg-white/50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-romantic-slate/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <p class="text-sm text-romantic-slate/50 lowercase italic">no shared memories on this day.</p>
                        </div>
                    @endif

                    @if($selectedMilestones->isNotEmpty())
                        <div class="space-y-4">
                            <p class="text-[10px] font-bold text-amber-600 uppercase tracking-widest">milestones</p>
                            @foreach($selectedMilestones as $milestone)
                                <div class="bg-white/60 rounded-2xl p-4 border border-white shadow-sm">
                                    <p class="text-sm font-bold text-gray-900 lowercase">{{ $milestone->title }}</p>
                                    <p class="text-xs text-gray-500 lowercase">{{ $milestone->description }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($selectedMemories->isNotEmpty())
                        <div class="space-y-4">
                            <p class="text-[10px] font-bold text-brand-600 uppercase tracking-widest">memories</p>
                            @foreach($selectedMemories as $memory)
                                <a href="{{ route('gallery.preview', $memory->media->first()->id) }}" wire:navigate class="block bg-white/60 rounded-2xl p-4 border border-white shadow-sm hover:bg-white transition-colors group">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <span class="text-lg leading-none">{{ $memory->mood ?? '✨' }}</span>
                                            <span class="text-[10px] text-gray-400 font-bold uppercase">{{ $memory->type }}</span>
                                        </div>
                                        <svg class="w-4 h-4 text-gray-300 group-hover:text-brand-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </div>
                                    @if($memory->title)
                                        <p class="text-sm font-bold text-gray-900 lowercase mb-1">{{ $memory->title }}</p>
                                    @endif
                                    <p class="text-xs text-gray-600 lowercase line-clamp-2">{{ $memory->content }}</p>
                                    
                                    @if($memory->media->isNotEmpty())
                                        <div class="mt-3 rounded-xl overflow-hidden aspect-video bg-gray-100">
                                            <img src="{{ Storage::disk('public')->url($memory->media->first()->file_path_thumbnail ?? $memory->media->first()->file_path_original) }}" class="w-full h-full object-cover">
                                        </div>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- "On This Day" Highlight --}}
            @php
                $onThisDay = \App\Models\Post::where('relationship_id', Auth::user()->relationship->id)
                    ->whereDay('published_at', Carbon::parse($selectedDate)->day)
                    ->whereMonth('published_at', Carbon::parse($selectedDate)->month)
                    ->whereYear('published_at', '<', Carbon::parse($selectedDate)->year)
                    ->first();
            @endphp
            @if($onThisDay)
                <div class="bg-brand-50 border border-brand-100 rounded-[2rem] p-8 shadow-sm">
                    <p class="text-[10px] font-bold text-brand-600 uppercase tracking-widest mb-4">on this day, {{ Carbon::parse($selectedDate)->year - $onThisDay->published_at->year }} year(s) ago</p>
                    <div class="space-y-4">
                        <p class="text-sm italic text-brand-900 leading-relaxed">"{{ Str::limit($onThisDay->content, 100) }}"</p>
                        <a href="{{ route('gallery.preview', $onThisDay->media->first()->id) }}" wire:navigate class="text-xs text-brand-600 font-bold hover:underline lowercase">relive this memory</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
