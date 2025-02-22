<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientFilterType;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CallsController extends AbstractController
{

    #[Route('/calls', name: 'app_calls_index', methods: ['GET', 'POST'])]
    public function index(ClientRepository $clientRepository, PaginatorInterface $paginator, Request $request, EntityManagerInterface $entityManager): Response
    {
        $client = new Client();
        $clientForm = $this->createForm(ClientType::class, $client);
        $clientForm->handleRequest($request);

        // Création du formulaire de filtre
        $filterForm = $this->createForm(ClientFilterType::class);
        
        $filterForm->handleRequest($request);
        // Initialisation de la requête pour filtrer les clients
        $queryBuilder = $clientRepository->createQueryBuilder('c');
        // Appliquer les filtres s'il y en a
        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $data = $filterForm->getData();
            //dd($data);

            if ($data['first_name']) {
                $queryBuilder->andWhere('c.first_name LIKE :first_name')
                             ->setParameter('first_name', '%' . $data['first_name'] . '%');
            }
            if ($data['last_name']) {
                $queryBuilder->andWhere('c.last_name LIKE :last_name')
                             ->setParameter('last_name', '%' . $data['last_name'] . '%');
            }
            if ($data['email']) {
                $queryBuilder->andWhere('c.email LIKE :email')
                             ->setParameter('email', '%' . $data['email'] . '%');
            }
            if ($data['phone']) {
                $queryBuilder->andWhere('c.phone LIKE :phone')
                             ->setParameter('phone', '%' . $data['phone'] . '%');
            }
            if ($data['added_date']) {
                if ($data['added_date_to']) {
                    $queryBuilder->andWhere('c.addedDate >= :added_date and c.addedDate <= :added_date_to')
                                 ->setParameter('added_date_to', $data['added_date_to'])
                                 ->setParameter('added_date', $data['added_date']);
                }else{
                    $queryBuilder->andWhere('c.addedDate = :added_date')
                                 ->setParameter('added_date', $data['added_date']);
                }
            }
            if ($data['phase']) {
                $queryBuilder->andWhere('c.phase = :phase')
                             ->setParameter('phase',  $data['phase'] );
            }
            if ($data['status']) {
                $queryBuilder->andWhere('c.status = :status')
                             ->setParameter('status',  $data['status'] );
            }
            if ($data['source']) {
                $source = $entityManager->getRepository(Source::class)->find($data['source']);
                if ($source) {
                    $queryBuilder->andWhere('c.Source = :source')
                                 ->setParameter('source', $source);
                }
            }
        }

        // Appliquer la logique des rôles pour récupérer les clients
        if ($this->isGranted('ROLE_ADMIN')) {
            // Récupérer tous les clients (sans restriction) mais avec les filtres appliqués
            $clients = $queryBuilder->getQuery()->getResult();
        } elseif ($this->isGranted('ROLE_MANAGER')) {
            // Récupérer les clients associés à l'équipe du manager
            $clients = $clientRepository->findClientsByUserTeam($queryBuilder);
        } else {
            // Récupérer les clients associés à l'utilisateur
            $clients = $clientRepository->findClientsByUserId($queryBuilder);
        }
        

        // Paginer les résultats
        $clients = $paginator->paginate(
            $queryBuilder, // La requête
            $request->query->getInt('page', 1), // Numéro de page (par défaut : 1)
            5 // Nombre d'éléments par page
        );

        return $this->render('calls/index.html.twig', [
            'clients' => $clients,
            'filterForm' => $filterForm->createView(),
            'clientForm' => $clientForm,
        ]);

    }
}
