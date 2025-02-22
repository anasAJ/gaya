<?php

// src/Form/ClientFilterType.php
namespace App\Form;

use App\Entity\Client;
use App\Entity\Phase;
use App\Entity\Source;
use App\Entity\Status;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name', TextType::class, [
                'required' => false,
                'label' => 'Prénom',
            ])
            ->add('last_name', TextType::class, [
                'required' => false,
                'label' => 'Nom',
            ])
            ->add('email', TextType::class, [
                'required' => false,
                'label' => 'Email',
            ])
            ->add('phone', TextType::class, [
                'required' => false,
                'label' => 'Téléphone',
            ])
            ->add('added_date', DateType::class, [
                'required' => false,
                'label' => 'Date d\'ajout',
                'widget' => 'single_text',
                'input' => 'datetime',
            ])
            ->add('added_date_to', DateType::class, [
                'required' => false,
                'label' => 'Date d\'ajout',
                'widget' => 'single_text',
                'input' => 'datetime',
            ])
            ->add('status', EntityType::class, [
                'class' => Status::class,
                'choice_label' => 'name',
                'label'=> "Le Statut associé",
                'attr'=>[
                    'id'=> 'client_status',
                ]
            ])
            ->add('phase', EntityType::class, [
                'class' => Phase::class,
                'choice_label' => 'name',
                'label'=> "La phase associée",
                'attr'=>[
                    'id'=> 'client_phase',
                ]
            ])
            ->add('source', EntityType::class, [
                'class' => Source::class,
                'choice_label' => 'name',
                'label'=> "La source associée",
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null, // Pas de classe associée à ce formulaire
            'csrf_protection' => false,
            'method' => 'GET',
        ]);
    }
}
