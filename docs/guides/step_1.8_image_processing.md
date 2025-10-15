# Step 1.8: Image Processing Configuration

## Installation Commands
```bash
composer require knplabs/knp-gaufrette-bundle liip/imagine-bundle vich/uploader-bundle
```

## Bundle Activation

Add bundles to `config/bundles.php`:
```php
Knp\Bundle\GaufretteBundle\KnpGaufretteBundle::class => ['all' => true],
Liip\ImagineBundle\LiipImagineBundle::class => ['all' => true],
Vich\UploaderBundle\VichUploaderBundle::class => ['all' => true],
```

## Configuration Files

### KNP Gaufrette Bundle (config/packages/knp_gaufrette.yaml)
File system abstraction for handling file uploads:
```yaml
knp_gaufrette:
    adapters:
        local_adapter:
            local:
                directory: "%kernel.project_dir%/public/uploads"
                create: true
    
    filesystems:
        local_filesystem:
            adapter: local_adapter
```

### Liip Imagine Bundle (config/packages/liip_imagine.yaml)
Image processing and thumbnail generation:
```yaml
liip_imagine:
    resolvers:
        default:
            web_path: ~
    
    filter_sets:
        thumbnail_small:
            quality: 85
            filters:
                thumbnail:
                    size: [150, 150]
                    mode: outbound
                    allow_upscale: true
        
        thumbnail_medium:
            quality: 85
            filters:
                thumbnail:
                    size: [300, 300]
                    mode: outbound
                    allow_upscale: true
        
        thumbnail_large:
            quality: 85
            filters:
                thumbnail:
                    size: [600, 600]
                    mode: outbound
                    allow_upscale: true
```

### Vich Uploader Bundle (config/packages/vich_uploader.yaml)
File upload handling with entity integration:
```yaml
vich_uploader:
    db_driver: orm
    
    mappings:
        media_files:
            uri_prefix: /uploads/media
            upload_destination: '%kernel.project_dir%/public/uploads/media'
            namer: Vich\UploaderBundle\Naming\SmartUniqueNamer
```

## API Platform Integration

### UploadedFileDenormalizer
Create `src/Serializer/Denormalizer/UploadedFileDenormalizer.php` for handling file uploads in API Platform:
```php
<?php

namespace App\Serializer\Denormalizer;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class UploadedFileDenormalizer implements DenormalizerInterface
{
    public function denormalize($data, string $type, string $format = null, array $context = []): UploadedFile
    {
        return $data;
    }

    public function supportsDenormalization($data, string $type, string $format = null, array $context = []): bool
    {
        return $data instanceof UploadedFile;
    }
}
```

### MultipartDecoder
Create `src/Encoder/MultipartDecoder.php` for handling multipart form data:
```php
<?php

namespace App\Encoder;

use Symfony\Component\Serializer\Encoder\DecoderInterface;

class MultipartDecoder implements DecoderInterface
{
    public const FORMAT = 'multipart';

    public function decode(string $data, string $format, array $context = []): array
    {
        return $_POST + $_FILES;
    }

    public function supportsDecoding(string $format): bool
    {
        return self::FORMAT === $format;
    }
}
```

## Media Entity

Create a unified Media entity for all media files. The importance of this entity is to store all information in the database for fast access to file paths, dimensions, and original names through a single query, without needing to read the actual file:
```php
<?php

namespace App\Entity\Cms;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity]
#[ApiResource]
#[Vich\Uploadable]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Vich\UploadableField(mapping: 'media_files', fileNameProperty: 'fileName', size: 'fileSize', mimeType: 'fileMimeType', originalName: 'fileOriginalName')]
    private ?File $file = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fileName = null;

    #[ORM\Column(nullable: true)]
    private ?int $fileSize = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fileMimeType = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fileOriginalName = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $uploadedAt = null;

    public function __construct()
    {
        $this->uploadedAt = new \DateTimeImmutable();
    }

    // Getters and setters...
}
```

## Media Event Listener

Create `src/EventListener/Doctrine/MediaEventListener.php` to automatically extract file extensions and manage Liip Imagine paths:
```php
<?php declare(strict_types=1);

namespace App\EventListener\Doctrine;

use App\Entity\Cms\Media;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[AsDoctrineListener(event: Events::prePersist, priority: -2)]
#[AsDoctrineListener(event: Events::preUpdate, priority: -1)]
class MediaEventListener
{
    public function prePersist(LifecycleEventArgs $event): void
    {
        $entity = $event->getObject();
        if(!$entity instanceof Media) {
            return;
        }

        $file = $entity->getFile();
        if(!$file instanceof File) {
            return;
        }

        $originalFileName = $entity->getFileOriginalName();
        $entity->setExtension($this->getExtension($originalFileName));
    }

    public function preUpdate(LifecycleEventArgs $event): void
    {
        $entity = $event->getObject();
        if(!$entity instanceof Media) {
            return;
        }

        $file = $entity->getFile();
        if(!$file instanceof UploadedFile) {
            return;
        }

        $originalFileName = $entity->getFileOriginalName();
        $entity->setExtension($this->getExtension($originalFileName));
        $entity->setLiipPaths(null); // Reset cached paths when file changes
    }

    private function getExtension(?string $originalFileName): string
    {
        if(null === $originalFileName) {
            return '';
        }

        $parts = explode('.', $originalFileName);
        if(count($parts) < 2) {
            return '';
        }
        $extension = array_pop($parts);
        return strtolower($extension);
    }
}
```

## Features

### File Management
- **Gaufrette**: Filesystem abstraction layer
- **Vich Uploader**: Entity-based file upload handling
- **Liip Imagine**: On-the-fly image processing

### Image Processing
- Multiple thumbnail sizes (small, medium, large)
- Quality control and optimization
- Automatic resizing and cropping

### API Integration
- Multipart form data support
- File upload normalization
- RESTful media endpoints

## Database Migration

After creating the Media entity, generate and run database migration:
```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

## Notes
- Files stored in `public/uploads/` directory
- Smart unique naming prevents filename conflicts  
- Thumbnails generated on-demand
- Media entity provides unified file management interface
