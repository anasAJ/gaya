<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Production;
use App\Form\ProductionType;
use App\Repository\ProductionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;


#[Route('/production')]
final class ProductionController extends AbstractController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route(name: 'app_production_index', methods: ['GET'])]
    public function index(ProductionRepository $productionRepository): Response
    {
        return $this->render('production/index.html.twig', [
            'productions' => $productionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_production_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $production = new Production();
        $form = $this->createForm(ProductionType::class, $production);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($production);
            $entityManager->flush();

            return $this->redirectToRoute('app_production_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('production/new.html.twig', [
            'production' => $production,
            'form' => $form,
        ]);
    }

    #[Route('/api/new/{clientId}', name: 'app_api_production_add', methods: ['POST'])]
    public function add(Request $request, EntityManagerInterface $entityManager, $clientId): JsonResponse
    {
        $production = new Production();
        $form = $this->createForm(ProductionType::class, $production);
        $form->handleRequest($request);

        $client = $entityManager->getRepository(Client::class)->find($clientId);
        if (!$client) {
            return new JsonResponse([
                'error' => 'Client not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $user = $this->security->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $production->setClient($client);
            $production->setUser($user);

            $entityManager->persist($production);
            $entityManager->flush();
            
            return new JsonResponse([
                'id' => $production->getId(),
                'products' => $production->getProduct()->map(fn($product) => [
                    'id' => $product->getId(),
                    'name' => $product->getName()
                ])->toArray(),
                'app_fees' => $production->getAppFees(),
                'signature_provider' => $production->getSignatureProvider()->getProvider()
            ], Response::HTTP_CREATED);
        }

        // En cas d'erreur, retourner les messages d'erreur
        return new JsonResponse([
            'errors' => (string) $form->getErrors(true, false),
        ], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/api/delete/{id}', name: 'app_api_production_delete', methods: ['DELETE'])]
    public function remove(EntityManagerInterface $entityManager,Request $request, $id): JsonResponse
    {
        $production = $entityManager->getRepository(Production::class)->find($id);

        if (!$production) {
            return new JsonResponse(['error' => 'Production non trouvée'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($production);
        $entityManager->flush();

        return new JsonResponse(['success' => 'Production supprimée'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'app_production_show', methods: ['GET'])]
    public function show(Production $production): Response
    {
        return $this->render('production/show.html.twig', [
            'production' => $production,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_production_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Production $production, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductionType::class, $production);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_production_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('production/edit.html.twig', [
            'production' => $production,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_production_delete', methods: ['POST'])]
    public function delete(Request $request, Production $production, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$production->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($production);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_production_index', [], Response::HTTP_SEE_OTHER);
    }
}
