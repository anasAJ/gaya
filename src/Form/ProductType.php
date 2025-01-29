<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;


class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('image', FileType::class, [
                    'label' => 'Brochure (fichier image)',
                    'mapped' => false,
                    'required' => false,
                    'constraints' => [
                        new File([
                            'maxSize' => '1024k',
                            'mimeTypes' => [
                                'image/gif', 'image/png', 'image/jpeg', 'image/bmp', 'image/webp'
                            ],
                            'mimeTypesMessage' => 'Merci de choisir une image valide (image/gif, image/png, image/jpeg, image/bmp, image/webp)',
                        ])
                    ],
                ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
            ])
            ->add('remuneration', NumberType::class, [
                'scale' => 2, // Nombre de décimales
                'html5' => true, // Active le type "number" en HTML5
                'attr' => [
                    'step' => '0.01', // Définit le pas pour les valeurs décimales
                ],
            ])
            ->add('contract', FileType::class, [
                'label' => 'Contrat (fichier PDF)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'application/pdf'
                        ],
                        'mimeTypesMessage' => 'Merci de choisir un fichier PDF valide',
                    ])
                ],
            ])
            ->add('custom_fields', HiddenType::class, [
                'mapped' => false, 
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'csrf_protection' => false,
        ]);
    }
}
