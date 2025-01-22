<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Phase;
use App\Entity\Source;
use App\Entity\Status;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
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
            ->add('email', EmailType::class, [
                'label'=> 'Email',
                'attr'=>[
                    'placeholder'=> "L'email de votre prospect",
                ]
                ])
            ->add('phone', TelType::class, [
                'label'=> 'Téléphone',
                'attr'=>[
                    'placeholder'=> 'Le téléphone de votre prospect',
                ]])
            ->add('added_date', TextType::class,[
                'attr'=>[
                    'readonly'=>true
                ]
            ])
            ->add('added_time', TextType::class,[
                'attr'=>[
                    'readonly'=>true
                ]
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getFirstName() . ' ' . $user->getLastName(); // Combinez les champs
                },
                'required' => true, // Ou false, selon vos besoins
                'label'=> "L'utilisateur associé",
            ])
            ->add('status', EntityType::class, [
                'class' => Status::class,
                'choice_label' => 'name',
                'label'=> "L'utilisateur associé",
                'attr'=>[
                    'id'=> 'client_status',
                ]
            ])
            ->add('phase', EntityType::class, [
                'class' => Phase::class,
                'choice_label' => 'name',
                'label'=> "L'utilisateur associé",
                'attr'=>[
                    'id'=> 'client_phase',
                ]
            ])
            ->add('source', EntityType::class, [
                'class' => Source::class,
                'choice_label' => 'name',
                'label'=> "La source associé",
                'attr'=>[
                    'readonly'=> true,
                ]
                
            ])
            /*->add('submit', SubmitType::class,[
                'label' =>'Valider',
                'attr'=>[
                    'class'=>'btn btn-primary me-2'
                ]
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
            'csrf_protection' => false,
            //'csrf_field_name' => '_csrf_token',
            //'csrf_token_id'   => 'form_intention',
        ]);
    }
}
