<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Product;
use App\Entity\Production;
use App\Entity\Signature;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => function(User $user): string{
                    return $user->getFullName();
                },
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => function(Client $client): string{
                    return $client->getFullName();
                },
            ])
            ->add('app_fees', TextType::class)
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'name',
                'attr' => [
                    'id' => 'productSelect',
                ],
                'expanded' => true,
                'multiple' => true,
            ])
            
            ->add('signature_provider', EntityType::class, [
                'class' => Signature::class,
                'choice_label' => 'provider',
                'expanded' => true,
                'multiple' => false
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Production::class,
            'csrf_protection' => false
        ]);
    }
}
