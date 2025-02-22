<?php

namespace App\Controller;

use App\Entity\Config;
use App\Form\ConfigType;
use App\Repository\ConfigRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/config')]
final class ConfigController extends AbstractController
{
    #[Route(name: 'app_config_index', methods: ['GET'])]
    public function index(ConfigRepository $configRepository): Response
    {
        return $this->render('config/index.html.twig', [
            'configs' => $configRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_config_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $config = new Config();
        $form = $this->createForm(ConfigType::class, $config);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($config);
            $entityManager->flush();

            return $this->redirectToRoute('app_config_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('config/new.html.twig', [
            'config' => $config,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_config_show', methods: ['GET'])]
    public function show(Config $config): Response
    {
        return $this->render('config/show.html.twig', [
            'config' => $config,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_config_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Config $config, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ConfigType::class, $config);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_config_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('config/edit.html.twig', [
            'config' => $config,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_config_delete', methods: ['POST'])]
    public function delete(Request $request, Config $config, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$config->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($config);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_config_index', [], Response::HTTP_SEE_OTHER);
    }
}
