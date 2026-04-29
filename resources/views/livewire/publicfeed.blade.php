<?php

use App\Models\Post;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public function with(): array
    {
        $allPosts = Post::where('is_public', true)
            ->where('is_archived', false)
            ->where('is_secret', false)
            ->with(['media'])
            ->latest()
            ->get();

        $albums = $allPosts->groupBy(function($post) {
            return $post->created_at->format('Y|F');
        })->map(function($posts, $key) {
            [$year, $month] = explode('|', $key);
            
            // Collect all media from these posts to show a preview strip
            $allMedia = $posts->flatMap(fn($p) => $p->media)->take(4);
            
            return (object) [
                'year' => $year,
                'month' => $month,
                'posts_count' => $posts->count(),
                'media_previews' => $allMedia,
                'fallback_text' => $posts->first()->content
            ];
        });

        return [
            'albums' => $albums
        ];
    }
}; ?>

<div class="space-y-12">
    {{-- iOS Style Collection Grid --}}
    <div class="grid grid-cols-2 gap-4 px-4 md:grid-cols-3 lg:grid-cols-4 md:gap-8 lg:gap-10">
        @forelse($albums as $album)
            <a href="{{ route('public.album', ['year' => $album->year, 'month' => $album->month]) }}" wire:navigate 
               class="group relative flex flex-col gap-2 cursor-pointer transition-all duration-500 active:scale-95">
                
                {{-- Collection Card --}}
                <div class="relative aspect-square w-full bg-white/5 rounded-[1.5rem] md:rounded-[2rem] overflow-hidden border theme-border shadow-sm group-hover:shadow-xl transition-all duration-500">
                    @if($album->media_previews->isNotEmpty())
                        {{-- Main Large Image --}}
                        <div class="h-full w-full relative">
                            <img src="{{ Storage::disk('public')->url($album->media_previews->first()->file_path_original) }}" 
                                 class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
                            
                            {{-- Preview Strip (iOS Style) --}}
                            @if($album->media_previews->count() > 1)
                                <div class="absolute bottom-0 left-0 right-0 h-1/4 flex gap-[1px] bg-black/10 backdrop-blur-sm border-t border-white/10 overflow-hidden">
                                    @foreach($album->media_previews->skip(1) as $media)
                                        <div class="flex-1 h-full overflow-hidden">
                                            <img src="{{ Storage::disk('public')->url($media->file_path_original) }}" class="w-full h-full object-cover">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="w-full h-full flex items-center justify-center p-6 bg-gradient-to-br from-brand-50 to-white/10">
                            <p class="text-[9px] md:text-xs theme-text opacity-40 line-clamp-4 italic text-center leading-relaxed px-2">{{ $album->fallback_text }}</p>
                        </div>
                    @endif

                    {{-- Title Overlay (iOS Style) --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent flex flex-col justify-end p-3 md:p-5 pointer-events-none">
                        <div class="transform group-hover:-translate-y-1 transition-transform duration-500">
                            <h3 class="text-sm md:text-lg font-bold text-white lowercase leading-tight drop-shadow-md">
                                {{ $album->month }}
                            </h3>
                            <p class="text-[9px] md:text-[11px] text-white/60 font-medium lowercase tracking-tight">
                                {{ $album->posts_count }} memories • {{ $album->year }}
                            </p>
                        </div>
                    </div>

                    {{-- Play/Folder Icon --}}
                    <div class="absolute top-3 right-3 md:top-5 md:right-5 bg-black/20 backdrop-blur-md p-1.5 rounded-full border border-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full py-20 text-center space-y-3">
                <p class="text-[10px] theme-text opacity-30 lowercase italic tracking-widest">no collections yet.</p>
            </div>
        @endforelse
    </div>
</div>
