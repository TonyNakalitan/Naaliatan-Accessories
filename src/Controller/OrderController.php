<?php

namespace App\Controller;

use App\Entity\ActivityLog;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class OrderController extends AbstractController
{
    public function __construct(
        private OrderRepository $orderRepository,
        private ProductRepository $productRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    // Admin routes
    #[Route('/admin/orders', name: 'app_admin_order_index')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminIndex(): Response
    {
        return $this->index();
    }

    #[Route('/admin/orders/create', name: 'app_admin_order_create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminCreate(Request $request): Response
    {
        return $this->create($request, 'admin');
    }

    #[Route('/admin/orders/{id}', name: 'app_admin_order_show')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminShow(Order $order): Response
    {
        return $this->show($order);
    }

    #[Route('/admin/orders/{id}/cancel', name: 'app_admin_order_cancel', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminCancel(Order $order): Response
    {
        return $this->cancel($order, 'admin');
    }

    #[Route('/admin/orders/{id}/complete', name: 'app_admin_order_complete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminComplete(Order $order): Response
    {
        return $this->complete($order, 'admin');
    }

    #[Route('/admin/orders/{id}/delete', name: 'app_admin_order_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminDelete(Order $order): Response
    {
        return $this->delete($order, 'admin');
    }

    // Staff routes
    #[Route('/staff/orders', name: 'app_staff_order_index')]
    #[IsGranted('ROLE_STAFF')]
    public function staffIndex(): Response
    {
        return $this->index();
    }

    #[Route('/staff/orders/create', name: 'app_staff_order_create', methods: ['POST'])]
    #[IsGranted('ROLE_STAFF')]
    public function staffCreate(Request $request): Response
    {
        return $this->create($request, 'staff');
    }

    #[Route('/staff/orders/{id}', name: 'app_staff_order_show')]
    #[IsGranted('ROLE_STAFF')]
    public function staffShow(Order $order): Response
    {
        return $this->show($order);
    }

    #[Route('/staff/orders/{id}/cancel', name: 'app_staff_order_cancel', methods: ['POST'])]
    #[IsGranted('ROLE_STAFF')]
    public function staffCancel(Order $order): Response
    {
        return $this->cancel($order, 'staff');
    }

    #[Route('/staff/orders/{id}/delete', name: 'app_staff_order_delete', methods: ['POST'])]
    #[IsGranted('ROLE_STAFF')]
    public function staffDelete(Order $order): Response
    {
        return $this->delete($order, 'staff');
    }

    // Shared implementation methods
    private function index(): Response
    {
        $user = $this->getUser();
        
        // Get all orders for admin and staff, or only user's orders for customers
        if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_STAFF')) {
            $orders = $this->orderRepository->findBy([], ['createdAt' => 'DESC']);
        } else {
            $orders = $this->orderRepository->findBy(['customer' => $user], ['createdAt' => 'DESC']);
        }
        
        return $this->render('OrderFolder/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    private function create(Request $request, string $role): Response
    {
        $productId = $request->request->get('product_id');
        $quantity = (int) $request->request->get('quantity', 1);
        $customerName = $request->request->get('customer_name');
        $customerAddress = $request->request->get('customer_address');
        $city = $request->request->get('city');
        $province = $request->request->get('province');
        $deliveryType = $request->request->get('delivery_type');
        $phoneNumber = $request->request->get('phone_number');
        
        // Validate product ID
        if (!$productId) {
            $this->addFlash('error', 'Product ID is missing.');
            $routeName = $role === 'admin' ? 'app_admin_item_management_index' : 'app_staff_item_management_index';
            return $this->redirectToRoute($routeName);
        }
        
        $product = $this->productRepository->find($productId);
        
        if (!$product) {
            $this->addFlash('error', 'Product not found. Product ID: ' . $productId);
            $routeName = $role === 'admin' ? 'app_admin_item_management_index' : 'app_staff_item_management_index';
            return $this->redirectToRoute($routeName);
        }

        // Check if product has a character
        $character = $product->getCharacter();
        if (!$character) {
            $this->addFlash('error', 'Product "' . $product->getName() . '" (ID: ' . $productId . ') is not associated with a character.');
            $routeName = $role === 'admin' ? 'app_admin_item_management_index' : 'app_staff_item_management_index';
            return $this->redirectToRoute($routeName);
        }
        
        // Validate required fields
        if (!$customerName || !$customerAddress || !$city || !$province || !$deliveryType || !$phoneNumber) {
            $this->addFlash('error', 'Please fill in all customer information fields.');
            $routeName = $role === 'admin' ? 'app_admin_item_management_show' : 'app_staff_item_management_show';
            return $this->redirectToRoute($routeName, ['id' => $character->getId()]);
        }
        
        if ($product->getStockQuantity() < $quantity) {
            $this->addFlash('error', 'Insufficient stock available.');
            $routeName = $role === 'admin' ? 'app_admin_item_management_show' : 'app_staff_item_management_show';
            return $this->redirectToRoute($routeName, ['id' => $character->getId()]);
        }
        
        // Create order
        $order = new Order();
        $order->setCustomer($this->getUser());
        $order->setCustomerName($customerName);
        $order->setCustomerAddress($customerAddress);
        $order->setCity($city);
        $order->setProvince($province);
        $order->setDeliveryType($deliveryType);
        $order->setPhoneNumber($phoneNumber);
        
        // Create order item
        $orderItem = new OrderItem();
        $orderItem->setProduct($product);
        $orderItem->setQuantity($quantity);
        $orderItem->setUnitPrice($product->getPrice());
        $subtotal = bcmul($product->getPrice(), (string)$quantity, 2);
        $orderItem->setSubtotal($subtotal);
        
        $order->addOrderItem($orderItem);
        $order->setTotalAmount($subtotal);
        
        // Update stock (only if product exists)
        if ($product) {
            $product->setStockQuantity($product->getStockQuantity() - $quantity);
        }
        
        $this->entityManager->persist($order);
        
        // Log activity
        $this->logActivity(
            'Order Created',
            sprintf(
                'Order #%s created for %s (Qty: %d, Total: %s) - Customer: %s, Delivery: %s',
                $order->getOrderNumber(),
                $product->getName(),
                $quantity,
                $order->getFormattedTotalAmount(),
                $customerName,
                $deliveryType
            )
        );
        
        $this->entityManager->flush();
        
        $this->addFlash('success', 'Order placed successfully! Order #' . $order->getOrderNumber());
        
        $routeName = $role === 'admin' ? 'app_admin_order_index' : 'app_staff_order_index';
        return $this->redirectToRoute($routeName);
    }

    private function show(Order $order): Response
    {
        // Check if user can view this order
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_STAFF') && $order->getCustomer() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You cannot view this order.');
        }
        
        return $this->render('OrderFolder/show.html.twig', [
            'order' => $order,
        ]);
    }

    private function cancel(Order $order, string $role): Response
    {
        // Check if user can cancel this order
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_STAFF') && $order->getCustomer() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You cannot cancel this order.');
        }
        
        if ($order->getStatus() === 'completed' || $order->getStatus() === 'cancelled') {
            $this->addFlash('error', 'This order cannot be cancelled.');
            $routeName = $role === 'admin' ? 'app_admin_order_show' : 'app_staff_order_show';
            return $this->redirectToRoute($routeName, ['id' => $order->getId()]);
        }
        
        // Restore stock
        foreach ($order->getOrderItems() as $orderItem) {
            $product = $orderItem->getProduct();
            // Only restore stock if product still exists
            if ($product) {
                $product->setStockQuantity($product->getStockQuantity() + $orderItem->getQuantity());
            }
        }
        
        $order->setStatus('cancelled');
        
        // Log activity
        $this->logActivity(
            'Order Cancelled',
            sprintf(
                'Order #%s cancelled (Total: %s)',
                $order->getOrderNumber(),
                $order->getFormattedTotalAmount()
            )
        );
        
        $this->entityManager->flush();
        
        $this->addFlash('success', 'Order cancelled successfully.');
        
        $routeName = $role === 'admin' ? 'app_admin_order_index' : 'app_staff_order_index';
        return $this->redirectToRoute($routeName);
    }

    private function complete(Order $order, string $role): Response
    {
        if ($order->getStatus() === 'completed') {
            $this->addFlash('error', 'This order is already completed.');
            $routeName = $role === 'admin' ? 'app_admin_order_show' : 'app_staff_order_show';
            return $this->redirectToRoute($routeName, ['id' => $order->getId()]);
        }
        
        $order->setStatus('completed');
        $order->setCompletedAt(new \DateTimeImmutable());
        
        // Log activity
        $this->logActivity(
            'Order Completed',
            sprintf(
                'Order #%s marked as completed (Total: %s)',
                $order->getOrderNumber(),
                $order->getFormattedTotalAmount()
            )
        );
        
        $this->entityManager->flush();
        
        $this->addFlash('success', 'Order marked as completed.');
        
        $routeName = $role === 'admin' ? 'app_admin_order_index' : 'app_staff_order_index';
        return $this->redirectToRoute($routeName);
    }

    private function delete(Order $order, string $role): Response
    {
        // Restore stock if order is not completed
        if ($order->getStatus() !== 'completed') {
            foreach ($order->getOrderItems() as $orderItem) {
                $product = $orderItem->getProduct();
                if ($product) {
                    $product->setStockQuantity($product->getStockQuantity() + $orderItem->getQuantity());
                }
            }
        }
        
        $orderNumber = $order->getOrderNumber();
        $totalAmount = $order->getFormattedTotalAmount();
        
        // Log activity before deletion
        $this->logActivity(
            'Order Deleted',
            sprintf(
                'Order #%s deleted (Total: %s, Status: %s)',
                $orderNumber,
                $totalAmount,
                $order->getStatus()
            )
        );
        
        $this->entityManager->remove($order);
        $this->entityManager->flush();
        
        $this->addFlash('success', 'Order deleted successfully.');
        
        $routeName = $role === 'admin' ? 'app_admin_order_index' : 'app_staff_order_index';
        return $this->redirectToRoute($routeName);
    }

    private function logActivity(string $action, string $targetData): void
    {
        $user = $this->getUser();
        
        $activityLog = new ActivityLog();
        $activityLog->setUser($user);
        $activityLog->setUsername($user->getUsername());
        $activityLog->setRole($this->isGranted('ROLE_ADMIN') ? 'Admin' : 'Staff');
        $activityLog->setAction($action);
        $activityLog->setTargetData($targetData);
        
        $this->entityManager->persist($activityLog);
    }
}
