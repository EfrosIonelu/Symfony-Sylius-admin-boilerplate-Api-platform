<?php

namespace App\Entity\CustomForm;

use App\Entity\Shared\Entity;
use App\Entity\Traits\OrderAwareTrait;
use App\Form\Type\CustomForm\CustomFormFieldType;
use App\Grid\CustomForm\CustomFormFieldGrid;
use App\Repository\CustomForm\CustomFormFieldRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Metadata\BulkDelete;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Delete;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Update;
use Sylius\Resource\Model\TranslatableInterface;

#[AsResource(
    section: 'admin',
    formType: CustomFormFieldType::class,
    templatesDir: '@SyliusAdminUi/crud',
    routePrefix: '/%app_admin.path_name%',
    name: 'custom_form_field',
    operations: [
        new Index(
            grid: CustomFormFieldGrid::class
        ),
        new Create(),
        new Update(),
        new Delete(),
        new BulkDelete(),
    ],
)]
#[ORM\Entity(repositoryClass: CustomFormFieldRepository::class)]
#[ORM\Table(name: 'app_custom_form_field')]
class CustomFormField extends Entity implements TranslatableInterface
{
    use OrderAwareTrait;
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
    }

    #[ORM\ManyToOne(targetEntity: CustomForm::class, inversedBy: 'fields')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CustomForm $customForm = null;

    #[ORM\Column(type: 'fieldType')]
    private ?string $fieldType = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $allowedValues = null;

    #[ORM\Column(type: 'boolean')]
    private bool $required = false;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $attributes = null;

    /**
     * @var Collection<int, FormSubmissionValues>
     */
    #[ORM\OneToMany(targetEntity: FormSubmissionValues::class, mappedBy: 'field', cascade: ['persist', 'remove'], fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $submissionValues;

    /**
     * @var Collection<int, CustomFormFieldTranslation>
     */
    #[ORM\OneToMany(targetEntity: CustomFormFieldTranslation::class, mappedBy: 'translatable', cascade: ['persist', 'remove'], orphanRemoval: true, indexBy: 'locale')]
    protected $translations;

    public function __construct()
    {
        $this->initializeTranslationsCollection();
        $this->submissionValues = new ArrayCollection();
    }

    public function getCustomForm(): ?CustomForm
    {
        return $this->customForm;
    }

    public function setCustomForm(?CustomForm $customForm): static
    {
        $this->customForm = $customForm;

        return $this;
    }

    public function getFieldType(): ?string
    {
        return $this->fieldType;
    }

    public function setFieldType(?string $fieldType): static
    {
        $this->fieldType = $fieldType;

        return $this;
    }

    public function getAllowedValues(): ?array
    {
        return $this->allowedValues;
    }

    public function setAllowedValues(?array $allowedValues): static
    {
        $this->allowedValues = $allowedValues;

        return $this;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): static
    {
        $this->required = $required;

        return $this;
    }

    public function getAttributes(): ?array
    {
        return $this->attributes;
    }

    public function setAttributes(?array $attributes): static
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function getTranslationForLocale(?string $locale = null): ?CustomFormFieldTranslationInterface
    {
        $translation = $this->getTranslation($locale);
        if (!is_a($translation, CustomFormFieldTranslationInterface::class)) {
            return null;
        }

        return $translation;
    }

    public function getLabel(?string $locale = null): ?string
    {
        return $this->getTranslationForLocale($locale)?->getLabel();
    }

    public function setLabel(string $label, ?string $locale = null): void
    {
        $this->getTranslationForLocale($locale)?->setLabel($label);
    }

    /**
     * @return Collection<int, FormSubmissionValues>
     */
    public function getSubmissionValues(): Collection
    {
        return $this->submissionValues;
    }

    public function addSubmissionValue(FormSubmissionValues $submissionValue): static
    {
        if (!$this->submissionValues->contains($submissionValue)) {
            $this->submissionValues->add($submissionValue);
            $submissionValue->setField($this);
        }

        return $this;
    }

    public function removeSubmissionValue(FormSubmissionValues $submissionValue): static
    {
        if ($this->submissionValues->removeElement($submissionValue)) {
            if ($submissionValue->getField() === $this) {
                $submissionValue->setField(null);
            }
        }

        return $this;
    }

    protected function createTranslation(): CustomFormFieldTranslation
    {
        return new CustomFormFieldTranslation();
    }
}
