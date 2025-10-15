<?php

declare(strict_types=1);

namespace App\EventListener\Doctrine;

use App\Entity\Cms\Media;
use App\Entity\Cms\Media\ImageMedia;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[AsDoctrineListener(event: Events::prePersist, priority: -2)]
#[AsDoctrineListener(event: Events::preUpdate, priority: -1)]
class MediaEventListener
{
    public function prePersist(LifecycleEventArgs $event): void
    {
        $entity = $event->getObject();
        if (!$entity instanceof Media) {
            return;
        }

        $file = $entity->getFile();
        if (!$file instanceof File) {
            return;
        }

        $originalFileName = $file->getFilename();

        $entity->setExtension($this->getExtension($originalFileName));
    }

    public function preUpdate(LifecycleEventArgs $event): void
    {
        $entity = $event->getObject();
        if (!$entity instanceof Media) {
            return;
        }

        $file = $entity->getFile();
        if (!$file instanceof UploadedFile) {
            return;
        }

        $originalFileName = $file->getClientOriginalName();
        $entity->setExtension($this->getExtension($originalFileName));
        if ($entity instanceof ImageMedia) {
            $entity->setLiipPaths(null);
        }
    }

    private function getExtension(?string $originalFileName): string
    {
        if (null === $originalFileName) {
            return '';
        }

        $parts = explode('.', $originalFileName);
        if (count($parts) < 2) {
            return '';
        }
        $extension = array_pop($parts);

        return strtolower($extension);
    }
}
