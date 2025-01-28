<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\FileUploader;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;






#[Route('/product')]
final class ProductController extends AbstractController
{


    #[Route(name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $brochureFile = $form->get('image')->getData();

            
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                try {
                    $brochureFile->move('./public/uploads/products', $newFilename);
                } catch (FileException $e) {
                    throw new \Exception("Erreur lors de l'upload du fichier");
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $product->setImage($newFilename);
            }

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/api/{id}/add-custom-field', name: 'product_add_custom_field', methods: ['POST'])]
    public function addCustomField(Product $product, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['label'], $data['type'])) {
            return new JsonResponse(['error' => 'Données invalides.'], Response::HTTP_BAD_REQUEST);
        }

        // Récupérer les customFields existants et ajouter le nouveau champ
        $customFields = $product->getCustomFields() ?? [];
        $newField = [
            'id' => uniqid(), // Générer un ID unique pour la suppression
            'label' => $data['label'],
            'type' => $data['type'],
            'ctx' => $data['ctx'],
            'name' => $data['name']
        ];
        $customFields[] = $newField;

        // Sauvegarder en base
        $product->setCustomFields($customFields);
        $entityManager->persist($product);
        $entityManager->flush();

        return new JsonResponse($newField, Response::HTTP_CREATED);
    }


    #[Route('/api/{id}/delete-custom-field/{fieldId}', name: 'product_delete_custom_field', methods: ['DELETE'])]
    public function deleteCustomField(Product $product, string $fieldId, EntityManagerInterface $entityManager): JsonResponse
    {
        $customFields = $product->getCustomFields() ?? [];

        // Vérifier si le champ existe avant de le supprimer
        $customFields = array_values(array_filter($customFields, function ($field) use ($fieldId) {
            return isset($field['id']) && $field['id'] !== $fieldId;
        }));

        $product->setCustomFields($customFields);
        $entityManager->persist($product);
        $entityManager->flush();

        return new JsonResponse(['success' => true], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/api/{id}/custom-fields', name: 'api_product_custom_fields', methods: ['GET'])]
    public function getCustomFields(Product $product): JsonResponse
    {
        
       // Récupérer les champs personnalisés du produit
        $customFields = $product->getCustomFields();  // Supposons que customFields soit une chaîne JSON

        // Vérifier si customFields est une chaîne JSON et la décoder
        if (is_string($customFields)) {
            $customFields = json_decode($customFields, true);
        }

        // Si customFields est déjà un tableau, on le laisse tel quel
        $response = [
            'customFields' => $customFields ?: [],
        ];

        return new JsonResponse($response);
    }



}
