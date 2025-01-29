<?php

namespace App\Controller;

use App\Entity\Production;
use App\Service\YousignService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class YousignController extends AbstractController
{


    #[Route('/sign-document/{production}', name: 'sign_document', methods: ['POST', 'GET'])]
    public function signDocument(Request $request,YousignService $yousignService, $production, EntityManagerInterface $em): Response
    {
        // Récupération de la production depuis la base de données
        $production = $em->getRepository(Production::class)->find($production);
        //dd($production->getClient()->getFirstName());
        // Récupération du fichier et des informations du signataire
        $file = ('./public/LMalt.pdf');
        $signerInfo = [
            'first_name' => $production->getClient()->getFirstName(),
            'last_name' => $production->getClient()->getLastName(),
            'email' => $production->getClient()->getEmail(),
            'phone_number' =>$production->getClient()->getIndicative() . $production->getClient()->getPhone(),
        ];

        $acords = [
            "height" => 39,
            "width" => 99,
            "page" => 1,
            "x" => 440,
            "y" => 649
        ];

        // 1. Créer une demande de signature
        $signatureRequestId = $yousignService->createSignatureRequest();

        // 2. Ajouter un document
        $documentId = $yousignService->addDocumentToSignatureRequest_($signatureRequestId, $file);

        // 3. Ajouter un signataire
        $yousignService->addSignerToSignatureRequest($signatureRequestId, $documentId, $signerInfo, $acords);

        // 4. Activer la signature
        $yousignService->activateSignatureRequest($signatureRequestId);

        return $this->json(['message' => 'Demande de signature envoyée !']);
    }

}
