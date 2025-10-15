<?php

namespace App\Serializer\Denormalizer;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class UploadedFileDenormalizer implements DenormalizerInterface
{
    public function denormalize($data, string $type, ?string $format = null, array $context = []): UploadedFile
    {
        return $data;
    }

    /**
     * @param string $type
     * @param null   $format
     */
    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        return $data instanceof UploadedFile;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            '*' => true,
        ];
    }
}
