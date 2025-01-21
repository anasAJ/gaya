<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('first_name', TextType::class, [
                'label'=> 'Nom',
                'attr'=>[
                    'placeholder'=> 'Le nom du prospect',
                ]
                ])
            ->add('last_name', TextType::class, [
                'label'=> 'Prénom',
                'attr'=>[
                    'placeholder'=> 'Le prénom du prospect',
                ]
                ])
            ->add('phone', TelType::class, [
                'label'=> 'Téléphone',
                'attr'=>[
                    'placeholder'=> 'Le téléphone de votre prospect',
                ]
                ])
            ->add('categories', EntityType::class, [
                    'class' => Category::class,
                    'choice_label' => 'name', // Affiche le nom des catégories
                    'multiple' => true, // Permet la sélection multiple
                    'expanded' => true, // Change en checkboxes (mettre `false` pour une liste déroulante)
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')->orderBy('c.name', 'ASC');
                    },
                    'by_reference' => false, // Important pour ManyToMany
                ])
            /*->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'constraints' => [
                    new Length([
                        'min' => 4,
                        'max' => 30,
                    ])
                    ],
                    'first_options' => ['label' => 'Mot de passe'],
                    'second_options' => ['label' => 'Confirmer le mot de passe'],
                    'invalid_message' => 'Les mots de passe doivent correspondre.',
            ])*/
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $user = $event->getData();
        
            // Si l'utilisateur existe déjà (donc en modification)
            if (!$user || null === $user->getId()) { // Si c'est une création, ajouter le champ password
                $form->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'first_options' => ['label' => 'Mot de passe'],
                    'second_options' => ['label' => 'Confirmer le mot de passe'],
                    'invalid_message' => 'Les mots de passe doivent correspondre.',
                    'constraints' => [
                        new NotBlank(),
                        new Length(['min' => 4, 'max' => 30]),
                    ],
                    'mapped' => true, // Permet d'enregistrer la valeur dans l'entité
                ]);
            }
            if (!$user || null === $user->getId()) { // Si c'est une création, ajouter le champ password
                $form->add('email', EmailType::class, [
                    'label'=> 'Email',
                    'attr'=>[
                        'placeholder'=> "L'email de votre prospect",
                    ]
                ]);
            }else{
                $form->add('email', EmailType::class, [
                    'label'=> 'Email',
                    'attr'=>[
                        'placeholder'=> "L'email de votre prospect",
                        'readonly' => true,
                    ]
                ]);
            }

        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
