<div class="max-w-6xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-12">
    <div class="text-center space-y-4">
        <x-ui.heading level="1" size="4xl">Relationship insights</x-ui.heading>
        <p class="text-gray-500 lowercase">our journey by the numbers. every moment counted.</p>
    </div>

    {{-- Top Overview --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white border border-gray-100 rounded-[2.5rem] p-8 shadow-sm text-center">
            <p class="text-4xl font-bold text-gray-900 mb-1">{{ $daysTogether }}</p>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">days together</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-[2.5rem] p-8 shadow-sm text-center">
            <p class="text-4xl font-bold text-gray-900 mb-1">{{ $totalMemories }}</p>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">memories</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-[2.5rem] p-8 shadow-sm text-center">
            <p class="text-4xl font-bold text-gray-900 mb-1">{{ $totalPhotos }}</p>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">photos</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-[2.5rem] p-8 shadow-sm text-center">
            <p class="text-4xl font-bold text-gray-900 mb-1">{{ $typeStats['journal'] ?? 0 }}</p>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">journal entries</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        {{-- Activity Chart --}}
        <div class="bg-white border border-gray-100 rounded-[3rem] p-10 shadow-sm space-y-8">
            <x-ui.heading level="2" size="xl">Monthly momentum</x-ui.heading>
            <div class="h-48 flex items-end gap-4 px-4">
                @php $max = $monthlyTrend->max('count') ?: 1; @endphp
                @foreach($monthlyTrend as $trend)
                    <div class="flex-1 flex flex-col items-center gap-3 group">
                        <div class="w-full bg-brand-50 rounded-t-xl transition-all duration-500 group-hover:bg-brand-100 relative" style="height: {{ ($trend->count / $max) * 100 }}%">
                            <div class="absolute -top-8 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity text-xs font-bold text-brand-600">
                                {{ $trend->count }}
                            </div>
                        </div>
                        <span class="text-[10px] text-gray-300 font-bold uppercase tracking-tighter">{{ Carbon\Carbon::parse($trend->month)->format('M') }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Mood Distribution --}}
        <div class="bg-white border border-gray-100 rounded-[3rem] p-10 shadow-sm space-y-8">
            <x-ui.heading level="2" size="xl">Emotional landscape</x-ui.heading>
            <div class="space-y-6">
                @php $totalMoods = $moodStats->sum('count') ?: 1; @endphp
                @foreach($moodStats as $stat)
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-sm">
                            <div class="flex items-center gap-2">
                                <span class="text-xl">{{ $stat->mood }}</span>
                                <span class="text-gray-600 lowercase">{{ $stat->count }} memories</span>
                            </div>
                            <span class="text-xs text-gray-400 font-bold">{{ round(($stat->count / $totalMoods) * 100) }}%</span>
                        </div>
                        <div class="h-2 bg-gray-50 rounded-full overflow-hidden">
                            <div class="h-full bg-brand-200 rounded-full" style="width: {{ ($stat->count / $totalMoods) * 100 }}%"></div>
                        </div>
                    </div>
                @endforeach
                @if($moodStats->isEmpty())
                    <p class="text-center text-gray-400 italic lowercase py-8">no mood data captured yet.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Contributor Breakdown --}}
    <div class="bg-romantic-rose/20 border border-romantic-rose/30 rounded-[3rem] p-12 shadow-sm overflow-hidden relative">
        <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-white rounded-full blur-3xl opacity-50"></div>
        
        <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div class="space-y-4">
                <x-ui.heading level="2" size="2xl">Shared contribution</x-ui.heading>
                <p class="text-gray-500 lowercase leading-relaxed">a space built together, piece by piece, memory by memory.</p>
            </div>

            <div class="flex items-center justify-center gap-12 md:gap-20">
                @foreach($userActivity as $name => $stats)
                    <div class="text-center space-y-4">
                        <div class="w-24 h-24 bg-white rounded-full border-4 border-white shadow-xl mx-auto flex items-center justify-center overflow-hidden">
                             <img src="https://ui-avatars.com/api/?name={{ urlencode($name) }}&background=random" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900 lowercase">{{ $name }}</p>
                            <p class="text-xs text-gray-400 lowercase">{{ $stats['posts'] }} posts • {{ $stats['media'] }} photos</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
