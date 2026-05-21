<?php

namespace App\Repository;

use App\Entity\Payment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Payment>
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    /** Find all payments for a given order */
    public function findByOrder(int $orderId): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.order', 'o')
            ->where('o.id = :orderId')
            ->setParameter('orderId', $orderId)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** Find payments by status */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.status = :status')
            ->setParameter('status', $status)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** Total revenue from completed payments */
    public function getTotalRevenue(): string
    {
        $result = $this->createQueryBuilder('p')
            ->select('SUM(p.amount)')
            ->where('p.status = :status')
            ->setParameter('status', Payment::STATUS_COMPLETED)
            ->getQuery()
            ->getSingleScalarResult();

        return $result ?? '0.00';
    }
}
