<?php

namespace App\Entity\Cms;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use App\Entity\Shared\Entity;
use App\Grid\Cms\LanguagesGrid;
use App\Repository\Cms\LanguagesRepository;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Metadata\BulkDelete;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Delete;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Update;

#[ApiResource(
    shortName: 'Languages',
    description: 'App languages',
    operations: [
        new GetCollection(
            openapi: new OpenApiOperation(
                summary: 'Get list of all languages',
            ),
            normalizationContext: [
                'groups' => [
                    'languages:list_read',
                ],
            ]
        ),
        new Get(
            openapi: new OpenApiOperation(
                summary: 'Get language by id'
            ),
            normalizationContext: [
                'groups' => [
                    'languages:item_read',
                ],
            ]
        ),
    ],
    paginationClientItemsPerPage: true,
    paginationEnabled: true,
    paginationItemsPerPage: 25,
    security: "is_granted('ROLE_USER')"
)]
#[AsResource(
    section: 'admin',
    templatesDir: '@SyliusAdminUi/crud',
    routePrefix: '/%app_admin.path_name%',
    name: 'languages',
    operations: [
        new Index(
            grid: LanguagesGrid::class
        ),
        new Create(),
        new Update(),
        new Delete(),
        new BulkDelete(),
    ],
)]
#[ORM\Entity(repositoryClass: LanguagesRepository::class)]
#[ORM\Table(name: 'app_cms_languages')]
class Languages extends Entity
{
    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 10)]
    private ?string $locale = null;

    #[ORM\Column(type: 'boolean', options: ['default' => 1])]
    protected bool $enabled = true;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }
}
