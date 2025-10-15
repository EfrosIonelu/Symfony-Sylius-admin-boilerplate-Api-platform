<?php

namespace App\Entity\CustomForm;

use App\Entity\Shared\AbstractTranslation;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Resource\Model\TranslatableInterface;

#[ORM\Entity]
#[ORM\Table(name: 'app_custom_form_field_translation')]
class CustomFormFieldTranslation extends AbstractTranslation implements CustomFormFieldTranslationInterface
{
    #[ORM\Column(length: 255)]
    private ?string $label = null;

    /** @var ?CustomFormField */
    #[ORM\ManyToOne(targetEntity: CustomFormField::class, inversedBy: 'translations')]
    #[ORM\JoinColumn(nullable: false)]
    protected ?TranslatableInterface $translatable = null;

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }
}
