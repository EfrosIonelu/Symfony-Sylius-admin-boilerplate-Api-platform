<?php

namespace App\Services;

use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;

class ImageUploader implements ImageUploaderInterface
{
    public function upload(ImageInterface $image): void
    {
    }

    public function remove(string $path): bool
    {
        return true;
    }
}
