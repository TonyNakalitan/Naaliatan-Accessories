<?php

namespace App\Controller;

use App\Entity\ActivityLog;
use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Payment;
use App\Repository\CartRepository;
use App\Repository\CharacterRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/customer')]
#[IsGranted('ROLE_CUSTOMER')]
class CustomerController extends AbstractController
{
    public function __construct(
        private ProductRepository $productRepository,
        private CharacterRepository $characterRepository,
        private OrderRepository $orderRepository,
        private CartRepository $cartRepository,
        private EntityManagerInterface $entityManager
    ) {}

    // ─── Dashboard ────────────────────────────────────────────────────────────

    #[Route('', name: 'app_customer_dashboard')]
    #[Route('/dashboard', name: 'app_customer_dashboard_alt')]
    public function dashboard(): Response
    {
        $user   = $this->getUser();
        $orders = $this->orderRepository->findBy(['customer' => $user], ['createdAt' => 'DESC'], 5);
        $cart   = $this->cartRepository->findOrCreateForUser($user);

        $totalOrders    = $this->orderRepository->count(['customer' => $user]);
        $pendingOrders  = $this->orderRepository->count(['customer' => $user, 'status' => 'pending']);
        $completedOrders = $this->orderRepository->count(['customer' => $user, 'status' => 'completed']);
        $cartItemCount  = $cart->getCartItems()->count();

        return $this->render('CustomerFolder/dashboard.html.twig', [
            'recentOrders'    => $orders,
            'totalOrders'     => $totalOrders,
            'pendingOrders'   => $pendingOrders,
            'completedOrders' => $completedOrders,
            'cartItemCount'   => $cartItemCount,
        ]);
    }

    // ─── Products / Characters ────────────────────────────────────────────────

    #[Route('/products', name: 'app_customer_products')]
    public function products(Request $request): Response
    {
        $search      = trim($request->query->get('search', ''));
        $characterId = $request->query->getInt('character', 0);
        $page        = max(1, $request->query->getInt('page', 1));
        $limit       = 12;

        $criteria = [];
        if ($characterId) {
            $character = $this->characterRepository->find($characterId);
            if ($character) {
                $criteria['character'] = $character;
            }
        }

        $allProducts = $this->productRepository->findBy($criteria, ['createdAt' => 'DESC']);

        if ($search) {
            $allProducts = array_filter($allProducts, fn($p) =>
                stripos($p->getName(), $search) !== false ||
                stripos($p->getDescription(), $search) !== false
            );
            $allProducts = array_values($allProducts);
        }

        $totalProducts = count($allProducts);
        $totalPages    = max(1, (int) ceil($totalProducts / $limit));
        $products      = array_slice($allProducts, ($page - 1) * $limit, $limit);

        return $this->render('CustomerFolder/products.html.twig', [
            'products'       => $products,
            'characters'     => $this->characterRepository->findBy([], ['name' => 'ASC']),
            'selectedChar'   => $characterId,
            'search'         => $search,
            'currentPage'    => $page,
            'totalPages'     => $totalPages,
            'totalProducts'  => $totalProducts,
        ]);
    }

    #[Route('/characters', name: 'app_customer_characters')]
    public function characters(Request $request): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 12;

        $allChars = $this->characterRepository->findBy([], ['name' => 'ASC']);
        $total = count($allChars);
        $totalPages = max(1, (int) ceil($total / $limit));
        $chars = array_slice($allChars, ($page - 1) * $limit, $limit);

        return $this->render('CustomerFolder/characters.html.twig', [
            'characters' => $chars,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalChars' => $total,
        ]);
    }

    #[Route('/characters/{id}', name: 'app_customer_character_show')]
    public function characterShow(int $id): Response
    {
        $character = $this->characterRepository->find($id);
        if (!$character) {
            $this->addFlash('error', 'Character not found.');
            return $this->redirectToRoute('app_customer_characters');
        }

        // Get products for this character
        $products = $this->productRepository->findBy(['character' => $character], ['createdAt' => 'DESC']);

        return $this->render('CustomerFolder/character_show.html.twig', [
            'character' => $character,
            'products' => $products,
        ]);
    }

    #[Route('/products/{id}', name: 'app_customer_product_show')]
    public function productShow(int $id): Response
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            $this->addFlash('error', 'Product not found.');
            return $this->redirectToRoute('app_customer_products');
        }

        $related = $this->productRepository->findBy(
            ['character' => $product->getCharacter()],
            ['createdAt' => 'DESC'],
            4
        );
        $related = array_filter($related, fn($p) => $p->getId() !== $product->getId());

        return $this->render('CustomerFolder/product_show.html.twig', [
            'product' => $product,
            'related' => array_values($related),
        ]);
    }

    // ─── Orders ───────────────────────────────────────────────────────────────

    #[Route('/orders', name: 'app_customer_orders')]
    public function orders(Request $request): Response
    {
        $user   = $this->getUser();
        $status = $request->query->get('status', 'all');

        $criteria = ['customer' => $user];
        if ($status !== 'all') {
            $criteria['status'] = $status;
        }

        $orders = $this->orderRepository->findBy($criteria, ['createdAt' => 'DESC']);

        return $this->render('CustomerFolder/orders.html.twig', [
            'orders'        => $orders,
            'activeStatus'  => $status,
        ]);
    }

    #[Route('/orders/{id}', name: 'app_customer_order_show')]
    public function orderShow(Order $order): Response
    {
        if ($order->getCustomer() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('CustomerFolder/order_show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/orders/{id}/cancel', name: 'app_customer_order_cancel', methods: ['POST'])]
    public function orderCancel(Order $order, Request $request): Response
    {
        if ($order->getCustomer() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isCsrfTokenValid('cancel_order_' . $order->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Invalid CSRF token.');
            return $this->redirectToRoute('app_customer_order_show', ['id' => $order->getId()]);
        }

        if (in_array($order->getStatus(), ['completed', 'cancelled'])) {
            $this->addFlash('error', 'This order cannot be cancelled.');
            return $this->redirectToRoute('app_customer_order_show', ['id' => $order->getId()]);
        }

        foreach ($order->getOrderItems() as $item) {
            if ($item->getProduct()) {
                $item->getProduct()->setStockQuantity($item->getProduct()->getStockQuantity() + $item->getQuantity());
            }
        }

        $order->setStatus('cancelled');
        $this->logActivity('Order Cancelled', 'Customer cancelled order #' . $order->getOrderNumber());
        $this->entityManager->flush();

        $this->addFlash('success', 'Order #' . $order->getOrderNumber() . ' has been cancelled.');
        return $this->redirectToRoute('app_customer_orders');
    }

    #[Route('/orders/{id}/pay', name: 'app_customer_order_pay', methods: ['POST'])]
    public function orderPay(Order $order, Request $request): Response
    {
        if ($order->getCustomer() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isCsrfTokenValid('pay_order_' . $order->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Invalid CSRF token.');
            return $this->redirectToRoute('app_customer_order_show', ['id' => $order->getId()]);
        }

        if ($order->getStatus() !== 'pending') {
            $this->addFlash('error', 'This order cannot be paid at this stage.');
            return $this->redirectToRoute('app_customer_order_show', ['id' => $order->getId()]);
        }

        $method = trim($request->request->get('payment_method', 'card'));

        // Create a Payment record
        $payment = new Payment();
        $payment->setOrder($order);
        $payment->setPaidBy($this->getUser());
        $payment->setMethod($method);
        $payment->setAmount($order->getTotalAmount());
        $payment->setStatus(Payment::STATUS_PENDING);

        // Move order to processing so admin/staff can review it
        $order->setStatus('processing');
        $order->setPaymentMethod($method);
        $order->setPaidAt(new \DateTimeImmutable());

        $this->entityManager->persist($payment);
        $this->logActivity(
            'Payment Submitted',
            sprintf('Customer submitted payment for order #%s (₱%s via %s)', $order->getOrderNumber(), number_format((float) $order->getTotalAmount(), 2), $method)
        );
        $this->entityManager->flush();

        $this->addFlash('success', 'Payment submitted! Your order is now being reviewed.');
        return $this->redirectToRoute('app_customer_order_show', ['id' => $order->getId()]);
    }

    // ─── Place Order ──────────────────────────────────────────────────────────

    #[Route('/order/place', name: 'app_customer_order_place', methods: ['POST'])]
    public function orderPlace(Request $request): Response
    {
        if (!$this->isCsrfTokenValid('place_order', $request->request->get('_token'))) {
            $this->addFlash('error', 'Invalid CSRF token.');
            return $this->redirectToRoute('app_customer_products');
        }

        $productId       = $request->request->getInt('product_id');
        $quantity        = max(1, $request->request->getInt('quantity', 1));
        $customerName    = trim($request->request->get('customer_name', ''));
        $customerAddress = trim($request->request->get('customer_address', ''));
        $city            = trim($request->request->get('city', ''));
        $province        = trim($request->request->get('province', ''));
        $deliveryType    = trim($request->request->get('delivery_type', ''));
        $phoneNumber     = trim($request->request->get('phone_number', ''));

        $product = $this->productRepository->find($productId);
        if (!$product) {
            $this->addFlash('error', 'Product not found.');
            return $this->redirectToRoute('app_customer_products');
        }

        if (!$customerName || !$customerAddress || !$city || !$province || !$deliveryType || !$phoneNumber) {
            $this->addFlash('error', 'Please fill in all required fields.');
            return $this->redirectToRoute('app_customer_product_show', ['id' => $productId]);
        }

        if ($product->getStockQuantity() < $quantity) {
            $this->addFlash('error', 'Insufficient stock. Only ' . $product->getStockQuantity() . ' available.');
            return $this->redirectToRoute('app_customer_product_show', ['id' => $productId]);
        }

        $order = new Order();
        $order->setCustomer($this->getUser());
        $order->setCustomerName($customerName);
        $order->setCustomerAddress($customerAddress);
        $order->setCity($city);
        $order->setProvince($province);
        $order->setDeliveryType($deliveryType);
        $order->setPhoneNumber($phoneNumber);

        $orderItem = new OrderItem();
        $orderItem->setProduct($product);
        $orderItem->setQuantity($quantity);
        $orderItem->setUnitPrice($product->getPrice());
        $subtotal = number_format((float) $product->getPrice() * $quantity, 2, '.', '');
        $orderItem->setSubtotal($subtotal);

        $order->addOrderItem($orderItem);
        $order->setTotalAmount($subtotal);
        $product->setStockQuantity($product->getStockQuantity() - $quantity);

        $this->entityManager->persist($order);
        $this->logActivity('Order Placed', sprintf(
            'Customer placed order #%s for %s (Qty: %d, Total: ₱%s)',
            $order->getOrderNumber(), $product->getName(), $quantity, $subtotal
        ));
        $this->entityManager->flush();

        $this->addFlash('success', 'Order #' . $order->getOrderNumber() . ' placed successfully!');
        return $this->redirectToRoute('app_customer_order_show', ['id' => $order->getId()]);
    }

    // ─── Cart ─────────────────────────────────────────────────────────────────

    #[Route('/cart', name: 'app_customer_cart')]
    public function cart(): Response
    {
        $cart = $this->cartRepository->findOrCreateForUser($this->getUser());

        return $this->render('CustomerFolder/cart.html.twig', [
            'cart' => $cart,
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_customer_cart_add', methods: ['POST'])]
    public function cartAdd(int $id, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('cart_add_' . $id, $request->request->get('_token'))) {
            $this->addFlash('error', 'Invalid CSRF token.');
            return $this->redirectToRoute('app_customer_product_show', ['id' => $id]);
        }

        $product = $this->productRepository->find($id);
        if (!$product) {
            $this->addFlash('error', 'Product not found.');
            return $this->redirectToRoute('app_customer_characters');
        }

        $quantity = max(1, (int) $request->request->get('quantity', 1));
        $cart = $this->cartRepository->findOrCreateForUser($this->getUser());

        $existingItem = null;
        foreach ($cart->getCartItems() as $item) {
            if ($item->getProduct()->getId() === $product->getId()) {
                $existingItem = $item;
                break;
            }
        }

        if ($existingItem) {
            $newQty = $existingItem->getQuantity() + $quantity;
            if ($newQty > $product->getStockQuantity()) {
                $this->addFlash('error', 'Not enough stock. Only ' . $product->getStockQuantity() . ' available.');
                return $this->redirectToRoute('app_customer_product_show', ['id' => $id]);
            }
            $existingItem->setQuantity($newQty);
        } else {
            if ($quantity > $product->getStockQuantity()) {
                $this->addFlash('error', 'Not enough stock. Only ' . $product->getStockQuantity() . ' available.');
                return $this->redirectToRoute('app_customer_product_show', ['id' => $id]);
            }
            $cartItem = new CartItem();
            $cartItem->setProduct($product);
            $cartItem->setQuantity($quantity);
            $cart->addCartItem($cartItem);
            $this->entityManager->persist($cartItem);
        }

        $cart->setUpdatedAt(new \DateTimeImmutable());
        $this->entityManager->flush();

        $this->addFlash('success', $product->getName() . ' added to cart!');
        return $this->redirectToRoute('app_customer_product_show', ['id' => $id]);
    }

    #[Route('/cart/{id}/remove', name: 'app_customer_cart_remove', methods: ['POST'])]
    public function cartRemove(CartItem $cartItem, Request $request): Response
    {
        if ($cartItem->getCart()->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isCsrfTokenValid('cart_remove_' . $cartItem->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Invalid CSRF token.');
            return $this->redirectToRoute('app_customer_cart');
        }

        $cart = $cartItem->getCart();
        $cart->removeCartItem($cartItem);
        $cart->setUpdatedAt(new \DateTimeImmutable());
        $this->entityManager->remove($cartItem);
        $this->entityManager->flush();

        $this->addFlash('success', 'Item removed from cart.');
        return $this->redirectToRoute('app_customer_cart');
    }

    // ─── Profile ──────────────────────────────────────────────────────────────

    #[Route('/profile', name: 'app_customer_profile')]
    public function profile(): Response
    {
        $user   = $this->getUser();
        $orders = $this->orderRepository->findBy(['customer' => $user], ['createdAt' => 'DESC'], 3);

        return $this->render('CustomerFolder/profile.html.twig', [
            'user'         => $user,
            'recentOrders' => $orders,
        ]);
    }

    #[Route('/profile/edit', name: 'app_customer_profile_edit', methods: ['GET', 'POST'])]
    public function profileEdit(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('edit_profile', $request->request->get('_token'))) {
                $this->addFlash('error', 'Invalid CSRF token.');
                return $this->redirectToRoute('app_customer_profile_edit');
            }

            $displayName = trim($request->request->get('display_name', ''));
            $bio         = trim($request->request->get('bio', ''));
            $zodiacSign  = trim($request->request->get('zodiac_sign', ''));
            $newPassword = trim($request->request->get('new_password', ''));
            $confirmPass = trim($request->request->get('confirm_password', ''));

            if ($displayName) {
                $user->setDisplayName($displayName);
            }
            $user->setBio($bio ?: null);
            $user->setZodiacSign($zodiacSign ?: null);

            // Handle profile picture upload
            $profilePicFile = $request->files->get('profile_picture');
            if ($profilePicFile) {
                $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!in_array($profilePicFile->getMimeType(), $allowedMimes)) {
                    $this->addFlash('error', 'Profile picture must be a JPG, PNG, GIF, or WebP image.');
                    return $this->redirectToRoute('app_customer_profile_edit');
                }
                if ($profilePicFile->getSize() > 2 * 1024 * 1024) {
                    $this->addFlash('error', 'Profile picture must be under 2MB.');
                    return $this->redirectToRoute('app_customer_profile_edit');
                }

                $uploadDir = $this->getParameter('kernel.project_dir') . '/public/images/profiles';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0775, true);
                }

                // Delete old picture if exists
                if ($user->getProfilePicture()) {
                    $oldFile = $uploadDir . '/' . $user->getProfilePicture();
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }

                $newFilename = uniqid('profile_', true) . '.' . $profilePicFile->guessExtension();
                $profilePicFile->move($uploadDir, $newFilename);
                $user->setProfilePicture($newFilename);
            }

            if ($newPassword) {
                if ($newPassword !== $confirmPass) {
                    $this->addFlash('error', 'Passwords do not match.');
                    return $this->redirectToRoute('app_customer_profile_edit');
                }
                if (strlen($newPassword) < 6) {
                    $this->addFlash('error', 'Password must be at least 6 characters.');
                    return $this->redirectToRoute('app_customer_profile_edit');
                }
                $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
            }

            $this->logActivity('Profile Updated', 'Customer updated their profile');
            $this->entityManager->flush();

            $this->addFlash('success', 'Profile updated successfully!');
            return $this->redirectToRoute('app_customer_profile');
        }

        return $this->render('CustomerFolder/profile_edit.html.twig', [
            'user' => $user,
        ]);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function logActivity(string $action, string $targetData): void
    {
        $user = $this->getUser();
        $log  = new ActivityLog();
        $log->setUser($user);
        $log->setUsername($user->getUsername());
        $log->setRole('Customer');
        $log->setAction($action);
        $log->setTargetData($targetData);
        $this->entityManager->persist($log);
    }
}
