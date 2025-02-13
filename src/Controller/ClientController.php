<?php

namespace App\Controller;

use App\Entity\Adresses;
use App\Entity\Client;
use App\Entity\Production;
use App\Entity\Source;
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
    #[Route(name: 'app_client_index', methods: ['GET'])]
    public function index(ClientRepository $clientRepository, PaginatorInterface $paginator, Request $request): Response
    {
        if( $this->isGranted('ROLE_ADMIN')){
            $query = $clientRepository->findAll();
        }elseif($this->isGranted('ROLE_MANAGER')){
            $query = $clientRepository->findClientsByUserTeam();
        }else{
            $query = $clientRepository->findClientsByUserId();
        }
        

        // Paginer les résultats
        $clients = $paginator->paginate(
            $query, // La requête
            $request->query->getInt('page', 1), // Numéro de page (par défaut : 1)
            5 // Nombre d'éléments par page
        );

        return $this->render('client/index.html.twig', [
            'clients' => $clients,
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
