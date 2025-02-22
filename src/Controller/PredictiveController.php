<?php

namespace App\Controller;

use App\Entity\Predictive;
use App\Form\PredictiveType;
use App\Repository\PredictiveRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Entity\Client;
use App\Entity\Adresses;
use League\Csv\Reader;

#[Route('/predictive')]
final class PredictiveController extends AbstractController
{
    #[Route(name: 'app_predictive_index', methods: ['GET'])]
    public function index(PredictiveRepository $predictiveRepository): Response
    {
        return $this->render('predictive/index.html.twig', [
            'predictives' => $predictiveRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_predictive_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, UserRepository $userRepository): Response
    {
        $predictive = new Predictive();
        $form = $this->createForm(PredictiveType::class, $predictive);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $predictive->setAddedDate(date('Y-m-d'));
            $predictive->setAddedTime(date('H:i:s'));
            $predictive->setStatusPercent(0);

            if($form->get('user')->getData()){
                $users = $form->get('user')->getData();
            }else{
                $teams = $form->get('taem')->getData();
                $users = $userRepository->getUsersByTeams($teams, $entityManager);
            }

            $userCount = count($users);
            $index = 0;

            
            $csv_file = $form->get('csv_file')->getData();
            if ($csv_file) {
                $originalFilename = pathinfo($csv_file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $csv_file->guessExtension();
            
                try {
                    $csv_file->move('./public/uploads/contacts', $newFilename);
                } catch (FileException $e) {
                    throw new \Exception("Erreur lors de l'upload du fichier");
                }
            
                // 📌 Lecture du fichier CSV et création des entités
                $filePath = './public/uploads/contacts/' . $newFilename;
            
                if (($handle = fopen($filePath, 'r')) !== false) {
                    $header = fgetcsv($handle, 1000, ","); // Lire la première ligne (titres des colonnes)
            
                    while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                        [$nom, $prenom, $indicative, $phone, $email, $adresse1, $adresse2, $ville, $codePostal1, $pays, $commentaire] = $data;
            
                        $repeated = $form->get('repeated')->getData();
                        // Vérifier si le client existe déjà
                        
                        if($repeated){
                            $client = new Client();
                            $client->setFirstName($nom);
                            $client->setLastName($prenom);
                            $client->setEmail($email);
                            $client->setIndicative($indicative);
                            $client->setPhone($phone);
                            $client->setComment($commentaire);

                            $assignedUser = $users[$index % $userCount];
                            $client->setUser($assignedUser);

                            $entityManager->persist($client);
                            // Ajouter les adresses si elles existent
                            if (!empty($adresse1)) {
                                $adresse = new Adresses();
                                $adresse->setAddress1($adresse1);
                                $adresse->setAddress2($adresse2);
                                $adresse->setCity($codePostal1);
                                $adresse->setZip($client);
                                $adresse->setCountry($pays);
                                $adresse->setClient($client);

                                $entityManager->persist($adresse);
                            }
                        }else{
                            $existingClient = $entityManager->getRepository(Client::class)->findOneBy(['email' => $email]);

                            if (!$existingClient) {
                                $client = new Client();
                                $client->setFirstName($nom);
                                $client->setLastName($prenom);
                                $client->setEmail($email);
                                $client->setIndicative($indicative);
                                $client->setPhone($phone);
                                $client->setComment($commentaire);

                                $assignedUser = $users[$index % $userCount];
                                $client->setUser($assignedUser);

                                $entityManager->persist($client);
                                // Ajouter les adresses si elles existent
                                if (!empty($adresse1)) {
                                    $adresse = new Adresses();
                                    $adresse->setAddress1($adresse1);
                                    $adresse->setAddress2($ville);
                                    $adresse->setCity($codePostal1);
                                    $adresse->setZip($client);
                                    $adresse->setCountry($client);
                                    $adresse->setClient($client);
                                    $entityManager->persist($adresse);
                                }
                            } 
                            
                        }
                        // Sauvegarder en base
                        $entityManager->flush();
                    }
            
                    fclose($handle);
                }
            }

            $entityManager->persist($predictive);
            $entityManager->flush();

            return $this->redirectToRoute('app_predictive_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('predictive/new.html.twig', [
            'predictive' => $predictive,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_predictive_show', methods: ['GET'])]
    public function show(Predictive $predictive): Response
    {
        return $this->render('predictive/show.html.twig', [
            'predictive' => $predictive,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_predictive_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Predictive $predictive, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PredictiveType::class, $predictive);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_predictive_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('predictive/edit.html.twig', [
            'predictive' => $predictive,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_predictive_delete', methods: ['POST'])]
    public function delete(Request $request, Predictive $predictive, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$predictive->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($predictive);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_predictive_index', [], Response::HTTP_SEE_OTHER);
    }
}
