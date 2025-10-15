<?php

declare(strict_types=1);

namespace App\Form\Traits;

use App\Entity\EntityMedia\EntityMedia;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait MultiZoneMediaTrait
{
    /**
     * Combine media from multiple zones into a single collection.
     */
    public function combineMediaFromZones(array $zoneMediaCollections): Collection
    {
        $combinedCollection = new ArrayCollection();

        foreach ($zoneMediaCollections as $collection) {
            if ($collection instanceof Collection) {
                /** @var EntityMedia $entityMedia */
                foreach ($collection as $entityMedia) {
                    $combinedCollection->add($entityMedia);
                }
            }
        }

        return $combinedCollection;
    }

    /**
     * Split a combined media collection back into zone-specific collections.
     */
    public function splitMediaByZones(Collection $combinedCollection, array $zones): array
    {
        $zoneCollections = [];

        // Initialize empty collections for each zone
        foreach ($zones as $zone) {
            $zoneCollections[$zone] = new ArrayCollection();
        }

        /** @var EntityMedia $entityMedia */
        foreach ($combinedCollection as $entityMedia) {
            $zone = $entityMedia->getZone();
            if ($zone && isset($zoneCollections[$zone])) {
                $zoneCollections[$zone]->add($entityMedia);
            }
        }

        return $zoneCollections;
    }

    /**
     * Get media IDs from a collection for a specific zone.
     */
    public function getMediaIdsForZone(Collection $collection, string $zone): array
    {
        $mediaIds = [];

        /** @var EntityMedia $entityMedia */
        foreach ($collection as $entityMedia) {
            if ($entityMedia->getZone() === $zone) {
                $mediaIds[] = $entityMedia->getMedia()->getId();
            }
        }

        return $mediaIds;
    }
}
