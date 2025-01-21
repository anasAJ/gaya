<?php

namespace App\Controller;

use App\Entity\Adresses;
use App\Entity\Client;
use App\Form\ClientType;
use App\Form\AdressesType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/client')]
final class ClientController extends AbstractController
{
    #[Route(name: 'app_client_index', methods: ['GET'])]
    public function index(ClientRepository $clientRepository): Response
    {
        return $this->render('client/index.html.twig', [
            'clients' => $clientRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_client_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $client = new Client();
        $adress = new Adresses();
        $adress_form = $this->createForm(AdressesType::class, $adress, [
            'client_id' => $client->getId(),
        ]);

        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $client->setAddedDate(date('Y-m-d'));
            $client->setAddedTime(date('H:i:s'));

            $entityManager->persist($client);
            $entityManager->flush();

            return $this->redirectToRoute('app_client_edit', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('client/new.html.twig', [
            'client' => $client,
            'form' => $form,
            'adress_form' => $adress_form,
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
    public function edit(Request $request, Client $client, EntityManagerInterface $entityManager): Response|JsonResponse
    {
        $adress = new Adresses();
        $adress_form = $this->createForm(AdressesType::class, $adress, [
            'client_id' => $client->getId(),
        ]);

        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            // Vérifie si la requête est AJAX
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse([
                    'success' => true,
                    'message' => 'Client mis à jour avec succès !',
                ], Response::HTTP_OK);
            }

            // Redirection si ce n'est pas une requête AJAX
            $this->addFlash('success', 'Client mis à jour avec succès !');
            return $this->redirectToRoute('app_client_edit'); // Change la route si nécessaire
        }

        // Si la requête n'est PAS en AJAX, on affiche normalement le formulaire
        if (!$request->isXmlHttpRequest()) {
            return $this->render('client/edit.html.twig', [
                'form' => $form->createView(),
                'adress_form' => $adress_form,
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
