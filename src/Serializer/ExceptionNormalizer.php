<?php

namespace App\Serializer;

use ApiPlatform\State\ApiResource\Error;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ExceptionNormalizer implements NormalizerInterface
{
    public function __construct(
        private readonly bool $debug,
    ) {
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return is_a($data, Error::class);
    }

    public function normalize(mixed $data, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        if ($data instanceof Error) {
            $response = [
                'detail' => $data->getDetail(),
                'title' => $data->getTitle(),
                'status' => $data->getStatusCode(),
            ];

            if ($this->debug) {
                $response['originalTrace'] = $data->originalTrace;
                $response['description'] = $data->getDescription();
            }

            return $response;
        }

        return $data;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            '*' => true,
        ];
    }
}
