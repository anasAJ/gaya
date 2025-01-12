<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\Length;


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
                ]
                ])
            /*->add('password', PasswordType::class, [
                'label'=> 'pass',
                'attr'=>[
                    'placeholder'=> 'Le téléphone de votre prospect',
                ]
                ])*/
            ->add('password', RepeatedType::class, [
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
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
