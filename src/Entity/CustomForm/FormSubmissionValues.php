<?php

namespace App\Entity\CustomForm;

use App\Entity\Shared\Entity;
use App\Repository\CustomForm\FormSubmissionValuesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormSubmissionValuesRepository::class)]
#[ORM\Table(name: 'app_form_submission_values')]
class FormSubmissionValues extends Entity
{
    #[ORM\ManyToOne(targetEntity: CustomFormSubmission::class, inversedBy: 'values')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CustomFormSubmission $formSubmission = null;

    #[ORM\ManyToOne(targetEntity: CustomFormField::class, inversedBy: 'submissionValues')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CustomFormField $field = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $value = null;

    public function getFormSubmission(): ?CustomFormSubmission
    {
        return $this->formSubmission;
    }

    public function setFormSubmission(?CustomFormSubmission $formSubmission): static
    {
        $this->formSubmission = $formSubmission;

        return $this;
    }

    public function getField(): ?CustomFormField
    {
        return $this->field;
    }

    public function setField(?CustomFormField $field): static
    {
        $this->field = $field;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): static
    {
        $this->value = $value;

        return $this;
    }
}
