<?php

namespace App\Entity\Cms;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use App\Entity\Shared\Entity;
use App\Form\Type\Translation\TranslationType;
use App\Grid\Cms\TranslationGrid;
use App\Repository\Cms\TranslationRepository;
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
    shortName: 'Translation',
    description: 'App translations',
    operations: [
        new GetCollection(
            openapi: new OpenApiOperation(
                summary: 'Get list of all translations',
            ),
            normalizationContext: [
                'groups' => [
                    'translation:list_read',
                ],
            ]
        ),
        new Get(
            openapi: new OpenApiOperation(
                summary: 'Get translation by id'
            ),
            normalizationContext: [
                'groups' => [
                    'translation:item_read',
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
    formType: TranslationType::class,
    templatesDir: '@SyliusAdminUi/crud',
    routePrefix: '/%app_admin.path_name%',
    name: 'translation',
    operations: [
        new Index(
            grid: TranslationGrid::class
        ),
        new Create(),
        new Update(),
        new Delete(),
        new BulkDelete(),
    ],
)]
#[ORM\Entity(repositoryClass: TranslationRepository::class)]
#[ORM\Table(name: 'app_cms_translation')]
class Translation extends Entity implements TranslatableInterface
{
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
    }

    #[ORM\Column(name: '`key`', length: 255, unique: true)]
    private ?string $key = null;

    /**
     * @var Collection<int, TranslationTranslation>
     */
    #[ORM\OneToMany(targetEntity: TranslationTranslation::class, mappedBy: 'translatable', cascade: ['persist', 'remove'], orphanRemoval: true, indexBy: 'locale')]
    protected $translations;

    public function __construct()
    {
        $this->initializeTranslationsCollection();
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(string $key): static
    {
        $this->key = $key;

        return $this;
    }

    public function getTranslationForLocale(?string $locale = null): ?TranslationTranslationInterface
    {
        $translation = $this->getTranslation($locale);
        if (!is_a($translation, TranslationTranslationInterface::class)) {
            return null;
        }

        return $translation;
    }

    public function getValue(?string $locale = null): ?string
    {
        return $this->getTranslationForLocale($locale)?->getValue();
    }

    public function setValue(string $value, ?string $locale = null): void
    {
        $this->getTranslationForLocale($locale)?->setValue($value);
    }

    protected function createTranslation(): TranslationTranslation
    {
        return new TranslationTranslation();
    }
}
