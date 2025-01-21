<?php

namespace App\Controller;

use App\Entity\Adresses;
use App\Entity\Client;
use App\Form\AdressesType;
use App\Repository\AdressesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;


#[Route('/addresses')]
final class AdressesController extends AbstractController
{
    #[Route(name: 'app_adresses_index', methods: ['GET'])]
    public function index(AdressesRepository $adressesRepository): Response
    {
        return $this->render('adresses/index.html.twig', [
            'adresses' => $adressesRepository->findAll(),
        ]);
    }

    #[Route('/api/new/{clientId}', name: 'app_api_adresses_add', methods: ['POST'])]
    public function add(Request $request, Client $client, EntityManagerInterface $entityManager, $clientId): JsonResponse
    {
        $adress = new Adresses();
        $client = $entityManager->getRepository(Client::class)->find($clientId);
        if (!$client) {
            return new JsonResponse([
                'error' => 'Client not found',
            ], Response::HTTP_NOT_FOUND);
        }
        $adress->setClient($client);

        $form = $this->createForm(AdressesType::class, $adress);
        $form->handleRequest($request);
        //dd($adress);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->persist($adress);
            $entityManager->flush();

            // Retourner les informations de l'adresse pour l'afficher directement
            return new JsonResponse([
                'id' => $adress->getId(),
                'address_1' => $adress->getAddress1(),
                'address_2' => $adress->getAddress2(),
                'city' => $adress->getCity(),
                'zip' => $adress->getZip(),
                'country' => $adress->getCountry(),
                'designation' => $adress->getDesignation(),
            ], Response::HTTP_CREATED);
        }

        // En cas d'erreur, retourner les messages d'erreur
        return new JsonResponse([
            'errors' => (string) $form->getErrors(true, false),
        ], Response::HTTP_BAD_REQUEST);
    }

    
    #[Route('/api/delete/{id}', name: 'app_api_adresses_delete', methods: ['DELETE'])]
    public function remove(EntityManagerInterface $entityManager,Request $request, $id): JsonResponse
    {
        $adress = $entityManager->getRepository(Adresses::class)->find($id);

        if (!$adress) {
            return new JsonResponse(['error' => 'Adresse non trouvée'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($adress);
        $entityManager->flush();

        return new JsonResponse(['success' => 'Adresse supprimée'], Response::HTTP_OK);
    }


    #[Route('/new', name: 'app_adresses_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $adress = new Adresses();
        $form = $this->createForm(AdressesType::class, $adress);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($adress);
            $entityManager->flush();

            return $this->redirectToRoute('app_adresses_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('adresses/new.html.twig', [
            'adress' => $adress,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_adresses_show', methods: ['GET'])]
    public function show(Adresses $adress): Response
    {
        return $this->render('adresses/show.html.twig', [
            'adress' => $adress,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_adresses_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Adresses $adress, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdressesType::class, $adress);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_adresses_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('adresses/edit.html.twig', [
            'adress' => $adress,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_adresses_delete', methods: ['POST'])]
    public function delete(Request $request, Adresses $adress, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$adress->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($adress);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_adresses_index', [], Response::HTTP_SEE_OTHER);
    }
}
