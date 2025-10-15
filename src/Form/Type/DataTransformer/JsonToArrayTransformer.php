<?php

declare(strict_types=1);

namespace App\Form\Type\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class JsonToArrayTransformer implements DataTransformerInterface
{
    public function transform($value): string
    {
        if (null === $value || [] === $value) {
            return '';
        }

        if (!is_array($value)) {
            return '';
        }

        return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function reverseTransform($value): ?array
    {
        if (empty($value)) {
            return null;
        }

        if (!is_string($value)) {
            throw new TransformationFailedException('Expected a string.');
        }

        $decoded = json_decode($value, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new TransformationFailedException(sprintf('Invalid JSON: %s', json_last_error_msg()));
        }

        return $decoded;
    }
}
