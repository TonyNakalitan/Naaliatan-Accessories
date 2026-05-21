<?php

namespace App\Controller;

use App\Entity\ActivityLog;
use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CartController extends AbstractController
{
    // Admin routes
    #[Route('/admin/cart', name: 'app_admin_cart_index')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminIndex(CartRepository $cartRepository): Response
    {
        return $this->index($cartRepository);
    }

    #[Route('/admin/cart/add/{id}', name: 'app_admin_cart_add', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminAdd(Product $product, Request $request, CartRepository $cartRepository, EntityManagerInterface $entityManager): Response
    {
        return $this->add($product, $request, $cartRepository, $entityManager, 'admin');
    }

    #[Route('/admin/cart/{id}/update', name: 'app_admin_cart_update', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminUpdate(CartItem $cartItem, Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->update($cartItem, $request, $entityManager);
    }

    #[Route('/admin/cart/{id}/remove', name: 'app_admin_cart_remove', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminRemove(CartItem $cartItem, EntityManagerInterface $entityManager): Response
    {
        return $this->remove($cartItem, $entityManager);
    }

    #[Route('/admin/cart/clear', name: 'app_admin_cart_clear', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminClear(CartRepository $cartRepository, EntityManagerInterface $entityManager): Response
    {
        return $this->clear($cartRepository, $entityManager);
    }

    // Staff routes
    #[Route('/staff/cart', name: 'app_staff_cart_index')]
    #[IsGranted('ROLE_STAFF')]
    public function staffIndex(CartRepository $cartRepository): Response
    {
        return $this->index($cartRepository);
    }

    #[Route('/staff/cart/add/{id}', name: 'app_staff_cart_add', methods: ['POST'])]
    #[IsGranted('ROLE_STAFF')]
    public function staffAdd(Product $product, Request $request, CartRepository $cartRepository, EntityManagerInterface $entityManager): Response
    {
        return $this->add($product, $request, $cartRepository, $entityManager, 'staff');
    }

    #[Route('/staff/cart/{id}/update', name: 'app_staff_cart_update', methods: ['POST'])]
    #[IsGranted('ROLE_STAFF')]
    public function staffUpdate(CartItem $cartItem, Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->update($cartItem, $request, $entityManager);
    }

    #[Route('/staff/cart/{id}/remove', name: 'app_staff_cart_remove', methods: ['POST'])]
    #[IsGranted('ROLE_STAFF')]
    public function staffRemove(CartItem $cartItem, EntityManagerInterface $entityManager): Response
    {
        return $this->remove($cartItem, $entityManager);
    }

    #[Route('/staff/cart/clear', name: 'app_staff_cart_clear', methods: ['POST'])]
    #[IsGranted('ROLE_STAFF')]
    public function staffClear(CartRepository $cartRepository, EntityManagerInterface $entityManager): Response
    {
        return $this->clear($cartRepository, $entityManager);
    }

    // Shared implementation methods
    private function index(CartRepository $cartRepository): Response
    {
        $user = $this->getUser();
        $cart = $cartRepository->findOrCreateForUser($user);

        return $this->render('CartFolder/index.html.twig', [
            'cart' => $cart,
        ]);
    }

    private function add(Product $product, Request $request, CartRepository $cartRepository, EntityManagerInterface $entityManager, string $role): Response
    {
        $user = $this->getUser();
        $cart = $cartRepository->findOrCreateForUser($user);

        $quantity = (int) $request->request->get('quantity', 1);

        // Check if product already in cart
        $existingItem = null;
        foreach ($cart->getCartItems() as $item) {
            if ($item->getProduct()->getId() === $product->getId()) {
                $existingItem = $item;
                break;
            }
        }

        if ($existingItem) {
            // Update quantity
            $newQuantity = $existingItem->getQuantity() + $quantity;
            
            // Check stock availability
            if ($newQuantity > $product->getStockQuantity()) {
                $this->addFlash('error', 'Not enough stock available. Only ' . $product->getStockQuantity() . ' items in stock.');
                $routeName = $role === 'admin' ? 'app_admin_item_management_show' : 'app_staff_item_management_show';
                return $this->redirectToRoute($routeName, ['id' => $product->getCharacter()->getId()]);
            }
            
            $existingItem->setQuantity($newQuantity);
            $actionDescription = 'Updated cart: ' . $product->getName() . ' (Qty: ' . $quantity . ' added, Total: ' . $newQuantity . ')';
        } else {
            // Check stock availability
            if ($quantity > $product->getStockQuantity()) {
                $this->addFlash('error', 'Not enough stock available. Only ' . $product->getStockQuantity() . ' items in stock.');
                $routeName = $role === 'admin' ? 'app_admin_item_management_show' : 'app_staff_item_management_show';
                return $this->redirectToRoute($routeName, ['id' => $product->getCharacter()->getId()]);
            }
            
            // Create new cart item
            $cartItem = new CartItem();
            $cartItem->setProduct($product);
            $cartItem->setQuantity($quantity);
            $cart->addCartItem($cartItem);
            $actionDescription = 'Added to cart: ' . $product->getName() . ' (Qty: ' . $quantity . ')';
        }

        $cart->setUpdatedAt(new \DateTimeImmutable());
        $entityManager->flush();

        // Log activity
        $this->logActivity($user, 'Cart', $actionDescription, $entityManager);

        $this->addFlash('success', $product->getName() . ' added to cart!');

        $routeName = $role === 'admin' ? 'app_admin_item_management_show' : 'app_staff_item_management_show';
        return $this->redirectToRoute($routeName, ['id' => $product->getCharacter()->getId()]);
    }

    private function update(CartItem $cartItem, Request $request, EntityManagerInterface $entityManager): Response
    {
        $quantity = (int) $request->request->get('quantity', 1);

        if ($quantity <= 0) {
            $routeName = $this->isGranted('ROLE_ADMIN') ? 'app_admin_cart_remove' : 'app_staff_cart_remove';
            return $this->redirectToRoute($routeName, ['id' => $cartItem->getId()]);
        }

        // Check stock availability
        if ($quantity > $cartItem->getProduct()->getStockQuantity()) {
            $this->addFlash('error', 'Not enough stock available. Only ' . $cartItem->getProduct()->getStockQuantity() . ' items in stock.');
            $routeName = $this->isGranted('ROLE_ADMIN') ? 'app_admin_cart_index' : 'app_staff_cart_index';
            return $this->redirectToRoute($routeName);
        }

        $oldQuantity = $cartItem->getQuantity();
        $cartItem->setQuantity($quantity);
        $cartItem->getCart()->setUpdatedAt(new \DateTimeImmutable());
        $entityManager->flush();

        // Log activity
        $this->logActivity(
            $this->getUser(),
            'Cart',
            'Updated cart quantity: ' . $cartItem->getProduct()->getName() . ' (From: ' . $oldQuantity . ' to: ' . $quantity . ')',
            $entityManager
        );

        $this->addFlash('success', 'Cart updated!');

        $routeName = $this->isGranted('ROLE_ADMIN') ? 'app_admin_cart_index' : 'app_staff_cart_index';
        return $this->redirectToRoute($routeName);
    }

    private function remove(CartItem $cartItem, EntityManagerInterface $entityManager): Response
    {
        $productName = $cartItem->getProduct()->getName();
        $quantity = $cartItem->getQuantity();
        
        $cart = $cartItem->getCart();
        $cart->removeCartItem($cartItem);
        $cart->setUpdatedAt(new \DateTimeImmutable());
        
        $entityManager->remove($cartItem);
        $entityManager->flush();

        // Log activity
        $this->logActivity(
            $this->getUser(),
            'Cart',
            'Removed from cart: ' . $productName . ' (Qty: ' . $quantity . ')',
            $entityManager
        );

        $this->addFlash('success', 'Item removed from cart!');

        $routeName = $this->isGranted('ROLE_ADMIN') ? 'app_admin_cart_index' : 'app_staff_cart_index';
        return $this->redirectToRoute($routeName);
    }

    private function clear(CartRepository $cartRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $cart = $cartRepository->findOrCreateForUser($user);

        $itemCount = $cart->getCartItems()->count();

        foreach ($cart->getCartItems() as $item) {
            $entityManager->remove($item);
        }

        $entityManager->flush();

        // Log activity
        $this->logActivity($user, 'Cart', 'Cleared cart (' . $itemCount . ' items removed)', $entityManager);

        $this->addFlash('success', 'Cart cleared!');

        $routeName = $this->isGranted('ROLE_ADMIN') ? 'app_admin_cart_index' : 'app_staff_cart_index';
        return $this->redirectToRoute($routeName);
    }

    private function logActivity($user, string $action, string $description, EntityManagerInterface $entityManager): void
    {
        $activityLog = new ActivityLog();
        $activityLog->setUser($user);
        $activityLog->setUsername($user->getUsername());
        $activityLog->setRole($this->isGranted('ROLE_ADMIN') ? 'Admin' : 'Staff');
        $activityLog->setAction($action);
        $activityLog->setTargetData($description);

        $entityManager->persist($activityLog);
        $entityManager->flush();
    }
}
