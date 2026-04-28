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
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($posts as $post)
            <div class="bg-white rounded-[2.5rem] overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-500 group">
                {{-- Media --}}
                @if($post->media->isNotEmpty())
                    <div class="aspect-[4/5] bg-gray-50 overflow-hidden relative">
                        <img src="{{ Storage::disk('public')->url($post->media->first()->file_path_original) }}" 
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        
                        @if($post->media->count() > 1)
                            <div class="absolute top-4 right-4 bg-white/80 backdrop-blur-md px-2 py-1 rounded-lg text-[10px] font-bold text-gray-800">
                                +{{ $post->media->count() - 1 }}
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Info --}}
                <div class="p-6 space-y-3">
                    <div class="flex items-center gap-3">
                        <img src="{{ $post->user->profile_photo_url }}" class="w-6 h-6 rounded-full object-cover">
                        <span class="text-xs font-bold lowercase text-gray-500">{{ $post->user->name }}</span>
                    </div>
                    
                    @if($post->title)
                        <h3 class="text-lg font-bold lowercase tracking-tight">{{ $post->title }}</h3>
                    @endif
                    
                    <p class="text-sm text-gray-500 line-clamp-3 lowercase leading-relaxed">
                        {{ $post->content }}
                    </p>

                    <div class="pt-2 flex items-center justify-between text-[10px] text-gray-300 font-medium lowercase">
                        <span>{{ $post->created_at->format('M d, Y') }}</span>
                        @if($post->mood)
                            <span>{{ $post->mood }}</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-8 text-center space-y-3">
                <div class="w-12 h-12 bg-gray-50/50 rounded-full flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <p class="text-[10px] text-gray-400 lowercase italic">no public stories to share yet.</p>
            </div>
        @endforelse
    </div>

    @if($posts->hasPages())
        <div class="py-10">
            {{ $posts->links() }}
        </div>
    @endif
</div>
