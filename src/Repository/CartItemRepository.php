<?php

namespace App\Repository;

use App\Entity\CartItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CartItem>
 */
class CartItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartItem::class);
    }

    /** @return CartItem[] */
    public function findByUser(int $userId): array
    {
        return $this->createQueryBuilder('c')
            ->where(['c.user = :user'])
            ->setParameter('user', $userId)
            ->getQuery()
            ->getResult()
        ;
    }

    public function removeBook(int $userId, int $bookId): void
    {
        $item = $this->createQueryBuilder('c')
            ->where(['c.user = :user'])
            ->andWhere('c.book = :book')
            ->setParameter('user', $userId)
            ->setParameter('book', $bookId)
            ->getQuery()
            ->getSingleResult();

        $this->getEntityManager()->remove($item);
        $this->getEntityManager()->flush();
    }
}
