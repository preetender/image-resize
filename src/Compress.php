<?php

namespace Leve\Uploader;

use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManagerStatic as Image;

final class Compress
{
    /**
     * @param UploadedFile|string $file
     * @return string
     */
    public static function execute(UploadedFile|string $file): string
    {
        return base64_encode(Image::make($file)->encode('webp', 100)->encoded);
    }
}
