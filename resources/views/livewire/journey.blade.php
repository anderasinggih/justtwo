<div class="max-w-xl mx-auto px-1.5 py-12">
    {{-- Journey Header --}}
    <div class="text-center mb-16">
        <h1 class="text-3xl font-bold tracking-tighter lowercase theme-text">Our Journey</h1>
        <p class="text-xs opacity-40 theme-text lowercase mt-2 italic">counting every step, every laugh, every mile.</p>
    </div>

    {{-- Vertical Timeline Line --}}
    <div class="relative">
        {{-- The Line --}}
        <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gradient-to-b from-brand-500/20 via-brand-500/10 to-transparent rounded-full"></div>

        {{-- Timeline Items --}}
        <div class="space-y-12">
            @foreach($journey as $item)
                <div class="relative pl-12 group">
                    {{-- Timeline Dot --}}
                    <div class="absolute left-0 top-2 w-8 h-8 rounded-full border-4 flex items-center justify-center z-10 transition-transform group-hover:scale-125"
                         style="background-color: {{ $item['type'] === 'milestone' ? 'var(--accent-color)' : 'transparent' }}; border-color: var(--bg-primary); color: {{ $item['type'] === 'milestone' ? 'white' : 'var(--accent-color)' }};">
                        
                        @if($item['type'] !== 'milestone')
                            <div class="absolute inset-0 rounded-full bg-white/5 border theme-border"></div>
                        @endif

                        <div class="relative z-10">
                            @if($item['icon'] === 'star')
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                            @else
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                            @endif
                        </div>
                    </div>

                    {{-- Date Bubble --}}
                    <div class="mb-2">
                        <span class="text-[10px] font-bold uppercase tracking-widest opacity-40 theme-text bg-white/5 px-2 py-1 rounded-md border theme-border">
                            {{ $item['date']->format('M d, Y') }}
                        </span>
                    </div>

                    {{-- Content Card --}}
                    <div class="theme-card rounded-2xl border theme-border p-5 shadow-sm transition-all hover:shadow-md hover:border-brand-500/30 relative overflow-hidden">
                        @if($item['type'] === 'milestone')
                            <div class="absolute top-0 right-0 w-24 h-24 theme-accent-bg opacity-5 rounded-full -mr-12 -mt-12 blur-2xl"></div>
                        @endif

                        <h3 class="text-sm font-bold lowercase tracking-tight mb-2 theme-text break-words">{{ $item['title'] }}</h3>
                        
                        @if(isset($item['image']))
                            <div class="aspect-[16/9] w-full rounded-xl overflow-hidden mb-4 bg-white/5 border theme-border">
                                <img src="{{ Storage::url($item['image']) }}" class="w-full h-full object-cover">
                            </div>
                        @endif

                        <p class="text-xs opacity-70 theme-text leading-relaxed lowercase break-words">
                            {{ Str::limit($item['content'], 120) }}
                        </p>

                        @if($item['type'] === 'post')
                            <div class="mt-4 flex justify-end">
                                <a href="{{ route('timeline') }}" wire:navigate class="text-[10px] font-bold theme-accent lowercase hover:underline">view full memory →</a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- End of Journey --}}
    <div class="mt-16 text-center py-12 border-t border-dashed theme-border">
        <div class="w-12 h-12 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4 border theme-border">
            <svg class="w-6 h-6 theme-accent" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
        </div>
        <p class="text-[10px] opacity-40 theme-text lowercase tracking-widest font-bold">and many more chapters to come...</p>
    </div>
</div>
