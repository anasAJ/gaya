<?php

namespace App\Form;

use App\Entity\Predictive;
use App\Entity\Source;
use App\Entity\Team;
use App\Entity\User;
use Doctrine\DBAL\Types\FloatType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;


class PredictiveType extends AbstractType
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('source', EntityType::class, [
                'class' => Source::class,
                'choice_label' => 'name',
                'label'=> "La source associé",
                
            ])
            /*->add('status_percent', NumberType::class, [
                'label' => 'Pourcentage atteint',
                'scale' => 2, // Nombre de décimales (ex: 10.50)
                'required' => true,
                'html5' => true, // Active l'input type="number" en HTML5
                'attr' => [
                    'step' => '0.01', // Permet les valeurs décimales
                    'min' => 0
                ]
            ])*/
            ->add('status', ChoiceType::class, [
                'choices'  => [
                    'En attente' => null,
                    'En cours' => 1,
                    'Términé' => 2,
                ],
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getFullName();
                },
                'required' => false, // Permettre un champ non obligatoire
                'label' => "L'utilisateur associé",
                'multiple' => true, // Permettre la sélection multiple
                'expanded' => true, // Si true, affiche sous forme de checkboxes, false = liste déroulante
                /*'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                              ->orderBy('u.fullName', 'ASC'); // Tri des utilisateurs par nom
                },*/
                'attr' => [
                    'data-placeholder' => 'Sélectionner un ou plusieurs utilisateurs', // Si tu utilises Select2 par exemple
                    'data-role' => 'user-select'
                ],
            ])
            ->add('team', ChoiceType::class, [
                'choices' => [
                    'Toutes les équipes' => 0, // Option personnalisée
                ],
                'label' => "Sélectionner une équipe",
                'required' => false,
                'expanded' => true, // Checkboxes
                'multiple' => true, // Sélection multiple
                'attr' => [
                    'data-placeholder' => 'Sélectionner une ou plusieurs équipes', // Si tu utilises Select2 par exemple
                    'data-role' => 'team-select'
                ],
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $form = $event->getForm();
                $teams = $this->entityManager->getRepository(Team::class)->findAll();
            
                $choices = [];
                foreach ($teams as $team) {
                    $choices[$team->getName()] = $team->getId();
                }
            
                // Mettre à jour le champ avec les choix des équipes
                $form->add('team', ChoiceType::class, [
                    'choices' => array_merge(['Toutes les équipes' => 0], $choices),
                    'label' => "Sélectionner une équipe",
                    'required' => false,
                    'expanded' => true, // Checkboxes
                    'multiple' => true, // Sélection multiple
                ]);
            })
            ->add('csv_file', FileType::class, [
                'label' => 'Importer un fichier CSV',
                'mapped' => false, // Le champ n'est pas lié à une entité
                'required' => true,
                'constraints' => [
                    new File([
                        'mimeTypes' => ['text/csv', 'text/plain', 'application/vnd.ms-excel'],
                        'mimeTypesMessage' => 'Veuillez télécharger un fichier CSV valide.',
                    ])
                ],
                'attr' => ['accept' => '.csv'] // Permet uniquement les fichiers CSV dans l'explorateur de fichiers
            ])
            ->add('repeated', CheckboxType::class, [
                'label' => "Garder les doublons?",
                'required' => false,
                'mapped' => false,
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Predictive::class,
        ]);
    }
}
