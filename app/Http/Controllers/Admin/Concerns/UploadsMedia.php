<?php

namespace App\Http\Controllers\Admin\Concerns;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait UploadsMedia
{
    /**
     * Store an uploaded image on the public disk and return its path.
     * Large images are downscaled with GD when the extension is available.
     */
    protected function storeImage(UploadedFile $file, string $directory = 'media'): string
    {
        $path = $file->store($directory, 'public');

        $this->optimizeImage(Storage::disk('public')->path($path));

        return $path;
    }

    protected function optimizeImage(string $absolutePath, int $maxWidth = 1800): void
    {
        if (! function_exists('imagecreatefromjpeg')) {
            return;
        }

        try {
            $info = @getimagesize($absolutePath);

            if ($info === false) {
                return;
            }

            [$width, $height] = $info;

            if ($width <= $maxWidth) {
                return;
            }

            $mime = $info['mime'];

            $create = match ($mime) {
                'image/jpeg' => 'imagecreatefromjpeg',
                'image/png' => 'imagecreatefrompng',
                'image/webp' => 'imagecreatefromwebp',
                default => null,
            };

            if ($create === null) {
                return;
            }

            $src = @$create($absolutePath);

            if ($src === false) {
                return;
            }

            $newWidth = $maxWidth;
            $newHeight = (int) round($height * ($maxWidth / $width));
            $dst = imagecreatetruecolor($newWidth, $newHeight);

            if ($mime === 'image/png') {
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
            }

            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            match ($mime) {
                'image/jpeg' => imagejpeg($dst, $absolutePath, 82),
                'image/png' => imagepng($dst, $absolutePath, 6),
                'image/webp' => imagewebp($dst, $absolutePath, 82),
                default => null,
            };

            imagedestroy($src);
            imagedestroy($dst);
        } catch (\Throwable) {
            // Optimization is best-effort; the original upload is kept on failure.
        }
    }
}
