<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ArchiveCleanup extends Command
{
    protected $signature = 'cleanup:archive';
    protected $description = 'Permanently delete archived posts older than 30 days';

    public function handle()
    {
        $cutoff = now()->subDays(30);
        
        $postsToDelete = Post::where('is_archived', true)
            ->where('archived_at', '<=', $cutoff)
            ->get();

        $count = $postsToDelete->count();

        foreach ($postsToDelete as $post) {
            foreach ($post->media as $media) {
                Storage::disk('public')->delete($media->file_path_original);
                if ($media->file_path_thumbnail) {
                    Storage::disk('public')->delete($media->file_path_thumbnail);
                }
                $media->delete();
            }
            $post->delete();
        }

        $this->info("Successfully deleted {$count} archived posts.");
    }
}
