<?php

namespace App\Entity\Cms;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use App\Repository\ConfigRepository;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Delete;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Update;

/**
 * API Platform Entity Structure Example
 * 
 * Key Components:
 * - #[ApiResource] - Main API configuration
 * - operations - Define available API endpoints
 * - normalizationContext - Control serialization
 * - security - Access control rules
 * - #[AsResource] - Sylius resource configuration
 * - #[ORM\Entity] - Doctrine entity mapping
 */
#[ApiResource(
    shortName: "Config",
    description: "Configuration entity with API Platform integration",
    operations: [
        new GetCollection(
            openapi: new OpenApiOperation(
                summary: "Get list of all configurations",
            ),
            normalizationContext: [
                'groups' => ['config:list_read']
            ]
        ),
        new Get(
            openapi: new OpenApiOperation(
                summary: 'Get config by id'
            ),
            normalizationContext: [
                'groups' => ['config:item_read']
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
    routePrefix: '/admin',
    operations: [
        new Index(),
        new Create(),
        new Update(),
        new Delete(),
    ],
)]
#[ORM\Entity(repositoryClass: ConfigRepository::class)]
#[ORM\Table(name: 'app_cms_config')]
class Config implements ResourceInterface
{
    #[ApiProperty(identifier: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $keyword = null;

    #[ORM\Column(length: 255)]
    private ?string $value = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKeyword(): ?string
    {
        return $this->keyword;
    }

    public function setKeyword(string $keyword): static
    {
        $this->keyword = $keyword;
        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;
        return $this;
    }
}