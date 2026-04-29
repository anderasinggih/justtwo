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
    <div class="grid grid-cols-3 lg:grid-cols-4 gap-1 md:gap-4 lg:gap-6">
        @forelse($posts as $post)
            <a href="{{ route('posts.preview', $post) }}" wire:navigate 
                 class="aspect-square bg-gray-50 overflow-hidden relative group cursor-pointer rounded-xl md:rounded-[2rem] transition-all">
                {{-- Media --}}
                @if($post->media->isNotEmpty())
                    <img src="{{ Storage::disk('public')->url($post->media->first()->file_path_original) }}" 
                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    
                    @if($post->media->count() > 1)
                        <div class="absolute top-2 right-2 md:top-4 md:right-4 bg-white/20 backdrop-blur-md px-1.5 py-0.5 rounded text-[8px] md:text-[10px] font-bold text-white">
                            +{{ $post->media->count() - 1 }}
                        </div>
                    @endif
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gray-100 p-4">
                        <p class="text-[10px] theme-text opacity-40 line-clamp-3 italic">{{ $post->content }}</p>
                    </div>
                @endif

                {{-- Hover Overlay --}}
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center text-white p-4">
                    <div class="flex items-center gap-4 text-xs font-bold">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.1 18.55l-.1.1-.1-.1C7.14 14.24 4 11.39 4 8.5 4 6.5 5.5 5 7.5 5c1.54 0 3.04.99 3.57 2.36h1.87C13.46 5.99 14.96 5 16.5 5c2 0 3.5 1.5 3.5 3.5 0 2.89-3.14 5.74-7.9 10.05z"></path></svg>
                            view
                        </span>
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
