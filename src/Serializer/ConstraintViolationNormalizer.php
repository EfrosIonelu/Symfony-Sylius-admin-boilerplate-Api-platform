<?php

namespace App\Serializer;

use ApiPlatform\Validator\Exception\ValidationException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ConstraintViolationNormalizer implements NormalizerInterface
{
    public function __construct(
        private readonly bool $debug,
    ) {
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return is_a($data, ValidationException::class);
    }

    public function normalize(mixed $data, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        if (!$data instanceof ValidationException) {
            return $data;
        }

        $normalized = [
            'type' => $data->getType(),
            'title' => $data->getTitle(),
            'status' => $data->getStatus(),
            'instance' => $data->getInstance(),
        ];

        $violations = [];
        foreach ($data->getConstraintViolationList() as $violation) {
            $violationData = [
                'propertyPath' => $violation->getPropertyPath(),
                'message' => $violation->getMessage(),
                'invalidValue' => $violation->getInvalidValue(),
                'code' => $violation->getCode(),
            ];

            if ($this->debug) {
                $violationData = array_merge($violationData, [
                    'messageTemplate' => $violation->getMessageTemplate(),
                    'parameters' => $violation->getParameters(),
                    'plural' => $violation->getPlural(),
                    'constraint' => $violation->getConstraint() ? [
                        'class' => get_class($violation->getConstraint()),
                        'payload' => $violation->getConstraint()->payload ?? [],
                        'groups' => $violation->getConstraint()->groups ?? [],
                    ] : null,
                    'root' => $violation->getRoot() ? [
                        'class' => get_class($violation->getRoot()),
                        'string_representation' => method_exists($violation->getRoot(), '__toString')
                            ? (string) $violation->getRoot()
                            : null,
                    ] : null,
                    'cause' => $violation->getCause() ? [
                        'class' => get_class($violation->getCause()),
                        'message' => method_exists($violation->getCause(), 'getMessage')
                            ? $violation->getCause()->getMessage()
                            : null,
                    ] : null,
                ]);
            }

            $violations[] = $violationData;
        }

        $normalized['violations'] = $violations;

        if ($this->debug) {
            $normalized = array_merge($normalized, [
                'exception' => [
                    'class' => get_class($data),
                    'file' => $data->getFile(),
                    'line' => $data->getLine(),
                    'trace' => $data->getTrace(),
                    'previous' => $data->getPrevious() ? [
                        'class' => get_class($data->getPrevious()),
                        'message' => $data->getPrevious()->getMessage(),
                        'file' => $data->getPrevious()->getFile(),
                        'line' => $data->getPrevious()->getLine(),
                    ] : null,
                ],
                'headers' => $data->getHeaders(),
                'id' => $data->getId(),
            ]);
        }

        return $normalized;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            '*' => true,
        ];
    }
}
