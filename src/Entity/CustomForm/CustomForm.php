<?php

namespace App\Entity\CustomForm;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use App\Entity\Shared\Entity;
use App\Entity\Traits\CodeAwareTrait;
use App\Entity\Traits\CreatedByAwareTrait;
use App\Entity\Traits\EnabledAwareTrait;
use App\Entity\Traits\TimestampsAwareTrait;
use App\Form\Type\CustomForm\CustomFormType;
use App\Grid\CustomForm\CustomFormGrid;
use App\Repository\CustomForm\CustomFormRepository;
use Doctrine\Common\Collections\ArrayCollection;
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
    shortName: 'CustomForm',
    description: 'Custom Forms',
    operations: [
        new GetCollection(
            openapi: new OpenApiOperation(
                summary: 'Get list of all custom forms',
            ),
            normalizationContext: [
                'groups' => [
                    'custom_form:list_read',
                ],
            ]
        ),
        new Get(
            openapi: new OpenApiOperation(
                summary: 'Get custom form by id'
            ),
            normalizationContext: [
                'groups' => [
                    'custom_form:item_read',
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
    formType: CustomFormType::class,
    templatesDir: '@SyliusAdminUi/crud',
    routePrefix: '/%app_admin.path_name%',
    name: 'custom_form',
    operations: [
        new Index(
            grid: CustomFormGrid::class
        ),
        new Create(),
        new Update(),
        new Delete(),
        new BulkDelete(),
    ],
)]
#[ORM\Entity(repositoryClass: CustomFormRepository::class)]
#[ORM\Table(name: 'app_custom_form')]
class CustomForm extends Entity implements TranslatableInterface
{
    use CreatedByAwareTrait;
    use CodeAwareTrait;
    use TimestampsAwareTrait;
    use EnabledAwareTrait;
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
    }

    /**
     * @var Collection<int, CustomFormField>
     */
    #[ORM\OneToMany(targetEntity: CustomFormField::class, mappedBy: 'customForm', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $fields;

    /**
     * @var Collection<int, CustomFormSubmission>
     */
    #[ORM\OneToMany(targetEntity: CustomFormSubmission::class, mappedBy: 'customForm', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $submissions;

    /**
     * @var Collection<int, CustomFormTranslation>
     */
    #[ORM\OneToMany(targetEntity: CustomFormTranslation::class, mappedBy: 'translatable', cascade: ['persist', 'remove'], orphanRemoval: true, indexBy: 'locale')]
    protected $translations;

    public function __construct()
    {
        $this->initializeTranslationsCollection();
        $this->fields = new ArrayCollection();
        $this->submissions = new ArrayCollection();
    }

    public function getTranslationForLocale(?string $locale = null): ?CustomFormTranslationInterface
    {
        $translation = $this->getTranslation($locale);
        if (!is_a($translation, CustomFormTranslationInterface::class)) {
            return null;
        }

        return $translation;
    }

    public function getName(?string $locale = null): ?string
    {
        return $this->getTranslationForLocale($locale)?->getName();
    }

    public function setName(string $name, ?string $locale = null): void
    {
        $this->getTranslationForLocale($locale)?->setName($name);
    }

    /**
     * @return Collection<int, CustomFormField>
     */
    public function getFields(): Collection
    {
        return $this->fields;
    }

    public function addField(CustomFormField $field): static
    {
        if (!$this->fields->contains($field)) {
            $this->fields->add($field);
            $field->setCustomForm($this);
        }

        return $this;
    }

    public function removeField(CustomFormField $field): static
    {
        if ($this->fields->removeElement($field)) {
            if ($field->getCustomForm() === $this) {
                $field->setCustomForm(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CustomFormSubmission>
     */
    public function getSubmissions(): Collection
    {
        return $this->submissions;
    }

    public function addSubmission(CustomFormSubmission $submission): static
    {
        if (!$this->submissions->contains($submission)) {
            $this->submissions->add($submission);
            $submission->setCustomForm($this);
        }

        return $this;
    }

    public function removeSubmission(CustomFormSubmission $submission): static
    {
        if ($this->submissions->removeElement($submission)) {
            if ($submission->getCustomForm() === $this) {
                $submission->setCustomForm(null);
            }
        }

        return $this;
    }

    protected function createTranslation(): CustomFormTranslation
    {
        return new CustomFormTranslation();
    }
}
