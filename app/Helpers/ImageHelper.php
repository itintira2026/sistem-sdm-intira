<?php

namespace App\Helpers;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver; // atau Imagick\Driver
use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    /**
     * Compress and convert image to WebP
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory Directory path (e.g., 'daily_reports/123')
     * @param int $maxWidth Maximum width (default: 1920)
     * @param int $maxHeight Maximum height (default: 1080)
     * @param int $quality WebP quality (default: 80)
     * @return string File path
     */
    public static function compressAndSave($file, $directory, $maxWidth = 1920, $maxHeight = 1080, $quality = 80)
    {
        // Generate unique filename
        $fileName = time() . '_' . uniqid() . '.webp';

        // Full path
        $fullPath = $directory . '/' . $fileName;

        // Create ImageManager instance with GD driver
        $manager = new ImageManager(new Driver());

        // Load image
        $img = $manager->read($file);

        // Resize with aspect ratio constraint
        $img->scale(width: $maxWidth, height: $maxHeight);

        // Encode to WebP
        $encoded = $img->toWebp($quality);

        // Save to storage
        Storage::disk('public')->put($fullPath, (string) $encoded);

        return $fullPath;
    }

    /**
     * Delete image from storage
     *
     * @param string $filePath
     * @return bool
     */
    public static function delete($filePath)
    {
        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->delete($filePath);
        }

        return false;
    }

    /**
     * Check if file exists
     *
     * @param string $filePath
     * @return bool
     */
    public static function exists($filePath)
    {
        return Storage::disk('public')->exists($filePath);
    }
}
