<?php

namespace App\Jobs;

use App\Models\PostMedia;
use App\Services\ImageProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessMediaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $mediaId;
    protected $tempPath;
    protected $originalName;

    /**
     * Create a new job instance.
     */
    public function __construct(int $mediaId, string $tempPath, string $originalName)
    {
        $this->mediaId = $mediaId;
        $this->tempPath = $tempPath;
        $this->originalName = $originalName;
    }

    /**
     * Execute the job.
     */
    public function handle(ImageProcessor $processor): void
    {
        $media = PostMedia::find($this->mediaId);

        if (!$media) {
            Storage::disk('local')->delete($this->tempPath);
            return;
        }

        try {
            $results = $processor->process($this->tempPath, $this->originalName);

            $media->update([
                'original_file_name' => $results['original_file_name'],
                'file_path_original' => $results['file_path_original'],
                'file_path_thumbnail' => $results['file_path_thumbnail'],
                'file_type' => $results['file_type'],
                'file_size_kb' => $results['file_size_kb'],
            ]);

            // Delete the temporary file from local disk
            Storage::disk('local')->delete($this->tempPath);
        } catch (\Exception $e) {
            // Log error or handle retry logic
            throw $e;
        }
    }
}
