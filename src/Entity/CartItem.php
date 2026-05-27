<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\CartItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CartItemRepository::class)]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_USER') and (object.getCart().getUser() == user or is_granted('ROLE_ADMIN'))"),
        new GetCollection(security: "is_granted('ROLE_ADMIN')"),
        new Post(security: "is_granted('ROLE_USER')"),
        new Put(security: "is_granted('ROLE_USER') and (object.getCart().getUser() == user or is_granted('ROLE_ADMIN'))"),
        new Delete(security: "is_granted('ROLE_USER') and (object.getCart().getUser() == user or is_granted('ROLE_ADMIN'))")
    ],
    normalizationContext: ['groups' => ['cartitem:read']],
    denormalizationContext: ['groups' => ['cartitem:write']]
)]
class CartItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['cartitem:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'cartItems')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['cartitem:read', 'cartitem:write'])]
    private ?Cart $cart = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['cartitem:read', 'cartitem:write'])]
    private ?Product $product = null;

    #[ORM\Column]
    #[Groups(['cartitem:read', 'cartitem:write'])]
    private ?int $quantity = null;

    #[ORM\Column]
    #[Groups(['cartitem:read'])]
    private ?\DateTimeImmutable $addedAt = null;

    public function __construct()
    {
        $this->addedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(?Cart $cart): static
    {
        $this->cart = $cart;

        return $this;
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

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getAddedAt(): ?\DateTimeImmutable
    {
        return $this->addedAt;
    }

    public function setAddedAt(\DateTimeImmutable $addedAt): static
    {
        $this->addedAt = $addedAt;

        return $this;
    }

    public function getSubtotal(): float
    {
        if ($this->product === null || $this->quantity === null) {
            return 0.0;
        }

        return (float) $this->product->getPrice() * $this->quantity;
    }

    public function getFormattedSubtotal(): string
    {
        return '₱' . number_format($this->getSubtotal(), 2);
    }
}
