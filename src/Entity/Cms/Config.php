<?php

namespace App\Entity\Cms;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use App\Dto\Output\Cms\ConfigOutput;
use App\Entity\Log\LogEntry;
use App\Entity\Shared\Entity;
use App\Entity\Traits\CreatedByAwareTrait;
use App\Entity\Traits\SoftDeleteAwareTrait;
use App\Entity\Traits\TimestampsAwareTrait;
use App\Entity\Traits\UpdatedByAwareTrait;
use App\Form\Type\Cms\ConfigType;
use App\Grid\Cms\ConfigGrid;
use App\Repository\Cms\ConfigRepository;
use App\State\Provider\MainEntityProvider;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Metadata\BulkDelete;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Delete;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Update;

#[ApiResource(
    shortName: 'Config',
    description: 'App config',
    operations: [
        new GetCollection(
            openapi: new OpenApiOperation(
                summary: 'Get list of all configurations',
            ),
            normalizationContext: [
                'groups' => [
                    'config:list_read',
                ],
            ],
            provider: MainEntityProvider::class
        ),
        new Get(
            openapi: new OpenApiOperation(
                summary: 'Get config by id'
            ),
            normalizationContext: [
                'groups' => [
                    'config:item_read',
                ],
            ],
            provider: MainEntityProvider::class
        ),
    ],
    output: ConfigOutput::class,
    paginationClientItemsPerPage: true,
    paginationEnabled: true,
    paginationItemsPerPage: 25,
    security: "is_granted('ROLE_USER')"
)]
#[AsResource(
    section: 'admin',
    formType: ConfigType::class,
    templatesDir: '@SyliusAdminUi/crud',
    routePrefix: '/%app_admin.path_name%',
    name: 'config',
    operations: [
        new Index(
            grid: ConfigGrid::class
        ),
        new Create(),
        new Update(),
        new Delete(),
        new BulkDelete(),
    ],
)]
#[ORM\Entity(repositoryClass: ConfigRepository::class)]
#[ORM\Table(name: 'app_cms_config')]
#[Gedmo\Loggable(logEntryClass: LogEntry::class)]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false)]
class Config extends Entity
{
    use CreatedByAwareTrait;
    use SoftDeleteAwareTrait;
    use TimestampsAwareTrait;
    use UpdatedByAwareTrait;

    #[ORM\Column(length: 50)]
    #[Gedmo\Versioned]
    private ?string $keyword = null;

    #[ORM\Column(length: 255)]
    #[Gedmo\Versioned]
    private ?string $value = null;

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
