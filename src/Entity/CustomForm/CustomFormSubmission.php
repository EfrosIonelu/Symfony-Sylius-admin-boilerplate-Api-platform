<?php

namespace App\Entity\CustomForm;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use App\Entity\Shared\Entity;
use App\Entity\Traits\CreatedAtAwareTrait;
use App\Entity\Traits\CreatedByAwareTrait;
use App\Grid\CustomForm\CustomFormSubmissionGrid;
use App\Repository\CustomForm\CustomFormSubmissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Show;

#[ApiResource(
    shortName: 'CustomFormSubmission',
    description: 'Custom Form Submissions',
    operations: [
        new GetCollection(
            openapi: new OpenApiOperation(
                summary: 'Get list of all custom form submissions',
            ),
            normalizationContext: [
                'groups' => [
                    'custom_form_submission:list_read',
                ],
            ]
        ),
        new Get(
            openapi: new OpenApiOperation(
                summary: 'Get custom form submission by id'
            ),
            normalizationContext: [
                'groups' => [
                    'custom_form_submission:item_read',
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
    name: 'custom_form_submission',
    operations: [
        new Index(
            grid: CustomFormSubmissionGrid::class
        ),
        new Show(),
    ],
)]
#[ORM\Entity(repositoryClass: CustomFormSubmissionRepository::class)]
#[ORM\Table(name: 'app_custom_form_submission')]
class CustomFormSubmission extends Entity
{
    use CreatedAtAwareTrait;
    use CreatedByAwareTrait;

    #[ORM\ManyToOne(targetEntity: CustomForm::class, inversedBy: 'submissions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CustomForm $customForm = null;

    /**
     * @var Collection<int, FormSubmissionValues>
     */
    #[ORM\OneToMany(targetEntity: FormSubmissionValues::class, mappedBy: 'formSubmission', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $values;

    public function __construct()
    {
        $this->values = new ArrayCollection();
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

    /**
     * @return Collection<int, FormSubmissionValues>
     */
    public function getValues(): Collection
    {
        return $this->values;
    }

    public function addValue(FormSubmissionValues $value): static
    {
        if (!$this->values->contains($value)) {
            $this->values->add($value);
            $value->setFormSubmission($this);
        }

        return $this;
    }

    public function removeValue(FormSubmissionValues $value): static
    {
        if ($this->values->removeElement($value)) {
            if ($value->getFormSubmission() === $this) {
                $value->setFormSubmission(null);
            }
        }

        return $this;
    }
}
