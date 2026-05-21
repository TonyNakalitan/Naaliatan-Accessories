<?php

namespace App\Controller\Api;

use App\Entity\ActivityLog;
use App\Entity\Cart;
use App\Entity\CartItem;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/cart')]
class ApiCartController extends AbstractController
{
    public function __construct(
        private CartRepository $cartRepository,
        private ProductRepository $productRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('', name: 'api_cart_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['id' => null, 'items' => [], 'totalItems' => 0, 'totalPrice' => 0]);
        }

        $cart = $this->cartRepository->findOrCreateForUser($user);

        return $this->json($this->serializeCart($cart));
    }

    #[Route('/add/{id}', name: 'api_cart_add', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function add(int $id, Request $request): JsonResponse
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            return $this->json(['message' => 'Product not found.'], 404);
        }

        $data     = json_decode($request->getContent(), true) ?? [];
        $quantity = (int) ($data['quantity'] ?? 1);

        if ($quantity < 1) {
            return $this->json(['message' => 'Quantity must be at least 1.'], 400);
        }

        $cart        = $this->cartRepository->findOrCreateForUser($this->getUser());
        $existingItem = null;

        foreach ($cart->getCartItems() as $item) {
            if ($item->getProduct()->getId() === $product->getId()) {
                $existingItem = $item;
                break;
            }
        }

        if ($existingItem) {
            $newQuantity = $existingItem->getQuantity() + $quantity;
            if ($newQuantity > $product->getStockQuantity()) {
                return $this->json(['message' => 'Not enough stock. Available: ' . $product->getStockQuantity()], 422);
            }
            $existingItem->setQuantity($newQuantity);
            $description = 'Updated cart: ' . $product->getName() . ' (Qty: +' . $quantity . ', Total: ' . $newQuantity . ')';
        } else {
            if ($quantity > $product->getStockQuantity()) {
                return $this->json(['message' => 'Not enough stock. Available: ' . $product->getStockQuantity()], 422);
            }
            $cartItem = new CartItem();
            $cartItem->setProduct($product);
            $cartItem->setQuantity($quantity);
            $cart->addCartItem($cartItem);
            $description = 'Added to cart: ' . $product->getName() . ' (Qty: ' . $quantity . ')';
        }

        $cart->setUpdatedAt(new \DateTimeImmutable());
        $this->logActivity('Cart', $description);
        $this->entityManager->flush();

        return $this->json($this->serializeCart($cart));
    }

    #[Route('/update/{id}', name: 'api_cart_update', methods: ['PATCH'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function update(CartItem $cartItem, Request $request): JsonResponse
    {
        if ($cartItem->getCart()->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            return $this->json(['message' => 'Access denied.'], 403);
        }

        $data     = json_decode($request->getContent(), true) ?? [];
        $quantity = (int) ($data['quantity'] ?? 0);

        if ($quantity <= 0) {
            return $this->redirectToRoute('api_cart_remove', ['id' => $cartItem->getId()]);
        }

        if ($quantity > $cartItem->getProduct()->getStockQuantity()) {
            return $this->json(['message' => 'Not enough stock. Available: ' . $cartItem->getProduct()->getStockQuantity()], 422);
        }

        $old = $cartItem->getQuantity();
        $cartItem->setQuantity($quantity);
        $cartItem->getCart()->setUpdatedAt(new \DateTimeImmutable());
        $this->logActivity('Cart', 'Updated cart: ' . $cartItem->getProduct()->getName() . ' (From: ' . $old . ' to: ' . $quantity . ')');
        $this->entityManager->flush();

        return $this->json($this->serializeCart($cartItem->getCart()));
    }

    #[Route('/remove/{id}', name: 'api_cart_remove', methods: ['DELETE'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function remove(CartItem $cartItem): JsonResponse
    {
        if ($cartItem->getCart()->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            return $this->json(['message' => 'Access denied.'], 403);
        }

        $productName = $cartItem->getProduct()->getName();
        $quantity    = $cartItem->getQuantity();
        $cart        = $cartItem->getCart();

        $cart->removeCartItem($cartItem);
        $cart->setUpdatedAt(new \DateTimeImmutable());
        $this->entityManager->remove($cartItem);
        $this->logActivity('Cart', 'Removed from cart: ' . $productName . ' (Qty: ' . $quantity . ')');
        $this->entityManager->flush();

        return $this->json($this->serializeCart($cart));
    }

    #[Route('/clear', name: 'api_cart_clear', methods: ['DELETE'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function clear(): JsonResponse
    {
        $cart      = $this->cartRepository->findOrCreateForUser($this->getUser());
        $itemCount = $cart->getCartItems()->count();

        foreach ($cart->getCartItems() as $item) {
            $this->entityManager->remove($item);
        }

        $cart->setUpdatedAt(new \DateTimeImmutable());
        $this->logActivity('Cart', 'Cleared cart (' . $itemCount . ' items removed)');
        $this->entityManager->flush();

        return $this->json($this->serializeCart($cart));
    }

    private function serializeCart(Cart $cart): array
    {
        return [
            'id'        => $cart->getId(),
            'updatedAt' => $cart->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
            'items'     => array_map(fn(CartItem $i) => [
                'id'        => $i->getId(),
                'product'   => [
                    'id'    => $i->getProduct()?->getId(),
                    'name'  => $i->getProduct()?->getName(),
                    'price' => $i->getProduct()?->getPrice(),
                ],
                'quantity'  => $i->getQuantity(),
                'subtotal'  => $i->getProduct() ? $i->getSubtotal() : 0,
                'addedAt'   => $i->getAddedAt()?->format(\DateTimeInterface::ATOM),
            ], $cart->getCartItems()->toArray()),
            'totalItems' => $cart->getTotalItems(),
            'totalPrice' => $cart->getTotalPrice(),
        ];
    }

    private function logActivity(string $action, string $description): void
    {
        $user = $this->getUser();
        $log  = new ActivityLog();
        $log->setUser($user);
        $log->setUsername($user->getUsername());
        $log->setRole($this->isGranted('ROLE_ADMIN') ? 'Admin' : 'User');
        $log->setAction($action);
        $log->setTargetData($description);
        $this->entityManager->persist($log);
    }
}
