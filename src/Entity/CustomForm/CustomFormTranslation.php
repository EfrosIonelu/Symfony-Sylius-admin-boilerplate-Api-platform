<?php

namespace App\Entity\CustomForm;

use App\Entity\Shared\AbstractTranslation;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Resource\Model\TranslatableInterface;

#[ORM\Entity]
#[ORM\Table(name: 'app_custom_form_translation')]
class CustomFormTranslation extends AbstractTranslation implements CustomFormTranslationInterface
{
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /** @var ?CustomForm */
    #[ORM\ManyToOne(targetEntity: CustomForm::class, inversedBy: 'translations')]
    #[ORM\JoinColumn(nullable: false)]
    protected ?TranslatableInterface $translatable = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }
}
