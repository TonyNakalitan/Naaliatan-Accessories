<?php

namespace App\Controller;

use App\Entity\ActivityLog;
use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PaymentManagementController extends AbstractController
{
    public function __construct(
        private OrderRepository $orderRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    // ── Admin routes ──────────────────────────────────────────────────────────

    #[Route('/admin/payment-management', name: 'app_admin_payment_management_index')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminIndex(Request $request): Response
    {
        return $this->listPayments($request);
    }

    #[Route('/admin/payment-management/{id}', name: 'app_admin_payment_management_show')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminShow(Order $order): Response
    {
        return $this->showPayment($order);
    }

    #[Route('/admin/payment-management/{id}/approve', name: 'app_admin_payment_management_approve', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminApprove(Order $order, Request $request): Response
    {
        return $this->approvePayment($order, $request, 'admin');
    }

    #[Route('/admin/payment-management/{id}/reject', name: 'app_admin_payment_management_reject', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminReject(Order $order, Request $request): Response
    {
        return $this->rejectPayment($order, $request, 'admin');
    }

    // ── Staff routes ──────────────────────────────────────────────────────────

    #[Route('/staff/payment-management', name: 'app_staff_payment_management_index')]
    #[IsGranted('ROLE_STAFF')]
    public function staffIndex(Request $request): Response
    {
        return $this->listPayments($request);
    }

    #[Route('/staff/payment-management/{id}', name: 'app_staff_payment_management_show')]
    #[IsGranted('ROLE_STAFF')]
    public function staffShow(Order $order): Response
    {
        return $this->showPayment($order);
    }

    #[Route('/staff/payment-management/{id}/approve', name: 'app_staff_payment_management_approve', methods: ['POST'])]
    #[IsGranted('ROLE_STAFF')]
    public function staffApprove(Order $order, Request $request): Response
    {
        return $this->approvePayment($order, $request, 'staff');
    }

    #[Route('/staff/payment-management/{id}/reject', name: 'app_staff_payment_management_reject', methods: ['POST'])]
    #[IsGranted('ROLE_STAFF')]
    public function staffReject(Order $order, Request $request): Response
    {
        return $this->rejectPayment($order, $request, 'staff');
    }

    // ── Shared logic ──────────────────────────────────────────────────────────

    private function listPayments(Request $request): Response
    {
        $status = $request->query->get('status', 'processing');

        // Only show orders that have been paid (have a payment_method set)
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('o')
            ->from(Order::class, 'o')
            ->where('o.paymentMethod IS NOT NULL')
            ->orderBy('o.paidAt', 'DESC');

        if ($status !== 'all') {
            $qb->andWhere('o.status = :status')->setParameter('status', $status);
        }

        $payments = $qb->getQuery()->getResult();

        // Summary counts
        $allPaid = $this->entityManager->createQueryBuilder()
            ->select('o')
            ->from(Order::class, 'o')
            ->where('o.paymentMethod IS NOT NULL')
            ->getQuery()->getResult();

        $counts = [
            'processing' => 0,
            'completed'  => 0,
            'cancelled'  => 0,
        ];
        foreach ($allPaid as $o) {
            $s = $o->getStatus();
            if (isset($counts[$s])) {
                $counts[$s]++;
            }
        }

        return $this->render('PaymentManagementFolder/index.html.twig', [
            'payments'      => $payments,
            'currentStatus' => $status,
            'counts'        => $counts,
        ]);
    }

    private function showPayment(Order $order): Response
    {
        return $this->render('PaymentManagementFolder/show.html.twig', [
            'order' => $order,
        ]);
    }

    private function approvePayment(Order $order, Request $request, string $role): Response
    {
        if (!$this->isCsrfTokenValid('approve_payment_' . $order->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Invalid CSRF token.');
            return $this->redirectToRoute('app_' . $role . '_payment_management_show', ['id' => $order->getId()]);
        }

        if ($order->getStatus() !== 'processing') {
            $this->addFlash('error', 'Only orders in "processing" status can be approved.');
            return $this->redirectToRoute('app_' . $role . '_payment_management_show', ['id' => $order->getId()]);
        }

        $order->setStatus('completed');
        $order->setCompletedAt(new \DateTimeImmutable());

        $this->logActivity(
            'Payment Approved',
            sprintf('Payment for order #%s approved (₱%s via %s)', $order->getOrderNumber(), number_format((float)$order->getTotalAmount(), 2), $order->getPaymentMethod())
        );

        $this->entityManager->flush();

        $this->addFlash('success', 'Payment approved. Order #' . $order->getOrderNumber() . ' marked as completed.');
        return $this->redirectToRoute('app_' . $role . '_payment_management_index');
    }

    private function rejectPayment(Order $order, Request $request, string $role): Response
    {
        if (!$this->isCsrfTokenValid('reject_payment_' . $order->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Invalid CSRF token.');
            return $this->redirectToRoute('app_' . $role . '_payment_management_show', ['id' => $order->getId()]);
        }

        if (!in_array($order->getStatus(), ['processing', 'pending'], true)) {
            $this->addFlash('error', 'This payment cannot be rejected at this stage.');
            return $this->redirectToRoute('app_' . $role . '_payment_management_show', ['id' => $order->getId()]);
        }

        // Restore stock
        foreach ($order->getOrderItems() as $item) {
            $product = $item->getProduct();
            if ($product) {
                $product->setStockQuantity($product->getStockQuantity() + $item->getQuantity());
            }
        }

        $order->setStatus('cancelled');

        $this->logActivity(
            'Payment Rejected',
            sprintf('Payment for order #%s rejected (₱%s via %s)', $order->getOrderNumber(), number_format((float)$order->getTotalAmount(), 2), $order->getPaymentMethod())
        );

        $this->entityManager->flush();

        $this->addFlash('success', 'Payment rejected. Order #' . $order->getOrderNumber() . ' has been cancelled and stock restored.');
        return $this->redirectToRoute('app_' . $role . '_payment_management_index');
    }

    private function logActivity(string $action, string $targetData): void
    {
        $user = $this->getUser();
        $log = new ActivityLog();
        $log->setUser($user);
        $log->setUsername($user->getUsername());
        $log->setRole($this->isGranted('ROLE_ADMIN') ? 'Admin' : 'Staff');
        $log->setAction($action);
        $log->setTargetData($targetData);
        $this->entityManager->persist($log);
    }
}
