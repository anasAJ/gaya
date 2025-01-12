<?php

namespace App\Form;

use App\Entity\Adresses;
use App\Entity\Client;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdressesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $clientId = $options['client_id'];
        //dd($clientId);
        $builder
            ->add('designation', TextType::class)
            ->add('address_1', TextType::class)
            ->add('adress_2', TextType::class)
            ->add('city', TextType::class)
            ->add('zip', NumberType::class)
            ->add('country', CountryType::class)
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => function(Client $client){
                    return $client->getFirstNAme().' '.$client->getLastName();
                },
                'query_builder' => function(EntityRepository $er) use ($clientId) {
                    return $er->createQueryBuilder('c')
                            ->where('c.id = :id')
                            ->setParameter('id', $clientId); 
                }
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Adresses::class,
            'client_id' => Client::class
        ]);
    }
}
