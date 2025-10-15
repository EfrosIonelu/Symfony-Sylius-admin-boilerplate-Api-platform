<?php

namespace App\Entity\Cms\Media;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ApiResource(
    shortName: 'media_page_image_file',
    description: 'App media page image',
    operations: [
        new Get(
            openapi: new OpenApiOperation(
                summary: 'Get media page image by id',
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
class PageImageMedia extends ImageMedia
{
    #[Vich\UploadableField(mapping: 'page_image_files', fileNameProperty: 'filePath', size: 'size', mimeType: 'mimeType', originalName: 'originalName')]
    protected ?File $file = null;

    public function getType(): string
    {
        return 'page_image';
    }
}
