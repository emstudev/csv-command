<?php
namespace App\Repository;

use App\Entity\StockItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class StockItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockItem::class);
    }

    /**
     * Search by mpn or ean.
     * If mpn provided => search by mpn
     * else if ean provided => search by ean
     * Returns array of StockItem
     */
    public function findByMpnOrEan(?string $mpn, ?string $ean): array
    {
        $qb = $this->createQueryBuilder('s');

        if ($mpn !== null && $ean !== null) {
            $qb->andWhere('s.mpn = :mpn OR s.ean = :ean')
                ->setParameter('mpn', $mpn)
                ->setParameter('ean', $ean);
            return $qb->getQuery()->getResult();
        }

        if ($mpn !== null) {
            $qb->andWhere('s.mpn = :mpn')
                ->setParameter('mpn', $mpn);
            return $qb->getQuery()->getResult();
        }

        if ($ean !== null) {
            $qb->andWhere('s.ean = :ean')
                ->setParameter('ean', $ean);
            return $qb->getQuery()->getResult();
        }

        return [];
    }
}
