<?php

namespace App\Controller\ActionsApi;

use App\Repository\StatusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClientStatusController extends AbstractController
{
    #[Route('/client/status/{phase_id}', name: 'app_client_status', methods: ['GET'])]
    public function index($phase_id, StatusRepository $statusRepository): Response
    {
        $statuses = $statusRepository->findBy(['phase'=>$phase_id]);

        $data = array_map(fn($status) => [
            'id' => $status->getId(),
            'name' => $status->getName(),
        ], $statuses);
        return new JsonResponse($data);
    }
}
