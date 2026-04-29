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

        // 1. Process Original (Auto-crop to 4:5 ratio - Instagram Standard)
        $originalImage = clone $image;
        $originalImage->cover(1080, 1350); // Automatically crops center to 4:5
        
        $originalPath = $originalFolder . '/' . $filename;
        $originalEncoded = $originalImage->toWebp(85);
        Storage::disk('public')->put($originalPath, (string) $originalEncoded);

        // 2. Process Thumbnail (4:5 ratio at 600x750)
        $thumbnailImage = clone $image;
        $thumbnailImage->cover(600, 750);
        
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
