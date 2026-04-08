<?php

namespace App\Controller;

use App\Repository\CharacterRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Entity\ActivityLog;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ItemManagementController extends AbstractController
{
    public function __construct(
        private CharacterRepository $characterRepository,
        private ProductRepository $productRepository,
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    // Admin routes
    #[Route('/admin/items', name: 'app_admin_item_management_index')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminIndex(Request $request): Response
    {
        return $this->index($request);
    }

    #[Route('/admin/items/character/{id}', name: 'app_admin_item_management_show')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminShow(int $id): Response
    {
        return $this->show($id, 'admin');
    }

    #[Route('/admin/buy-now', name: 'app_admin_buy_now', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminBuyNow(Request $request): Response
    {
        return $this->buyNow($request);
    }

    // Staff routes
    #[Route('/staff/items', name: 'app_staff_item_management_index')]
    #[IsGranted('ROLE_STAFF')]
    public function staffIndex(Request $request): Response
    {
        return $this->index($request);
    }

    #[Route('/staff/items/character/{id}', name: 'app_staff_item_management_show')]
    #[IsGranted('ROLE_STAFF')]
    public function staffShow(int $id): Response
    {
        return $this->show($id, 'staff');
    }

    #[Route('/staff/buy-now', name: 'app_staff_buy_now', methods: ['POST'])]
    #[IsGranted('ROLE_STAFF')]
    public function staffBuyNow(Request $request): Response
    {
        return $this->buyNow($request);
    }

    // Shared implementation methods
    private function index(Request $request): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 27;
        
        $totalCharacters = $this->characterRepository->count([]);
        $totalPages = (int) ceil($totalCharacters / $limit);
        $offset = ($page - 1) * $limit;
        
        $characters = $this->characterRepository->findBy(
            [],
            ['name' => 'ASC'],
            $limit,
            $offset
        );
        $products = $this->productRepository->findBy([], ['name' => 'ASC']);
        
        // Get alignment counts
        $goodCount = $this->characterRepository->count(['alignment' => 'Good']);
        $neutralCount = $this->characterRepository->count(['alignment' => 'Neutral']);
        $evilCount = $this->characterRepository->count(['alignment' => 'Evil']);
        
        return $this->render('ItemsFolder/index.html.twig', [
            'characters' => $characters,
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalCharacters' => $totalCharacters,
            'goodCount' => $goodCount,
            'neutralCount' => $neutralCount,
            'evilCount' => $evilCount,
        ]);
    }

    private function show(int $id, string $role): Response
    {
        $character = $this->characterRepository->find($id);
        
        if (!$character) {
            $this->addFlash('error', 'Character not found. The character may have been deleted.');
            $routeName = $role === 'admin' ? 'app_admin_item_management_index' : 'app_staff_item_management_index';
            return $this->redirectToRoute($routeName);
        }

        $products = $this->productRepository->findBy(['character' => $character], ['name' => 'ASC']);
        
        return $this->render('ItemsFolder/show.html.twig', [
            'character' => $character,
            'products' => $products,
        ]);
    }

    private function buyNow(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return new JsonResponse(['error' => 'Invalid request data'], 400);
            }
            
            $productId = $data['productId'] ?? null;
            $quantity = $data['quantity'] ?? 1;
            $customerData = $data['customer'] ?? [];
            
            // Validate required fields
            if (!$productId || !isset($customerData['name']) || !isset($customerData['email']) || 
                !isset($customerData['phone']) || !isset($customerData['address'])) {
                return new JsonResponse(['error' => 'Missing required fields'], 400);
            }
            
            // Find product
            $product = $this->productRepository->find($productId);
            if (!$product) {
                return new JsonResponse(['error' => 'Product not found'], 404);
            }
            
            // Check stock availability
            if ($quantity > $product->getStockQuantity()) {
                return new JsonResponse([
                    'error' => 'Insufficient stock',
                    'available' => $product->getStockQuantity()
                ], 400);
            }
            
            // Get the current logged-in user (staff/admin) who is making the sale
            $currentUser = $this->getUser();
            if (!$currentUser) {
                return new JsonResponse(['error' => 'User not authenticated'], 401);
            }
            
            // Parse address to extract city and province (simplified - take last two parts as city/province)
            $addressParts = explode(',', $customerData['address']);
            $city = isset($addressParts[count($addressParts) - 2]) ? trim($addressParts[count($addressParts) - 2]) : 'N/A';
            $province = isset($addressParts[count($addressParts) - 1]) ? trim($addressParts[count($addressParts) - 1]) : 'N/A';
            
            // Calculate totals
            $subtotal = bcmul($product->getPrice(), (string)$quantity, 2);
            
            // Create the order
            $order = new Order();
            $order->setCustomer($currentUser); // The logged-in user making the sale
            $order->setCustomerName($customerData['name']);
            $order->setCustomerAddress($customerData['address']);
            $order->setCity($city);
            $order->setProvince($province);
            $order->setDeliveryType('standard'); // Default delivery type
            $order->setPhoneNumber($customerData['phone']);
            $order->setTotalAmount($subtotal);
            $order->setStatus('pending');
            $order->setCreatedAt(new \DateTimeImmutable());
            
            // Create order item
            $orderItem = new OrderItem();
            $orderItem->setOrderRef($order);
            $orderItem->setProduct($product);
            $orderItem->setQuantity($quantity);
            $orderItem->setUnitPrice($product->getPrice());
            $orderItem->setSubtotal($subtotal);
            
            // Add order item to order
            $order->addOrderItem($orderItem);
            
            // Update product stock
            $product->setStockQuantity($product->getStockQuantity() - $quantity);
            
            // Persist all entities
            $this->entityManager->persist($order);
            $this->entityManager->persist($product);
            $this->entityManager->flush();
            
            // Log activity
            $this->logActivity(
                'Order Created',
                sprintf(
                    'Order #%s created for %s (Qty: %d, Total: %s) - Customer: %s, Delivery: standard',
                    $order->getOrderNumber(),
                    $product->getName(),
                    $quantity,
                    $order->getFormattedTotalAmount(),
                    $customerData['name']
                )
            );
            
            return new JsonResponse([
                'success' => true,
                'message' => 'Order placed successfully',
                'orderId' => $order->getId(),
                'orderNumber' => $order->getOrderNumber(),
                'productId' => $product->getId(),
                'quantity' => $quantity
            ]);
            
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    private function logActivity(string $action, string $targetData): void
    {
        $user = $this->getUser();
        if (!$user) {
            return;
        }
        
        $activityLog = new ActivityLog();
        $activityLog->setUser($user);
        $activityLog->setUsername($user->getUsername());
        $activityLog->setRole($this->isGranted('ROLE_ADMIN') ? 'Admin' : 'Staff');
        $activityLog->setAction($action);
        $activityLog->setTargetData($targetData);
        
        $this->entityManager->persist($activityLog);
        $this->entityManager->flush();
    }
}
