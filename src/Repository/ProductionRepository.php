<?php

namespace App\Repository;

use App\Entity\Production;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Production>
 */
class ProductionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Production::class);
    }

    public function findProductsByClientId(int $clientId): array
    {
        return $this->createQueryBuilder('pr')
            ->join('pr.product', 'p')
            ->join('pr.client', 'c')
            ->where('c.id = :clientId')
            ->setParameter('clientId', $clientId)
            ->select('p') // On sélectionne uniquement les produits
            ->getQuery()
            ->getResult();
    }
     /*$products = $entityManager->getRepository(Product::class)
            ->createQueryBuilder('p')
            ->join('p.productions', 'pr')
            ->join('pr.client', 'c')
            ->where('c.id = :clientId')
            ->setParameter('clientId', 11)
            ->getQuery()
            ->getResult();
        dd($products);*/

    //    /**
    //     * @return Production[] Returns an array of Production objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Production
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
