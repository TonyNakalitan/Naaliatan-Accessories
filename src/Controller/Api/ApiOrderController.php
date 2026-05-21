<?php

namespace App\Controller\Api;

use App\Entity\ActivityLog;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/orders')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class ApiOrderController extends AbstractController
{
    public function __construct(
        private OrderRepository $orderRepository,
        private ProductRepository $productRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('', name: 'api_order_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $user = $this->getUser();

        $orders = $this->isGranted('ROLE_ADMIN')
            ? $this->orderRepository->findBy([], ['createdAt' => 'DESC'])
            : $this->orderRepository->findBy(['customer' => $user], ['createdAt' => 'DESC']);

        return $this->json(array_map(fn(Order $o) => $this->serializeOrder($o), $orders));
    }

    #[Route('/{id}', name: 'api_order_show', methods: ['GET'])]
    public function show(Order $order): JsonResponse
    {
        $user = $this->getUser();

        if (!$this->isGranted('ROLE_ADMIN') && $order->getCustomer() !== $user) {
            return $this->json(['message' => 'Access denied.'], 403);
        }

        return $this->json($this->serializeOrder($order));
    }

    #[Route('', name: 'api_order_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['message' => 'Invalid JSON body.'], 400);
        }

        $productId      = $data['product_id'] ?? null;
        $quantity       = (int) ($data['quantity'] ?? 1);
        $customerName   = trim($data['customer_name'] ?? '');
        $customerAddress = trim($data['customer_address'] ?? '');
        $city           = trim($data['city'] ?? '');
        $province       = trim($data['province'] ?? '');
        $deliveryType   = trim($data['delivery_type'] ?? '');
        $phoneNumber    = trim($data['phone_number'] ?? '');

        if (!$productId) {
            return $this->json(['message' => 'product_id is required.'], 400);
        }

        $product = $this->productRepository->find($productId);
        if (!$product) {
            return $this->json(['message' => 'Product not found.'], 404);
        }

        if (!$product->getCharacter()) {
            return $this->json(['message' => 'Product is not associated with a character.'], 422);
        }

        if (!$customerName || !$customerAddress || !$city || !$province || !$deliveryType || !$phoneNumber) {
            return $this->json(['message' => 'All customer fields are required.'], 400);
        }

        if ($product->getStockQuantity() < $quantity) {
            return $this->json(['message' => 'Insufficient stock.'], 422);
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
        $subtotal = bcmul($product->getPrice(), (string) $quantity, 2);
        $orderItem->setSubtotal($subtotal);

        $order->addOrderItem($orderItem);
        $order->setTotalAmount($subtotal);

        $product->setStockQuantity($product->getStockQuantity() - $quantity);

        $this->entityManager->persist($order);
        $this->logActivity('Order Created', sprintf(
            'Order #%s created for %s (Qty: %d, Total: ₱%s)',
            $order->getOrderNumber(), $product->getName(), $quantity, $subtotal
        ));
        $this->entityManager->flush();

        return $this->json($this->serializeOrder($order), 201);
    }

    #[Route('/{id}/cancel', name: 'api_order_cancel', methods: ['PATCH'])]
    public function cancel(Order $order): JsonResponse
    {
        $user = $this->getUser();

        if (!$this->isGranted('ROLE_ADMIN') && $order->getCustomer() !== $user) {
            return $this->json(['message' => 'Access denied.'], 403);
        }

        if (in_array($order->getStatus(), ['completed', 'cancelled'])) {
            return $this->json(['message' => 'Order cannot be cancelled.'], 422);
        }

        foreach ($order->getOrderItems() as $item) {
            if ($item->getProduct()) {
                $item->getProduct()->setStockQuantity($item->getProduct()->getStockQuantity() + $item->getQuantity());
            }
        }

        $order->setStatus('cancelled');
        $this->logActivity('Order Cancelled', sprintf('Order #%s cancelled.', $order->getOrderNumber()));
        $this->entityManager->flush();

        return $this->json($this->serializeOrder($order));
    }

    #[Route('/{id}/complete', name: 'api_order_complete', methods: ['PATCH'])]
    #[IsGranted('ROLE_ADMIN')]
    public function complete(Order $order): JsonResponse
    {
        if ($order->getStatus() === 'completed') {
            return $this->json(['message' => 'Order is already completed.'], 422);
        }

        $order->setStatus('completed');
        $order->setCompletedAt(new \DateTimeImmutable());
        $this->logActivity('Order Completed', sprintf('Order #%s completed.', $order->getOrderNumber()));
        $this->entityManager->flush();

        return $this->json($this->serializeOrder($order));
    }

    #[Route('/{id}', name: 'api_order_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Order $order): JsonResponse
    {
        if ($order->getStatus() !== 'completed') {
            foreach ($order->getOrderItems() as $item) {
                if ($item->getProduct()) {
                    $item->getProduct()->setStockQuantity($item->getProduct()->getStockQuantity() + $item->getQuantity());
                }
            }
        }

        $this->logActivity('Order Deleted', sprintf(
            'Order #%s deleted (Status: %s)', $order->getOrderNumber(), $order->getStatus()
        ));
        $this->entityManager->remove($order);
        $this->entityManager->flush();

        return $this->json(null, 204);
    }

    private function serializeOrder(Order $order): array
    {
        return [
            'id'              => $order->getId(),
            'orderNumber'     => $order->getOrderNumber(),
            'status'          => $order->getStatus(),
            'totalAmount'     => $order->getTotalAmount(),
            'customerName'    => $order->getCustomerName(),
            'customerAddress' => $order->getCustomerAddress(),
            'city'            => $order->getCity(),
            'province'        => $order->getProvince(),
            'deliveryType'    => $order->getDeliveryType(),
            'phoneNumber'     => $order->getPhoneNumber(),
            'customer'        => $order->getCustomer()?->getId(),
            'createdAt'       => $order->getCreatedAt()?->format(\DateTimeInterface::ATOM),
            'completedAt'     => $order->getCompletedAt()?->format(\DateTimeInterface::ATOM),
            'orderItems'      => array_map(fn(OrderItem $i) => [
                'id'        => $i->getId(),
                'product'   => $i->getProduct()?->getId(),
                'quantity'  => $i->getQuantity(),
                'unitPrice' => $i->getUnitPrice(),
                'subtotal'  => $i->getSubtotal(),
            ], $order->getOrderItems()->toArray()),
        ];
    }

    private function logActivity(string $action, string $targetData): void
    {
        $user = $this->getUser();
        $log = new ActivityLog();
        $log->setUser($user);
        $log->setUsername($user->getUsername());
        $log->setRole($this->isGranted('ROLE_ADMIN') ? 'Admin' : 'User');
        $log->setAction($action);
        $log->setTargetData($targetData);
        $this->entityManager->persist($log);
    }
}
