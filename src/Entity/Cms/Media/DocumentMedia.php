<?php

namespace App\Entity\Cms\Media;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use App\Entity\Cms\Media;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ApiResource(
    shortName: 'media_document_file',
    description: 'App media document',
    operations: [
        new Get(
            openapi: new OpenApiOperation(
                summary: 'Get media document by id',
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
class DocumentMedia extends Media
{
    #[Vich\UploadableField(mapping: 'document_files', fileNameProperty: 'filePath', size: 'size', mimeType: 'mimeType', originalName: 'originalName')]
    protected ?File $file = null;

    public function getType(): string
    {
        return 'document';
    }
}
