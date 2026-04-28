<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class ImageProcessor
{
    protected $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Process an image: resize, convert to WebP, and generate thumbnail.
     *
     * @param string $tempPath Path to the temporary raw file
     * @param string $originalName Original filename
     * @return array Metadata about the processed files
     */
    public function process(string $tempPath, string $originalName): array
    {
        $filename = Str::random(40) . '.webp';
        $originalFolder = 'media/original';
        $thumbnailFolder = 'media/thumbnails';

        // Ensure directories exist
        Storage::disk('public')->makeDirectory($originalFolder);
        Storage::disk('public')->makeDirectory($thumbnailFolder);

        $image = $this->manager->read(Storage::path($tempPath));

        // 1. Process Original (capped at 2560px)
        $originalImage = clone $image;
        if ($originalImage->width() > 2560 || $originalImage->height() > 2560) {
            $originalImage->scaleDown(width: 2560, height: 2560);
        }
        
        $originalPath = $originalFolder . '/' . $filename;
        $originalEncoded = $originalImage->toWebp(85);
        Storage::disk('public')->put($originalPath, (string) $originalEncoded);

        // 2. Process Thumbnail (800px)
        $thumbnailImage = clone $image;
        $thumbnailImage->scaleDown(width: 800, height: 800);
        
        $thumbnailPath = $thumbnailFolder . '/' . $filename;
        $thumbnailEncoded = $thumbnailImage->toWebp(75); // Slightly lower quality for thumbnails
        Storage::disk('public')->put($thumbnailPath, (string) $thumbnailEncoded);

        return [
            'original_file_name' => $originalName,
            'file_path_original' => $originalPath,
            'file_path_thumbnail' => $thumbnailPath,
            'file_type' => 'image/webp',
            'file_size_kb' => round(Storage::disk('public')->size($originalPath) / 1024),
        ];
    }
}
