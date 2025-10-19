<?php

namespace App\Controller\Admin;

use App\Entity\Log\ExportFileLog;
use App\Message\ExportFile\ExportFileMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class CustomExportController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function exportAction(
        Request $request,
        string $format,
        ?string $resource = null,
    ): JsonResponse {
        if (!in_array($format, ['csv', 'zip'])) {
            throw new BadRequestHttpException('Format is not supported');
        }

        if (!$resource) {
            if (!$resource = $request->get('resource')) {
                throw new BadRequestHttpException('Resource is required');
            }
        }

        $exportFileLog = new ExportFileLog($resource, $format);
        if ($additionalData = $request->get('additionalData')) {
            if (!is_array($additionalData)) {
                throw new BadRequestHttpException('AdditionalData is not an array');
            }
            $exportFileLog->setAdditionalData($additionalData, true);
        }

        if ($criteria = $request->get('criteria')) {
            $exportFileLog->addToAdditionalData(['criteria' => $criteria]);
        }

        if (isset($request->get('_sylius')['grid'])) {
            $exportFileLog->addToAdditionalData(['grid' => $request->get('_sylius')['grid']]);
        }

        $this->entityManager->persist($exportFileLog);
        $this->entityManager->flush();

        $this->messageBus->dispatch(new ExportFileMessage($exportFileLog->getExternalId()));

        return new JsonResponse([
            'status' => $exportFileLog->getStatus(),
            'process_id' => $exportFileLog->getExternalId(),
        ]);
    }

    public function statusAction(Request $request): JsonResponse
    {
        $exportFileLog = $this->entityManager->getRepository(ExportFileLog::class)
            ->findOneBy(['externalId' => $request->get('id')]);
        if (!$exportFileLog) {
            return new JsonResponse(['status' => ExportFileLog::ERROR]);
        }

        return new JsonResponse(['status' => $exportFileLog->getStatus()]);
    }

    public function getFileAction(Request $request): Response
    {
        $exportFileLog = $this->entityManager->getRepository(ExportFileLog::class)
            ->findOneBy(['externalId' => $request->get('id')]);
        if (!$exportFileLog) {
            return new JsonResponse(['status' => ExportFileLog::ERROR]);
        }

        $filePath = sys_get_temp_dir().'/export_files/'.$exportFileLog->getFileName();
        if (!file_exists($filePath)) {
            return new JsonResponse(['status' => ExportFileLog::ERROR]);
        }

        $response = new Response(file_get_contents($filePath));

        unlink($filePath);

        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $exportFileLog->getFileName()
        );

        $response->headers->set('Content-Type', 'application/csv');
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
