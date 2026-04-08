<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\CharacterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CharacterRepository::class)]
#[ORM\Table(name: 'game_character')]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(security: "is_granted('ROLE_ADMIN')"),
        new Put(security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')")
    ],
    normalizationContext: ['groups' => ['character:read']],
    denormalizationContext: ['groups' => ['character:write']]
)]
class Character
{
    public const ALIGNMENT_GOOD = 'Good';
    public const ALIGNMENT_EVIL = 'Evil';
    public const ALIGNMENT_NEUTRAL = 'Neutral';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['character:read', 'product:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['character:read', 'character:write', 'product:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 100, unique: true)]
    #[Groups(['character:read', 'character:write'])]
    private ?string $creator = null;

    #[ORM\Column(type: 'text')]
    #[Groups(['character:read', 'character:write'])]
    private ?string $description = null;

    #[ORM\Column(length: 20)]
    #[Groups(['character:read', 'character:write'])]
    private ?string $alignment = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['character:read', 'character:write'])]
    private ?string $image = null;

    #[ORM\Column(length: 7)]
    #[Groups(['character:read', 'character:write'])]
    private ?string $colorCode = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['character:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    #[Groups(['character:read'])]
    private ?User $createdBy = null;

    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'character')]
    private Collection $products;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getCreator(): ?string
    {
        return $this->creator;
    }

    public function setCreator(string $creator): static
    {
        $this->creator = $creator;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getAlignment(): ?string
    {
        return $this->alignment;
    }

    public function setAlignment(string $alignment): static
    {
        $this->alignment = $alignment;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function getColorCode(): ?string
    {
        return $this->colorCode;
    }

    public function setColorCode(string $colorCode): static
    {
        $this->colorCode = $colorCode;
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

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setCharacter($this);
        }
        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            if ($product->getCharacter() === $this) {
                $product->setCharacter(null);
            }
        }
        return $this;
    }

    public static function getAlignmentChoices(): array
    {
        return [
            self::ALIGNMENT_GOOD => self::ALIGNMENT_GOOD,
            self::ALIGNMENT_EVIL => self::ALIGNMENT_EVIL,
            self::ALIGNMENT_NEUTRAL => self::ALIGNMENT_NEUTRAL,
        ];
    }

    public function getAlignmentColor(): string
    {
        return match($this->alignment) {
            self::ALIGNMENT_GOOD => '#28a745',
            self::ALIGNMENT_EVIL => '#dc3545',
            self::ALIGNMENT_NEUTRAL => '#6c757d',
            default => '#6c757d',
        };
    }
}
