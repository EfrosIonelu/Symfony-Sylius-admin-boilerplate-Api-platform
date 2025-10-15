<?php

namespace App\Entity\Cms\Media;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use App\Entity\Cms\Media;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ApiResource(
    shortName: 'media_image_file',
    description: 'App media image',
    operations: [
        new Get(
            openapi: new OpenApiOperation(
                summary: 'Get media image by id',
            ),
            normalizationContext: [
                'groups' => [
                    'media:item_read',
                ],
            ]
        ),
    ],
    paginationClientItemsPerPage: true,
    paginationEnabled: true,
    paginationItemsPerPage: 25,
    security: "is_granted('ROLE_USER')"
)]
#[ORM\Entity]
class ImageMedia extends Media
{
    #[Vich\UploadableField(mapping: 'image_files', fileNameProperty: 'filePath', size: 'size', mimeType: 'mimeType', originalName: 'originalName')]
    protected ?File $file = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $liipPaths = null;

    public function getType(): string
    {
        return 'image';
    }

    public function getLiipPaths(): ?array
    {
        return $this->liipPaths;
    }

    public function setLiipPaths(?array $liipPaths): void
    {
        $this->liipPaths = $liipPaths;
    }
}
