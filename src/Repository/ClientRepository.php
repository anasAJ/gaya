<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Client>
 */
class ClientRepository extends ServiceEntityRepository
{

    private Security $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Client::class);
        $this->security = $security;
    }

    public function findClientsByUserTeam($queryBuilder)
    {
        $user = $this->security->getUser();
        if (!$user) {
            return [];
        }

        // Récupérer l'ID de la team de l'utilisateur
        $teamId = $user->getTeam()->getId();

        // Ajouter la condition pour l'équipe de l'utilisateur et appliquer le filtre
        return $queryBuilder
            ->join('c.user', 'u') // Relation Client -> User
            ->join('u.team', 't') // Relation User -> Team
            ->where('t.id = :teamId') // Vérifier que l'équipe est la même
            ->setParameter('teamId', $teamId)
            ->getQuery()
            ->getResult();
    }

    public function findClientsByUserId($queryBuilder)
    {
        $user = $this->security->getUser();
        if (!$user) {
            return [];
        }

        // Récupérer l'ID de l'utilisateur
        $userId = $user->getId();

        // Ajouter la condition pour l'utilisateur et appliquer le filtre
        if (!$queryBuilder->getParameter('userId')) {
            $queryBuilder
                ->andWhere('c.user = :userId') // Utilisation de andWhere() au lieu de where()
                ->setParameter('userId', $userId);
        }
    
        return $queryBuilder->getQuery()->getResult();
    }


    //    /**
    //     * @return Client[] Returns an array of Client objects
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

    //    public function findOneBySomeField($value): ?Client
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
