<?php

namespace App\Twig\Components\Trait;

use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

trait HasLiveTableHeadersTrait
{
    use HasPaginationTrait;

    #[LiveProp(writable: true)]
    public array $sorting = [];

    #[LiveAction]
    public function sortBy(#[LiveArg] string $field, #[LiveArg] string $direction = 'asc'): void
    {
        $this->sorting = [$field => $direction];
        $this->page = 1; // Reset the first page when sorting
    }

    #[LiveAction]
    public function toggleSort(#[LiveArg] string $field): void
    {
        if (isset($this->sorting[$field])) {
            $currentDirection = $this->sorting[$field];
            $newDirection = 'asc' === $currentDirection ? 'desc' : 'asc';
        } else {
            $newDirection = 'asc';
        }

        $this->sortBy($field, $newDirection);
    }

    public function getSortingDirection(string $field): ?string
    {
        return $this->sorting[$field] ?? null;
    }

    public function isSortedBy(string $field): bool
    {
        return isset($this->sorting[$field]);
    }

    protected function getSortingParameters(): array
    {
        return ['sorting' => $this->sorting];
    }
}
