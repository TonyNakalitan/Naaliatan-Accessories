<?php

namespace App\Repository;

use App\Entity\Character;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Character>
 */
class CharacterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Character::class);
    }

    public function save(Character $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Character $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByAlignment(string $alignment): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.alignment = :alignment')
            ->setParameter('alignment', $alignment)
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function searchCharacters(string $query): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.name LIKE :query')
            ->orWhere('c.characterSymbols LIKE :query')
            ->orWhere('c.description LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getTotalCharactersCount(): int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getAlignmentStats(): array
    {
        return $this->createQueryBuilder('c')
            ->select('c.alignment', 'COUNT(c.id) as count')
            ->groupBy('c.alignment')
            ->orderBy('count', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findWithProducts(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.products', 'p')
            ->addSelect('COUNT(p.id) as productCount')
            ->groupBy('c.id')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
