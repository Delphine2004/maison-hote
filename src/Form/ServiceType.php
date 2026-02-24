<?php

namespace App\Form;

use App\Entity\Service;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $mode = $options['mode'];

        if ($mode === 'create') {
            $builder
                ->add('name', TextType::class, [
                    'label' => 'Nom du service',
                    'required' => true,
                    'attr' => ['class' => 'form-control'],
                ])
                ->add('price', TextType::class, [
                    'label' => 'Prix du service',
                    'required' => true,
                    'attr' => ['class' => 'form-control'],
                ])
                ->add('description', TextareaType::class, [
                    'label' => 'Description du service',
                    'required' => true,
                    'attr' => [
                        'class' => 'form-control',
                        'rows' => 5
                    ],
                ])
            ;
        }

        if ($mode === 'update') {
            $builder
                ->add('name', TextType::class, [
                    'label' => 'Nom du service',
                    'required' => false,
                    'attr' => ['class' => 'form-control'],
                ])
                ->add('price', TextType::class, [
                    'label' => 'Prix du service',
                    'required' => false,
                    'attr' => ['class' => 'form-control'],
                ])
                ->add('description', TextareaType::class, [
                    'label' => 'Description du service',
                    'required' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'rows' => 5
                    ],
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
            'mode' => 'create', // valeur par défaut
            'csrf_protection' => true,
        ]);
    }
}
