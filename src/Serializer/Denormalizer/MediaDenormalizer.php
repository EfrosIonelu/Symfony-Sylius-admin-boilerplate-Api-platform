<?php

declare(strict_types=1);

namespace App\Serializer\Denormalizer;

use ApiPlatform\Metadata\IriConverterInterface;
use App\Entity\Cms\Media;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class MediaDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    public function __construct(
        private readonly IriConverterInterface $iriConverter,
    ) {
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        if (is_string($data)) {
            return $this->iriConverter->getResourceFromIri($data, $context);
        }

        if (empty($data['type'])) {
            throw new \Exception('Type is not set');
        }

        if (!array_key_exists($data['type'], Media::getTypes())) {
            throw new \Exception('Unknown media type');
        }

        $context['prevent_circular_denormalization'] = true;

        return $this->denormalizer->denormalize($data, Media::getTypes()[$data['type']], $format, $context);
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return is_a($type, Media::class, true)
            && !isset($context['prevent_circular_denormalization']);
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Media::class => true,
        ];
    }
}
