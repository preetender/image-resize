<?php

namespace Leve\Uploader;

use Illuminate\Http\UploadedFile;

final class Temporary
{
    /**
     * @var array
     */
    private array $images;

    /**
     * ImageTemporary constructor.
     * @param array $images
     */
    private function __construct(array $images)
    {
        $this->images = $images;
    }

    /**
     * @param array $images
     * @return array
     */
    public static function create(array $images)
    {
        return (new self($images))->upload();
    }

    /**
     * @return array
     */
    private function upload(): array
    {
        $pictures = [];

        if (count($this->images) <= 0) {
            return $pictures;
        }

        /** @var UploadedFile $image */
        foreach ($this->images as $image) {
            if (!$image->isValid()) {
                continue;
            }

            $temp_file_name = $image->storeAs('temp', $image->getClientOriginalName());

            $pictures[] = [
                'mime' => $image->getMimeType(),
                'name' => $image->getClientOriginalName(),
                'file' => $temp_file_name
            ];
        }

        return $pictures;
    }
}
