<?php

namespace App\Form;

use App\Entity\Team;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('users', EntityType::class, [
                'class' => User::class,
                'choice_label' => function(User $user):string{
                    return $user->getFullName();
                }, 
                'multiple' => true, // Permet de sélectionner plusieurs utilisateurs
                'expanded' => true, // Affichage sous forme de checkboxes
                'by_reference' => false, // Important pour les relations bidirectionnelles
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Team::class,
            'csrf_protection' => false,
        ]);
    }
}
