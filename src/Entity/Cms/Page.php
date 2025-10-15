<?php

namespace App\Entity\Cms;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use App\Entity\EntityMedia\PageMedia;
use App\Entity\Shared\Entity;
use App\Entity\Traits\MediaAwareTrait;
use App\Form\Type\Page\PageType;
use App\Grid\Cms\PageGrid;
use App\Repository\Cms\PageRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Metadata\BulkDelete;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Delete;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Update;
use Sylius\Resource\Model\TranslatableInterface;

#[ApiResource(
    shortName: 'Page',
    description: 'App pages',
    operations: [
        new GetCollection(
            openapi: new OpenApiOperation(
                summary: 'Get list of all pages',
            ),
            normalizationContext: [
                'groups' => [
                    'page:list_read',
                ],
            ]
        ),
        new Get(
            openapi: new OpenApiOperation(
                summary: 'Get page by id'
            ),
            normalizationContext: [
                'groups' => [
                    'page:item_read',
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
    formType: PageType::class,
    templatesDir: '@SyliusAdminUi/crud',
    routePrefix: '/%app_admin.path_name%',
    name: 'page',
    operations: [
        new Index(
            grid: PageGrid::class
        ),
        new Create(),
        new Update(),
        new Delete(),
        new BulkDelete(),
    ],
)]
#[ORM\Entity(repositoryClass: PageRepository::class)]
#[ORM\Table(name: 'app_cms_page')]
class Page extends Entity implements TranslatableInterface
{
    use MediaAwareTrait;
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
    }

    #[ORM\Column(type: 'boolean')]
    private bool $enabled = true;

    /**
     * @var Collection<int, PageMedia>
     */
    #[ORM\OneToMany(targetEntity: PageMedia::class, mappedBy: 'entity', cascade: ['persist'], orphanRemoval: true)]
    private Collection $files;

    /**
     * @var Collection<int, PageTranslation>
     */
    #[ORM\OneToMany(targetEntity: PageTranslation::class, mappedBy: 'translatable', cascade: ['persist', 'remove'], orphanRemoval: true, indexBy: 'locale')]
    protected $translations;

    public function __construct()
    {
        $this->initializeTranslationsCollection();
        $this->initializeMedia();
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getTranslationForLocale(?string $locale = null): ?PageTranslationInterface
    {
        $translation = $this->getTranslation($locale);
        if (!is_a($translation, PageTranslationInterface::class)) {
            return null;
        }

        return $translation;
    }

    public function getName(?string $locale = null): ?string
    {
        return $this->getTranslationForLocale($locale)?->getName();
    }

    public function getSlug(?string $locale = null): ?string
    {
        return $this->getTranslationForLocale($locale)?->getSlug();
    }

    public function setName(string $name, ?string $locale = null): void
    {
        $this->getTranslationForLocale($locale)?->setName($name);
    }

    public function getContent(?string $locale = null): ?string
    {
        return $this->getTranslationForLocale($locale)?->getContent();
    }

    public function setContent(string $content, ?string $locale = null): void
    {
        $this->getTranslationForLocale($locale)?->setContent($content);
    }

    protected function createTranslation(): PageTranslation
    {
        return new PageTranslation();
    }
}
