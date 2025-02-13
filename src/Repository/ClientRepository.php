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

    public function findClientsByUserTeam()
    {
        // Récupérer l'utilisateur connecté
        $user = $this->security->getUser();
        if (!$user) {
            return [];
        }

        // Récupérer l'ID de la team de l'utilisateur
        $teamId = $user->getTeam()->getId();

        // Construire la requête
        return $this->createQueryBuilder('c')
            ->join('c.user', 'u') // Relation Client -> User
            ->join('u.team', 't') // Relation User -> Team
            ->where('t.id = :teamId') // Vérifier que l'équipe est la même
            ->andWhere('u.roles LIKE :role') // Vérifier que l'utilisateur est ROLE_USER
            ->setParameter('teamId', $teamId)
            ->setParameter('role', '%ROLE_USER%') // Vérifier que "ROLE_USER" est présent dans le tableau des rôles
            ->getQuery()
            ->getResult();
    }

    public function findClientsByUserId()
    {
        // Récupérer l'utilisateur connecté
        $user = $this->security->getUser();
        if (!$user) {
            return [];
        }

        // Récupérer l'ID de la team de l'utilisateur
        $userId = $user->getId();

        // Construire la requête
        return $this->createQueryBuilder('c')
            ->join('c.user', 'u')
            ->where('c.user = :userId') // Vérifier que l'équipe est la même
            ->andWhere('u.roles LIKE :role') // Vérifier que l'utilisateur est ROLE_USER
            ->setParameter('userId', $userId)
            ->setParameter('role', '%ROLE_USER%') // Vérifier que "ROLE_USER" est présent dans le tableau des rôles
            ->getQuery()
            ->getResult();
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
