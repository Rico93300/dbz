<?php

namespace App\Form;

use App\Entity\Race;
use App\Entity\Personnage;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PersonnageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('pouvoir')
            ->add('image', FileType::class, [
                'data_class' => null,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([

                        'maxSize' => '1M',
                        'mimeTypes' => ['image/jpeg', 'image/png'],

                    ]),
                ],
            ])
           
            
            ->add('race', EntityType::class, [
                'class' => Race::class,
                'choice_label' => 'name',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Personnage::class,
        ]);
    }
}
