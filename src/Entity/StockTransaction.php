<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\StockTransactionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: StockTransactionRepository::class)]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_ADMIN')"),
        new GetCollection(security: "is_granted('ROLE_ADMIN')"),
        new Post(security: "is_granted('ROLE_ADMIN')"),
        new Put(security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')")
    ],
    normalizationContext: ['groups' => ['stocktransaction:read']],
    denormalizationContext: ['groups' => ['stocktransaction:write']]
)]
class StockTransaction
{
    public const TYPE_RESTOCK = 'RESTOCK';
    public const TYPE_SALE = 'SALE';
    public const TYPE_ADJUSTMENT = 'ADJUSTMENT';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['stocktransaction:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'stockTransactions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['stocktransaction:read', 'stocktransaction:write'])]
    private ?Product $product = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    #[Groups(['stocktransaction:read'])]
    private ?User $user = null;

    #[ORM\Column(length: 20)]
    #[Groups(['stocktransaction:read', 'stocktransaction:write'])]
    private ?string $type = null;

    #[ORM\Column]
    #[Groups(['stocktransaction:read', 'stocktransaction:write'])]
    private ?int $quantity = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['stocktransaction:read', 'stocktransaction:write'])]
    private ?string $notes = null;

    // Virtual property for form adjustment type selection (not persisted)
    private ?string $adjustmentType = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['stocktransaction:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;
        return $this;
    }

    public function getAdjustmentType(): ?string
    {
        return $this->adjustmentType;
    }

    public function setAdjustmentType(?string $adjustmentType): static
    {
        $this->adjustmentType = $adjustmentType;
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

    public static function getTypeChoices(): array
    {
        return [
            self::TYPE_RESTOCK => self::TYPE_RESTOCK,
            self::TYPE_SALE => self::TYPE_SALE,
            self::TYPE_ADJUSTMENT => self::TYPE_ADJUSTMENT,
        ];
    }

    public function getTypeColor(): string
    {
        return match($this->type) {
            self::TYPE_RESTOCK => '#28a745',
            self::TYPE_SALE => '#dc3545',
            self::TYPE_ADJUSTMENT => '#ffc107',
            default => '#6c757d',
        };
    }

    public function getFormattedQuantity(): string
    {
        $prefix = match($this->type) {
            self::TYPE_RESTOCK => '+',
            self::TYPE_SALE => '-',
            self::TYPE_ADJUSTMENT => '±',
            default => '',
        };
        return $prefix . $this->quantity;
    }
}
