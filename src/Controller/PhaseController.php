<?php

namespace App\Controller;

use App\Entity\Phase;
use App\Form\PhaseType;
use App\Repository\PhaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/phase')]
final class PhaseController extends AbstractController
{
    #[Route(name: 'app_phase_index', methods: ['GET'])]
    public function index(PhaseRepository $phaseRepository): Response
    {
        return $this->render('phase/index.html.twig', [
            'phases' => $phaseRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_phase_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $phase = new Phase();
        $form = $this->createForm(PhaseType::class, $phase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($phase);
            $entityManager->flush();

            return $this->redirectToRoute('app_phase_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('phase/new.html.twig', [
            'phase' => $phase,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_phase_show', methods: ['GET'])]
    public function show(Phase $phase): Response
    {
        return $this->render('phase/show.html.twig', [
            'phase' => $phase,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_phase_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Phase $phase, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PhaseType::class, $phase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_phase_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('phase/edit.html.twig', [
            'phase' => $phase,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_phase_delete', methods: ['POST'])]
    public function delete(Request $request, Phase $phase, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$phase->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($phase);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_phase_index', [], Response::HTTP_SEE_OTHER);
    }
}
