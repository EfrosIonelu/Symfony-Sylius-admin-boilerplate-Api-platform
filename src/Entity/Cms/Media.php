<?php

namespace App\Entity\Cms;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use ApiPlatform\OpenApi\Model\RequestBody;
use App\Dto\Output\Cms\MediaOutput;
use App\Entity\Cms\Media\DocumentMedia;
use App\Entity\Cms\Media\ImageMedia;
use App\Entity\Cms\Media\PageImageMedia;
use App\Entity\EntityMedia\EntityMedia;
use App\Entity\Shared\Entity;
use App\Grid\Cms\MediaGrid;
use App\Repository\Cms\MediaRepository;
use App\State\Processor\MainEntityProcessor;
use App\State\Provider\MainEntityProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Metadata\Delete as DeleteResource;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Update;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Attribute\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ApiResource(
    shortName: 'media_file',
    description: 'App media',
    operations: [
        new GetCollection(
            openapi: new OpenApiOperation(
                summary : 'Get list of all media',
            ),
            normalizationContext: [
                'groups' => [
                    'media:list_read',
                ],
            ],
            provider: MainEntityProvider::class
        ),
        new Post(
            openapi: new OpenApiOperation(
                summary: 'Upload a media file',
                description: 'Upload a media file. The file will be stored in the configured media directory.',
                requestBody: new RequestBody(
                    content: new \ArrayObject([
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'file' => [
                                        'type' => 'string',
                                        'format' => 'binary',
                                    ],
                                ],
                            ],
                        ],
                    ])
                )
            ),
            normalizationContext: [
                'groups' => [
                    'media:item_read',
                ],
            ],
            denormalizationContext: [
                'groups' => [
                    'media:item_write',
                ],
            ],
            processor: MainEntityProcessor::class
        ),
        new Get(
            openapi: new OpenApiOperation(
                summary: 'Get media by id',
            ),
            normalizationContext: [
                'groups' => [
                    'media:item_read',
                ],
            ],
            provider: MainEntityProvider::class
        ),
        new Post(
            uriTemplate: '/media_files/{id}',
            openapi: new OpenApiOperation(
                summary: 'Update a media file',
                description: 'Update a media file. The file will be stored in the configured media directory.',
                requestBody: new RequestBody(
                    content: new \ArrayObject([
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'file' => [
                                        'type' => 'string',
                                        'format' => 'binary',
                                    ],
                                ],
                            ],
                        ],
                    ])
                )
            ),
            normalizationContext: [
                'groups' => [
                    'media:item_read',
                ],
            ],
            denormalizationContext: [
                'groups' => [
                    'media:item_write',
                ],
            ],
            processor: MainEntityProcessor::class
        ),
        new Delete(
            openapi: new OpenApiOperation(
                summary: 'Delete media by id',
            )
        ),
    ],
    output: MediaOutput::class,
    paginationClientItemsPerPage: true,
    paginationEnabled: true,
    paginationItemsPerPage: 25,
    security: "is_granted('ROLE_USER')"
)]
#[AsResource(
    section: 'admin',
    templatesDir: '@SyliusAdminUi/crud',
    routePrefix: '/%app_admin.path_name%',
    operations: [
        new Index(
            grid: MediaGrid::class
        ),
        new Update(),
        new DeleteResource(),
    ],
)]
#[ORM\Entity(repositoryClass: MediaRepository::class)]
#[ORM\Table(name: 'app_cms_media')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'type', type: Types::STRING)]
#[ORM\DiscriminatorMap([
    'image' => ImageMedia::class,
    'page_image' => PageImageMedia::class,
    'document' => DocumentMedia::class,
])]
#[Vich\Uploadable]
abstract class Media extends Entity
{
    #[Vich\UploadableField(mapping: 'media_files', fileNameProperty: 'filePath', size: 'size', mimeType: 'mimeType', originalName: 'originalName')]
    #[Groups(['media:item_write'])]
    protected ?File $file = null;

    #[ORM\Column(length: 255)]
    private ?string $filePath = null;

    #[ORM\Column(nullable: true)]
    private ?int $size = null;
    #[ORM\Column(length: 255)]
    private ?string $mimeType = null;

    #[ORM\Column(length: 255)]
    #[Groups(['media:item_write'])]
    private ?string $originalName = null;

    #[ORM\Column(length: 255)]
    private ?string $extension = null;

    /**
     * @var Collection<int, EntityMedia>
     */
    #[ORM\OneToMany(targetEntity: EntityMedia::class, mappedBy: 'media', cascade: ['persist'], orphanRemoval: true)]
    private Collection $entityMedias;

    public function __construct()
    {
        $this->entityMedias = new ArrayCollection();
    }

    abstract public function getType(): string;

    public static function create(string $type)
    {
        return match ($type) {
            'image' => new ImageMedia(),
            'document' => new DocumentMedia(),
            default => throw new \Exception('Invalid media type'),
        };
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): void
    {
        $this->file = $file;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): void
    {
        $this->filePath = $filePath;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): void
    {
        $this->size = $size;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): void
    {
        $this->mimeType = $mimeType;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(?string $originalName): void
    {
        $this->originalName = $originalName;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(?string $extension): void
    {
        $this->extension = $extension;
    }

    public static function getTypes(): array
    {
        return [
            'image' => ImageMedia::class,
            'document' => DocumentMedia::class,
        ];
    }

    public function getEntityMedias(): Collection
    {
        return $this->entityMedias;
    }

    public function setEntityMedias(Collection $entityMedias): void
    {
        $this->entityMedias = $entityMedias;
    }

    public function addEntityMedia(EntityMedia $entityMedia): void
    {
        if (!$this->entityMedias->contains($entityMedia)) {
            $this->entityMedias->add($entityMedia);
            $entityMedia->setMedia($this);
        }
    }

    public function removeEntityMedia(EntityMedia $entityMedia): void
    {
        if ($this->entityMedias->contains($entityMedia)) {
            $this->entityMedias->removeElement($entityMedia);
        }
    }
}
