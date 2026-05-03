<?php

namespace App\Livewire;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreatePost extends Component
{
    use WithFileUploads;

    public $step = 1;
    public $post;
    public $isEdit = false;
    public $existingMedia = [];
    public $photos = [];
    public $location = '';
    public $is_public = false;

    public function mount($post = null)
    {
        if ($post) {
            $post = Post::with('media')->findOrFail($post);
            $this->post = $post;
            $this->isEdit = true;
            $this->location = $post->location;
            $this->is_public = $post->is_public;
            $this->existingMedia = $post->media->map(fn($m) => [
                'id' => $m->id,
                'file_path_original' => $m->file_path_original
            ])->toArray();
            $this->step = 2; // Go straight to preview
        }
    }

    public function updatedPhotos()
    {
        $this->validate([
            'photos.*' => 'image|max:10240', // 10MB max
        ]);
        $this->step = 2;
    }

    public function savePost($base64Images = [], $keepMediaIds = [], $imageLocations = [], $capturedDates = [], $lats = [], $lons = [])
    {
        \Illuminate\Support\Facades\Log::info('savePost started', ['count' => count($base64Images)]);
        try {
            $this->validate([
                'location' => 'nullable|string',
            ]);

        if ($this->isEdit) {
            $this->post->update([
                'title' => $this->location ?: 'a memory',
                'location' => $this->location,
                'is_public' => $this->is_public,
            ]);
            $post = $this->post;

            // Delete media not kept
            $post->media()->whereNotIn('id', $keepMediaIds)->get()->each(function($m) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($m->file_path_original);
                $m->delete();
            });
        } else {
            $post = Auth::user()->relationship->posts()->create([
                'user_id' => Auth::id(),
                'title' => $this->location ?: 'a memory',
                'location' => $this->location,
                'type' => 'memory',
                'is_public' => $this->is_public,
                'is_secret' => false,
                'published_at' => now(),
            ]);
        }

        if (!empty($base64Images)) {
            $lastSortOrder = $post->media()->max('sort_order') ?? -1;
            
            foreach ($base64Images as $index => $base64) {
                // Decode base64
                $image_parts = explode(";base64,", $base64);
                if (count($image_parts) < 2) continue;
                
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1] ?? 'jpeg';
                $image_base64 = base64_decode($image_parts[1]);

                $filename = Str::random(40) . '.' . $image_type;
                $path = 'memories/' . $filename;
                
                \Illuminate\Support\Facades\Storage::disk('public')->put($path, $image_base64);

                $post->media()->create([
                    'file_path_original' => $path,
                    'file_path_thumbnail' => $path,
                    'file_type' => 'image/' . $image_type,
                    'file_size_kb' => strlen($image_base64) / 1024,
                    'location' => $imageLocations[$index] ?? null,
                    'captured_at' => ($capturedDates[$index] ?? null) ? \Carbon\Carbon::parse($capturedDates[$index]) : null,
                    'lat' => $lats[$index] ?? null,
                    'lon' => $lons[$index] ?? null,
                    'sort_order' => $lastSortOrder + $index + 1,
                ]);
            }
        }

        \Illuminate\Support\Facades\Log::info('savePost finished');
        
        $firstMedia = $post->media()->first();
        if ($firstMedia) {
            return route('gallery.preview', $firstMedia->id);
        }

        return route('gallery');
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('savePost failed: ' . $e->getMessage());
        throw $e;
    }
}

    public function render()
    {
        return view('livewire.create-post')
            ->layout('layouts.app');
    }
}
