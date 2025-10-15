<?php

declare(strict_types=1);

namespace App\Form\DataTransformer;

use App\Entity\EntityMedia\EntityMedia;
use App\Form\Traits\MultiZoneMediaTrait;
use App\Repository\Cms\MediaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\DataTransformerInterface;

class MultiZoneMediaTransformer implements DataTransformerInterface
{
    use MultiZoneMediaTrait;

    public function __construct(
        private readonly MediaRepository $mediaRepository,
        private readonly array $zones, // ['main', 'slider']
        private readonly string $entityMediaClass,
    ) {
    }

    /**
     * Transform EntityMedia collection to array of zone => [mediaIds].
     */
    public function transform($value): array
    {
        if (!$value instanceof Collection) {
            // Initialize empty arrays for each zone
            $result = [];
            foreach ($this->zones as $zone) {
                $result[$zone] = [];
            }

            return $result;
        }

        $zoneCollections = $this->splitMediaByZones($value, $this->zones);
        $result = [];

        foreach ($this->zones as $zone) {
            $mediaIds = [];
            if (isset($zoneCollections[$zone])) {
                $mediaIds = $this->getMediaIdsForZone($zoneCollections[$zone], $zone);
            }
            $result[$zone] = $mediaIds;
        }

        foreach ($result as $zone => $value) {
            $result[$zone] = json_encode($value);
        }

        return $result;
    }

    /**
     * Transform array of zone => [mediaIds] back to EntityMedia collection.
     */
    public function reverseTransform($value): Collection
    {
        if (!is_array($value)) {
            return new ArrayCollection();
        }

        $combinedCollection = new ArrayCollection();

        foreach ($this->zones as $zone) {
            if (!isset($value[$zone])) {
                continue;
            }

            if (is_string($value[$zone])) {
                $value[$zone] = json_decode($value[$zone], true);
            }

            if (!is_array($value[$zone])) {
                continue;
            }

            $mediaIds = $value[$zone];
            if (empty($mediaIds)) {
                continue;
            }

            $medias = $this->mediaRepository->findBy(['id' => $mediaIds]);

            foreach ($medias as $media) {
                /** @var EntityMedia $entityMedia */
                $entityMedia = new $this->entityMediaClass();
                $entityMedia->setMedia($media);
                $entityMedia->setZone($zone);

                $combinedCollection->add($entityMedia);
            }
        }

        return $combinedCollection;
    }
}
