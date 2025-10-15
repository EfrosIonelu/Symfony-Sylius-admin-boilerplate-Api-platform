<?php

namespace App\Twig\Components\Trait;

use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

trait HasPaginationTrait
{
    #[LiveProp(writable: true)]
    public int $page = 1;

    #[LiveProp(writable: true)]
    public int $limit = 10;

    #[LiveAction]
    public function changePage(#[LiveArg] int $page): void
    {
        if ($page > 0) {
            $this->page = $page;
        }
    }

    #[LiveAction]
    public function changeLimit(#[LiveArg] int $limit): void
    {
        if ($limit > 0) {
            $this->limit = $limit;
            $this->page = 1; // Reset to first page when changing limit
        }
    }

    #[LiveAction]
    public function firstPage(): void
    {
        $this->page = 1;
    }

    #[LiveAction]
    public function lastPage(#[LiveArg] int $totalPages): void
    {
        if ($totalPages > 0) {
            $this->page = $totalPages;
        }
    }

    #[LiveAction]
    public function nextPage(#[LiveArg] int $totalPages): void
    {
        if ($this->page < $totalPages) {
            ++$this->page;
        }
    }

    #[LiveAction]
    public function previousPage(): void
    {
        if ($this->page > 1) {
            --$this->page;
        }
    }

    protected function getOffset(): int
    {
        return ($this->page - 1) * $this->limit;
    }

    protected function getPaginationParameters(): array
    {
        return [
            'page' => $this->page,
            'limit' => $this->limit,
        ];
    }
}
