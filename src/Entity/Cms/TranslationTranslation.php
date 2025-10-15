<?php

namespace App\Entity\Cms;

use App\Entity\Shared\AbstractTranslation;
use App\Form\Type\Translation\TranslationTranslationType;
use App\Repository\Cms\TranslationTranslationRepository;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Model\TranslatableInterface;

#[ORM\Entity(repositoryClass: TranslationTranslationRepository::class)]
#[ORM\Table(name: 'app_cms_translation_translation')]
#[AsResource(
    formType: TranslationTranslationType::class,
    name: 'translation_translation',
)]
class TranslationTranslation extends AbstractTranslation implements TranslationTranslationInterface
{
    #[ORM\Column(type: 'text')]
    private ?string $value = null;

    /** @var ?Translation */
    #[ORM\ManyToOne(targetEntity: Translation::class, inversedBy: 'translations')]
    #[ORM\JoinColumn(nullable: false)]
    protected ?TranslatableInterface $translatable = null;

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): void
    {
        $this->value = $value;
    }
}
