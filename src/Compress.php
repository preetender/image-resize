<?php

namespace Leve\Uploader;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Intervention\Image\ImageManagerStatic as Image;

final class Compress
{
    /**
     * @param UploadedFile|string $file
     * @return string
     */
    public static function execute(UploadedFile|string $file): string
    {
        return base64_encode(Image::make($file)->encode(
            Config::get('uploader.compress.extension', 'webp'),
            Config::get('uploader.compress.quality', 100)
        )->encoded);
    }
}
