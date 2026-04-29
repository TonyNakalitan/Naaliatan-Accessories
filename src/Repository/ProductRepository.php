<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function save(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByCharacter(int $characterId): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.character = :characterId')
            ->setParameter('characterId', $characterId)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findLowStockProducts(int $threshold = 10): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.stockQuantity <= :threshold')
            ->setParameter('threshold', $threshold)
            ->orderBy('p.stockQuantity', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOutOfStockProducts(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.stockQuantity = 0')
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function searchProducts(string $query): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.character', 'c')
            ->where('p.name LIKE :query')
            ->orWhere('p.productCode LIKE :query')
            ->orWhere('p.description LIKE :query')
            ->orWhere('c.name LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getTotalStockValue(): float
    {
        return $this->createQueryBuilder('p')
            ->select('SUM(p.stockQuantity * p.price)')
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }

    public function getTotalProductsCount(): int
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getLowStockCount(int $threshold = 10): int
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.stockQuantity <= :threshold')
            ->setParameter('threshold', $threshold)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getOutOfStockCount(): int
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.stockQuantity = 0')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findProductsInUse(): array
    {
        // Find products that have been involved in stock transactions in the last 30 days
        $thirtyDaysAgo = new \DateTime();
        $thirtyDaysAgo->modify('-30 days');

        $results = $this->createQueryBuilder('p')
            ->select('p', 'COUNT(st.id) as usageCount', 'MAX(st.createdAt) as lastUsedAt')
            ->innerJoin('p.stockTransactions', 'st')
            ->where('st.createdAt >= :thirtyDaysAgo')
            ->setParameter('thirtyDaysAgo', $thirtyDaysAgo)
            ->groupBy('p.id')
            ->orderBy('MAX(st.createdAt)', 'DESC')
            ->getQuery()
            ->getResult();

        // Transform results to the expected structure
        $productsInUse = [];
        foreach ($results as $result) {
            $productsInUse[] = [
                'product' => $result[0],
                'usageCount' => $result['usageCount'],
                'lastUsedAt' => $result['lastUsedAt']
            ];
        }

        return $productsInUse;
    }

    public function findPaginatedProducts(int $page = 1, int $limit = 4, ?string $search = null, ?string $status = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.character', 'c')
            ->orderBy('p.createdAt', 'DESC');

        // Apply search filter
        if ($search) {
            $qb->andWhere('p.name LIKE :search OR p.productCode LIKE :search OR c.name LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        // Apply status filter
        if ($status === 'in stock') {
            $qb->andWhere('p.stockQuantity > 0');
        } elseif ($status === 'out of stock') {
            $qb->andWhere('p.stockQuantity = 0');
        }

        return $qb
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getFilteredProductsCount(?string $search = null, ?string $status = null): int
    {
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->leftJoin('p.character', 'c');

        // Apply search filter
        if ($search) {
            $qb->andWhere('p.name LIKE :search OR p.productCode LIKE :search OR c.name LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        // Apply status filter
        if ($status === 'in stock') {
            $qb->andWhere('p.stockQuantity > 0');
        } elseif ($status === 'out of stock') {
            $qb->andWhere('p.stockQuantity = 0');
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function findLowStockProductsPaginated(int $page = 1, int $limit = 5, int $threshold = 10): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.stockQuantity <= :threshold')
            ->setParameter('threshold', $threshold)
            ->orderBy('p.stockQuantity', 'ASC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
