<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Contract;
use App\Entity\Product;
use App\Entity\Production;
use App\Form\ContractType;
use App\Service\ConcatService;
use App\Service\PdfGeneratorService;
use App\Repository\ContractRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\YousignService;


#[Route('/contract')]
final class ContractController extends AbstractController
{
    #[Route(name: 'app_contract_index', methods: ['GET'])]
    public function index(ContractRepository $contractRepository): Response
    {
        return $this->render('contract/index.html.twig', [
            'contracts' => $contractRepository->findAll(),
        ]);
    }

    #[Route('/new/{client_id}', name: 'app_contract_new', methods: ['GET', 'POST'])]
    public function new(ContractRepository $contractRepository, EntityManagerInterface $entityManager, $client_id, PdfGeneratorService $pdfGenerator, YousignService $yousignService): Response
    {
        if ($contractRepository->hasContractsForClient($client_id)) {
            return $this->redirectToRoute('app_contract_show', ['id' => $client_id], Response::HTTP_SEE_OTHER);
        } 

        $client = $entityManager->getRepository(Client::class)->find($client_id);
        //dd($client);
        $productions = $entityManager->getRepository(Production::class)->findBy(['client'=>$client->getId()]);
       
        $contractPDF = "";
        $count = 0;

        //foreach ($productions as $production) {foreach ($production->getProduct() as $product) {dump($product);}}die();

        foreach ($productions as $production) {
            
            $contractList = array();
            //dd($production->getSignatureProvider()->getProvider());
            $provider = $production->getSignatureProvider()->getProvider();
            if ($provider === "Yousign") {

                $fields = array();
                $page = 0;
                
                foreach ($production->getProduct() as $product) {
                    //dump($product);
                    
                    $product_meta = $product->getMetaData();
                    //dd($product_meta);
                    //foreach ($product_meta[$provider] as $data) {
                    $page += $product_meta[$provider]['singn_in'];
                
                    $field = $product_meta[$provider]['accords'];
                    $field['page'] = $page;
                    $field['type'] = 'signature';
                    array_push($fields, $field);
                    
                    $page += ($product_meta[$provider]['nbr_page']-$product_meta[$provider]['singn_in']);
                    //}
                    //dump($product->getContract());
                    $contractPDF = $pdfGenerator->makePdf($product->getContract(), $client, 1);
                    dump($contractPDF);
                    # mise a jour des infos du contrats partie 1
                    $contract = new Contract();
                    $count++;
                    $contract->setPdfFile($contractPDF);
                    $contract->setProduct($product);
                    $contract->setProduction($production);

                    $entityManager->persist($contract);
                    $entityManager->flush();
    
                    array_push($contractList, $contractPDF);

                }
                //dd($contractList);

                //-----------------------------
                /*$path = './public/uploads/contracts/compiled/';
                $finalFileName = $client->getFirstName()."_".$client->getLastName()."_".date('U').rand(100000,999999).".pdf";

                $concat = new ConcatService();
		        $concat->setFiles($contractList);
		        $concat->concat();
                $concat->Output($path.$finalFileName, 'F');

                //dd($fields);
                $signerInfo = [
                    'first_name' => $client->getFirstName(),
                    'last_name' => $client->getLastName(),
                    'email' => $client->getEmail(),
                    'phone_number' => $client->getIndicative() . substr($client->getPhone(), 1),
                ];

                // 1. Créer une demande de signature
                $signatureRequestId = $yousignService->createSignatureRequest();
                $documentId = $yousignService->addDocumentToSignatureRequest_($signatureRequestId, $path.$finalFileName);
                $yousignService->addSignerToSignatureRequest($signatureRequestId, $documentId, $signerInfo, $fields);
                $yousignService->activateSignatureRequest($signatureRequestId);*/
                $signatureRequestId = $yousignService->Sign($client, $contractList, $fields);

                $contract->setSignatureId($signatureRequestId);

            }elseif($provider ==="Docusign"){
                $fields = array();
                $page = 0;
                
                foreach ($production->getProduct() as $product) {
                    //dump($product);
                    
                    $product_meta = $product->getMetaData();
                    //dd($product_meta);
                    //foreach ($product_meta[$provider] as $data) {
                    $page += $product_meta[$provider]['singn_in'];
                
                    $field = $product_meta[$provider]['accords'];
                    $field['page'] = $page;
                    $field['type'] = 'signature';
                    array_push($fields, $field);
                    
                    $page += ($product_meta[$provider]['nbr_page']-$product_meta[$provider]['singn_in']);
                    //}
                    //dump($product->getContract());
                    $contractPDF = $pdfGenerator->makePdf($product->getContract(), $client, 1);
                    dump($contractPDF);
                    # mise a jour des infos du contrats partie 1
                    $contract = new Contract();
                    $count++;
                    $contract->setPdfFile($contractPDF);
                    $contract->setProduct($product);
                    $contract->setProduction($production);

                    $entityManager->persist($contract);
                    $entityManager->flush();
    
                    array_push($contractList, $contractPDF);

                }
                $signatureRequestId = "Docusign";
                $contract->setSignatureId($signatureRequestId);
            }
            // envoie en signature (a tester), mise en place de l'ID yousigne dans l'entité Contacts...      
            //dd($contract);
            $entityManager->flush();
        }    
        //dump($count);    
        
        //die();
        return $this->redirectToRoute('app_contract_show', ['id' => $client_id], Response::HTTP_SEE_OTHER);
    }

    #[Route('/show/{id}', name: 'app_contract_show', methods: ['GET'])]
    public function show($id, EntityManagerInterface $entityManager): Response
    {
        $contracts = $entityManager->getRepository(Contract::class)->findContractsByClientId($id);

        return $this->render('contract/show.html.twig', [
            'contracts' => $contracts,
        ]);
    }

    #[Route('/{id}', name: 'app_contract_delete', methods: ['POST'])]
    public function delete(Request $request, Contract $contract, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contract->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($contract);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_contract_index', [], Response::HTTP_SEE_OTHER);
    }
}
