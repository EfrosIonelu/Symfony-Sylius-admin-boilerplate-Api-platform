<?php

namespace App\Controller\Admin;

use App\Entity\CustomForm\CustomFormField;
use App\Entity\CustomForm\CustomFormSubmission;
use App\Entity\CustomForm\FormSubmissionValues;
use App\Repository\CustomForm\CustomFormRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class CustomFormSubmissionController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CustomFormRepository $customFormRepository,
    ) {
    }

    public function submitAction(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['code'])) {
            throw new BadRequestHttpException('Form code is required');
        }

        if (!isset($data['fields']) || !is_array($data['fields'])) {
            throw new BadRequestHttpException('Fields data is required');
        }

        $customForm = $this->customFormRepository->findOneBy(['code' => $data['code']]);

        if (!$customForm) {
            throw new NotFoundHttpException(sprintf('Custom form with code "%s" not found', $data['code']));
        }

        // Create submission
        $submission = new CustomFormSubmission();
        $submission->setCustomForm($customForm);

        // Validate and add field values
        $errors = [];
        $fieldErrors = [];
        foreach ($customForm->getFields() as $field) {
            /** @var CustomFormField $field */
            $fieldId = (string) $field->getId();
            $value = $data['fields'][$fieldId] ?? null;

            // Validate required fields
            if ($field->isRequired() && empty($value)) {
                $fieldErrors[$fieldId] = sprintf('This field is required');
                continue;
            }

            // Skip empty non-required fields
            if (empty($value)) {
                continue;
            }

            // Validate allowed values for select, radio fields
            if (!empty($field->getAllowedValues())) {
                $allowedValues = $field->getAllowedValues();

                // Handle array values (multiple selections)
                if (is_array($value)) {
                    foreach ($value as $val) {
                        if (!in_array($val, $allowedValues, true)) {
                            $fieldErrors[$fieldId] = sprintf('Invalid value "%s". Allowed values: %s',
                                $val,
                                implode(', ', $allowedValues)
                            );
                        }
                    }
                } else {
                    // Handle single value
                    if (!in_array($value, $allowedValues, true)) {
                        $fieldErrors[$fieldId] = sprintf('Invalid value. Allowed values: %s',
                            implode(', ', $allowedValues)
                        );
                        continue;
                    }
                }
            }

            // Convert array values to string (for multi-select, checkboxes, etc)
            if (is_array($value)) {
                $value = json_encode($value);
            }

            // Create submission value
            $submissionValue = new FormSubmissionValues();
            $submissionValue->setField($field);
            $submissionValue->setValue((string) $value);

            $submission->addValue($submissionValue);
        }

        if (!empty($fieldErrors)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Validation failed',
                'fieldErrors' => $fieldErrors,
            ], 400);
        }

        // Persist submission
        $this->entityManager->persist($submission);
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Form submitted successfully',
            'submission_id' => $submission->getId(),
        ]);
    }
}
