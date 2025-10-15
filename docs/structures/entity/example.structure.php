<?php

namespace App\Entity\Example;

use App\Repository\ExampleRepository;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Metadata\BulkDelete;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Delete;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Show;
use Sylius\Resource\Metadata\Update;

/**
 * Example entity structure for Sylius Resource Bundle
 * 
 * This demonstrates the basic structure of an entity with:
 * - Sylius Resource integration (#[AsResource])
 * - Doctrine ORM mapping
 * - Basic properties and methods
 * - CRUD operations configuration
 */
#[AsResource(
    section: 'admin',                    // Define which section this resource belongs to
    templatesDir: '@SyliusAdminUi/crud', // Template directory for CRUD operations
    routePrefix: '/admin',               // URL prefix for all routes
    operations: [                        // Available operations for this resource
        new Index(),                     // GET /admin/examples - List all items
        new Create(),                    // GET|POST /admin/examples/new - Create new item
        new Update(),                    // GET|POST /admin/examples/{id}/edit - Update item
        new Show(),                      // GET /admin/examples/{id} - Show single item
        new Delete(),                    // DELETE /admin/examples/{id} - Delete item
        new BulkDelete(),               // DELETE multiple items at once
    ],
)]
#[ORM\Entity(repositoryClass: ExampleRepository::class)]
#[ORM\Table(name: 'example_table')]     // Optional: specify custom table name
class Example implements ResourceInterface
{
    /**
     * Primary key - auto-incrementing integer
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * Example string field with length constraint
     * This could represent a title, name, or any short text
     */
    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private ?string $title = null;

    /**
     * Example text field for longer content
     * This could represent description, content, or any long text
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    /**
     * Example boolean field with default value
     * This could represent status, visibility, or any on/off state
     */
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isActive = false;

    /**
     * Example datetime field for timestamps
     * Automatically set when entity is created
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * Constructor - initialize default values
     */
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // Getter and setter methods

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Optional: String representation of the entity
     * Useful for debugging and form selections
     */
    public function __toString(): string
    {
        return $this->title ?? 'Example #' . $this->id;
    }
}

/**
 * COMMON DOCTRINE COLUMN TYPES:
 * 
 * #[ORM\Column(type: 'string', length: 255)]      // VARCHAR(255)
 * #[ORM\Column(type: 'text')]                     // TEXT
 * #[ORM\Column(type: 'integer')]                  // INT
 * #[ORM\Column(type: 'bigint')]                   // BIGINT
 * #[ORM\Column(type: 'smallint')]                 // SMALLINT
 * #[ORM\Column(type: 'boolean')]                  // BOOLEAN/TINYINT
 * #[ORM\Column(type: 'decimal', precision: 10, scale: 2)] // DECIMAL(10,2)
 * #[ORM\Column(type: 'float')]                    // FLOAT
 * #[ORM\Column(type: 'datetime')]                 // DATETIME
 * #[ORM\Column(type: 'datetime_immutable')]       // DATETIME (immutable)
 * #[ORM\Column(type: 'date')]                     // DATE
 * #[ORM\Column(type: 'date_immutable')]           // DATE (immutable)
 * #[ORM\Column(type: 'time')]                     // TIME
 * #[ORM\Column(type: 'json')]                     // JSON
 * 
 * COMMON COLUMN OPTIONS:
 * 
 * nullable: true|false                            // Allow NULL values
 * unique: true|false                              // Unique constraint
 * options: ['default' => 'value']                 // Default value
 * columnDefinition: 'CUSTOM SQL'                  // Custom SQL definition
 * 
 * RELATIONSHIPS:
 * 
 * #[ORM\OneToOne(targetEntity: RelatedEntity::class)]
 * #[ORM\OneToMany(targetEntity: RelatedEntity::class, mappedBy: 'example')]
 * #[ORM\ManyToOne(targetEntity: RelatedEntity::class, inversedBy: 'examples')]
 * #[ORM\ManyToMany(targetEntity: RelatedEntity::class)]
 */