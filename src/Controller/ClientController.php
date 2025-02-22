<?php

namespace App\Controller;

use App\Entity\Adresses;
use App\Entity\Client;
use App\Entity\Production;
use App\Entity\Source;
use App\Form\ClientFilterType;
use App\Form\ClientType;
use App\Form\AdressesType;
use App\Form\ProductionType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/client')]
final class ClientController extends AbstractController
{
    #[Route('/', name: 'app_client_index', methods: ['GET', 'POST'])]
    public function index(ClientRepository $clientRepository, PaginatorInterface $paginator, Request $request, EntityManagerInterface $entityManager): Response
    {
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

        return $this->render('client/index.html.twig', [
            'clients' => $clients,
            'filterForm' => $filterForm->createView(),
        ]);

    }

    #[Route('/new', name: 'app_client_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $client = new Client();
    
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$client->getUser()){
                $client->setUser($this->getUser());
            }
            $client->setAddedDate(date('Y-m-d'));
            $client->setAddedTime(date('H:i:s'));
            $client->setIndicative('+33');
            
            if( !$client->getSource() or is_null($client->getSource())){
                $source = $entityManager->getRepository(Source::class)->findOneBy(['id' => 1]);
                $client->setSource($source);
            }
            

            $entityManager->persist($client);
            $entityManager->flush();


            return $this->redirectToRoute('app_client_edit', ['id' => $client->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('client/new.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_client_show', methods: ['GET'])]
    public function show(Client $client): Response
    {
        $adresses = $client->getAdresses(); // Charge les adresses associées

        return $this->render('client/show.html.twig', [
            'client' => $client,
            'adresses' => $adresses,
            
        ]);
    }


    #[Route('/{id}/edit', name: 'app_client_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ClientRepository $clientRepository, $id, EntityManagerInterface $entityManager): Response|JsonResponse
    {
        $client = $clientRepository->find($id);

        $adress = new Adresses();
        $adress_form = $this->createForm(AdressesType::class, $adress, [
            'client_id' => $client->getId(),
        ]);

        $production = new Production();
        $production_form = $this->createForm(ProductionType::class, $production);

        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$client->setSource($client->getSource());
            $entityManager->flush();

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse([
                    'success' => true,
                    'message' => 'Client mis à jour avec succès !',
                ], Response::HTTP_OK);
            }

            $this->addFlash('success', 'Client mis à jour avec succès !');
            return $this->redirectToRoute('app_client_edit'); 
        }

        // Si la requête n'est PAS en AJAX, on affiche normalement le formulaire
        if (!$request->isXmlHttpRequest()) {
            return $this->render('client/edit.html.twig', [
                'form' => $form->createView(),
                'adress_form' => $adress_form,
                'production_form' => $production_form,
                'client' => $client,
            ]);
        }

        // Retourne les erreurs en JSON si c'est une requête AJAX
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        return new JsonResponse([
            'success' => false,
            'errors' => $errors,
        ], Response::HTTP_BAD_REQUEST);
        
    }


    #[Route('/{id}', name: 'app_client_delete', methods: ['POST'])]
    public function delete(Request $request, Client $client, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$client->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($client);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_client_index', [], Response::HTTP_SEE_OTHER);
    }
}
