<?php

namespace App\Entity\Cms;

use App\Entity\Shared\AbstractTranslation;
use App\Repository\Cms\PageTranslationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Sylius\Resource\Model\TranslatableInterface;

#[ORM\Entity(repositoryClass: PageTranslationRepository::class)]
#[ORM\Table(name: 'app_cms_page_translation')]
class PageTranslation extends AbstractTranslation implements PageTranslationInterface
{
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Gedmo\Slug(fields: ['name'])]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    /** @var ?Page */
    #[ORM\ManyToOne(targetEntity: Page::class, inversedBy: 'translations')]
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }
}
