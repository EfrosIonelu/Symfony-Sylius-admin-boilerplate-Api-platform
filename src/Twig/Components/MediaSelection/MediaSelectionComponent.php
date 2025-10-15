<?php

declare(strict_types=1);

namespace App\Twig\Components\MediaSelection;

use App\Repository\Cms\MediaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsLiveComponent]
class MediaSelectionComponent extends AbstractController
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    public function __construct(
        private readonly MediaRepository $mediaRepository,
    ) {
    }

    #[LiveProp]
    public array $selectedMediaIds = [];

    #[LiveProp]
    public string $initialSelectedMediaIds = '[]';

    #[LiveProp]
    public string $zone = 'main';

    #[LiveProp]
    public bool $multiple = false;

    #[LiveProp]
    public int $currentPage = 1;

    #[LiveProp]
    public int $itemsPerPage = 10;

    #[LiveProp]
    public bool $isModalOpen = false;

    #[LiveProp(writable: true)]
    public string $searchTerm = '';

    #[PostMount]
    public function preMount(): void
    {
        // Initialize selectedMediaIds from initialSelectedMediaIds JSON string
        if (!empty($this->initialSelectedMediaIds) && '[]' !== $this->initialSelectedMediaIds) {
            $decoded = json_decode($this->initialSelectedMediaIds, true);
            if (is_array($decoded)) {
                $this->selectedMediaIds = array_map('intval', $decoded);
            }
        }
    }

    public function getMediaList(): array
    {
        $offset = ($this->currentPage - 1) * $this->itemsPerPage;

        if (!empty($this->searchTerm)) {
            return $this->mediaRepository->findByOriginalNameLike($this->searchTerm, $this->itemsPerPage, $offset);
        }

        return $this->mediaRepository->findBy([], ['id' => 'DESC'], $this->itemsPerPage, $offset);
    }

    public function getTotalMedia(): int
    {
        if (!empty($this->searchTerm)) {
            return $this->mediaRepository->countByOriginalNameLike($this->searchTerm);
        }

        return $this->mediaRepository->count([]);
    }

    public function getTotalPages(): int
    {
        return (int) ceil($this->getTotalMedia() / $this->itemsPerPage);
    }

    public function isMediaSelected(int $mediaId): bool
    {
        return in_array($mediaId, $this->selectedMediaIds, true);
    }

    #[LiveAction]
    public function toggleMedia(#[LiveArg] int $mediaId): void
    {
        if (true == $this->multiple) {
            if ($this->isMediaSelected($mediaId)) {
                $this->selectedMediaIds = array_filter($this->selectedMediaIds, fn ($id) => $id !== $mediaId);
            } else {
                $this->selectedMediaIds[] = $mediaId;
            }
        } else {
            if ($this->isMediaSelected($mediaId)) {
                $this->selectedMediaIds = [];
            } else {
                $this->selectedMediaIds = [$mediaId];
            }
        }

        // Reorder array to have sequential indices
        $this->selectedMediaIds = array_values($this->selectedMediaIds);

        // Dispatch event to update the hidden input
        $this->dispatchBrowserEvent("media:selection-changed-{$this->zone}", [
            'selectedIds' => $this->selectedMediaIds,
        ]);
    }

    #[LiveAction]
    public function openModal(): void
    {
        $this->isModalOpen = true;
    }

    #[LiveAction]
    public function closeModal(): void
    {
        $this->isModalOpen = false;
    }

    #[LiveAction]
    public function goToPage(#[LiveArg] int $page): void
    {
        if ($page >= 1 && $page <= $this->getTotalPages()) {
            $this->currentPage = $page;
        }
    }

    #[LiveAction]
    public function previousPage(): void
    {
        if ($this->currentPage > 1) {
            --$this->currentPage;
        }
    }

    #[LiveAction]
    public function nextPage(): void
    {
        if ($this->currentPage < $this->getTotalPages()) {
            ++$this->currentPage;
        }
    }

    #[LiveAction]
    public function search(): void
    {
        // Reset to first page when searching
        $this->currentPage = 1;
    }

    #[LiveAction]
    public function clearSearch(): void
    {
        $this->searchTerm = '';
        $this->currentPage = 1;
    }

    public function getSelectedMedia(): array
    {
        if (empty($this->selectedMediaIds)) {
            return [];
        }

        return $this->mediaRepository->findBy(['id' => $this->selectedMediaIds]);
    }

    public function getSelectedMediaIds(): array
    {
        return $this->selectedMediaIds;
    }
}
