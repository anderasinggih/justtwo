<?php

use App\Models\Post;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public function with(): array
    {
        return [
            'posts' => Post::where('is_public', true)
                ->where('is_archived', false)
                ->with(['user', 'media'])
                ->latest()
                ->paginate(12),
        ];
    }
}; ?>

<div class="space-y-12">
    {{-- Instagram-style Grid --}}
    {{-- Premium Dynamic Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-0 md:gap-4 lg:gap-6 auto-rows-[160px] md:auto-rows-[250px]">
        @forelse($posts as $index => $post)
            @php
                $pattern = $index % 8;
                $classes = 'col-span-1 row-span-1'; // Default
                if ($pattern === 0) $classes = 'col-span-2 row-span-1'; // Wide
                if ($pattern === 5) $classes = 'col-span-1 row-span-2'; // Tall
            @endphp
            <a href="{{ route('posts.preview', $post) }}" wire:navigate 
               class="relative group cursor-pointer overflow-hidden rounded-none md:rounded-2xl transition-all duration-500 hover:shadow-[0_10px_30px_rgba(0,0,0,0.1)] {{ $classes }}">
                
                {{-- Media with Zoom Effect --}}
                <div class="w-full h-full bg-gray-50 overflow-hidden">
                    @if($post->media->isNotEmpty())
                        <img src="{{ Storage::disk('public')->url($post->media->first()->file_path_original) }}" 
                             class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
                        
                        @if($post->media->count() > 1)
                            <div class="absolute top-4 right-4 z-10 bg-white/20 backdrop-blur-md px-2 py-1 rounded-full text-[8px] md:text-[10px] font-bold text-white border border-white/20">
                                {{ $post->media->count() }} photos
                            </div>
                        @endif
                    @else
                        <div class="w-full h-full flex items-center justify-center p-6 bg-gradient-to-br from-brand-50 to-white">
                            <p class="text-[10px] md:text-xs theme-text opacity-40 line-clamp-4 italic text-center leading-relaxed">{{ $post->content }}</p>
                        </div>
                    @endif
                </div>

                {{-- Premium Hover Overlay --}}
                <div class="absolute inset-0 bg-black/40 md:bg-black/20 opacity-0 group-hover:opacity-100 transition-all duration-500 backdrop-blur-[2px] flex flex-col justify-end p-4 md:p-8">
                    <div class="transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500 space-y-2">
                        @if($post->location)
                            <p class="text-[8px] md:text-[10px] text-white/70 uppercase tracking-widest font-medium">{{ $post->location }}</p>
                        @endif
                        <div class="flex items-center justify-between">
                            <p class="text-xs md:text-sm text-white font-bold lowercase truncate pr-4">view story</p>
                            <div class="flex items-center gap-3 text-white">
                                @if($post->reactions->count() > 0)
                                    <span class="flex items-center gap-1 text-[10px] font-bold">
                                        <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                        {{ $post->reactions->count() }}
                                    </span>
                                @endif
                                <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full py-20 text-center space-y-3">
                <p class="text-[10px] theme-text opacity-30 lowercase italic tracking-widest">no stories to share yet.</p>
            </div>
        @endforelse
    </div>

    @if($posts->hasPages())
        <div class="py-10">
            {{ $posts->links() }}
        </div>
    @endif
</div>
