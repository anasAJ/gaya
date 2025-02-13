<?php

namespace App\Repository;

use App\Entity\Contract;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Contract>
 */
class ContractRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contract::class);
    }

    /**
     * Récupérer tous les contrats pour un client donné
     *
     * @param int $clientId
     * @return Contract[]
     */
    public function findContractsByClientId(int $clientId)
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->innerJoin('c.production', 'p')
            ->innerJoin('p.client', 'cl')
            ->where('cl.id = :clientId')
            ->setParameter('clientId', $clientId);

        return $queryBuilder->getQuery()->getResult();
    }

    // src/Repository/ContractRepository.php
    public function hasContractsForClient(int $clientId): bool
    {
        return (bool) $this->createQueryBuilder('c')
            ->innerJoin('c.production', 'p')   // Jointure avec Production
            ->innerJoin('p.client', 'cl')      // Jointure avec Client
            ->where('cl.id = :clientId')       // Filtrer par client ID
            ->setParameter('clientId', $clientId)
            ->setMaxResults(1)                 // On ne cherche qu'un seul résultat
            ->getQuery()
            ->getOneOrNullResult();            // Retourne null si aucun contrat n'est trouvé
    }

    public function findContractsForClient(int $clientId)
    {
        return  $this->createQueryBuilder('c')
            ->innerJoin('c.production', 'p')   // Jointure avec Production
            ->innerJoin('p.client', 'cl')      // Jointure avec Client
            ->where('cl.id = :clientId')       // Filtrer par client ID
            ->setParameter('clientId', $clientId)
            ->setMaxResults(1)                 // On ne cherche qu'un seul résultat
            ->getQuery();
    }


    //    /**
    //     * @return Contract[] Returns an array of Contract objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Contract
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
