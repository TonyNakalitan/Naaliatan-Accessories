<?php

namespace App\Controller\Api;

use App\Entity\ActivityLog;
use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/payments')]
#[IsGranted('ROLE_CUSTOMER')]
class ApiPaymentController extends AbstractController
{
    public function __construct(
        private OrderRepository $orderRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('', name: 'api_payments_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $user = $this->getUser();
        $orders = $this->orderRepository->findBy(['customer' => $user], ['createdAt' => 'DESC']);

        return $this->json(array_map(fn(Order $order) => $this->serializePayment($order), $orders));
    }

    #[Route('/{id}', name: 'api_payments_show', methods: ['GET'])]
    public function show(Order $order): JsonResponse
    {
        if ($order->getCustomer() !== $this->getUser()) {
            return $this->json(['message' => 'Access denied.'], 403);
        }

        return $this->json($this->serializePayment($order));
    }

    #[Route('/charge', name: 'api_payments_charge', methods: ['POST'])]
    public function charge(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json(['message' => 'Invalid JSON body.'], 400);
        }

        $orderId = $data['order_id'] ?? null;
        $paymentMethod = trim((string) ($data['payment_method'] ?? ''));

        if (!$orderId || !$paymentMethod) {
            return $this->json(['message' => 'order_id and payment_method are required.'], 422);
        }

        $order = $this->orderRepository->find($orderId);
        if (!$order) {
            return $this->json(['message' => 'Order not found.'], 404);
        }

        if ($order->getCustomer() !== $this->getUser()) {
            return $this->json(['message' => 'Access denied.'], 403);
        }

        if (in_array($order->getStatus(), ['completed', 'cancelled', 'processing'], true)) {
            return $this->json(['message' => 'This order cannot be paid at this stage.'], 422);
        }

        $order->setStatus('processing');
        $order->setPaymentMethod($paymentMethod);
        $order->setPaidAt(new \DateTimeImmutable());
        $this->logActivity('Payment Captured', sprintf('Payment successful for order #%s using %s', $order->getOrderNumber(), $paymentMethod));
        $this->entityManager->flush();

        return $this->json($this->serializePayment($order));
    }

    private function serializePayment(Order $order): array
    {
        return [
            'orderId' => $order->getId(),
            'orderNumber' => $order->getOrderNumber(),
            'status' => $order->getStatus(),
            'totalAmount' => $order->getTotalAmount(),
            'customerName' => $order->getCustomerName(),
            'customerAddress' => $order->getCustomerAddress(),
            'createdAt' => $order->getCreatedAt()?->format(\DateTimeInterface::ATOM),
            'items' => array_map(fn($item) => [
                'id' => $item->getId(),
                'product' => $item->getProduct()?->getName(),
                'quantity' => $item->getQuantity(),
                'subtotal' => $item->getSubtotal(),
            ], $order->getOrderItems()->toArray()),
        ];
    }

    private function logActivity(string $action, string $targetData): void
    {
        $log = new ActivityLog();
        $log->setUser($this->getUser());
        $log->setUsername($this->getUser()->getUsername());
        $log->setRole('Customer');
        $log->setAction($action);
        $log->setTargetData($targetData);
        $this->entityManager->persist($log);
    }
}
