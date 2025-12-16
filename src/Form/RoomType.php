<?php

namespace App\Form;

use App\Entity\Room;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class RoomType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {

        $mode = $options['mode'];
        if ($mode === 'create') {
            $builder
                ->add('name', TextType::class, [
                    'label' => 'Nom de la chambre',
                    'required' => true,
                    'attr' => ['class' => 'form-control',],
                ])
                ->add('number', IntegerType::class, [
                    'label' => 'Numéro de la chambre',
                    'required' => true,
                    'attr' => ['class' => 'form-control',],
                ])
                ->add('picture', FileType::class, [
                    'label' => 'Photo',
                    'mapped' => false,
                    'required' => true,
                    'attr' => ['class' => 'form-control',],
                    'constraints' => [
                        new File([
                            'maxSize' => '10M', // ICI tu règles la taille
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                            ],
                            'mimeTypesMessage' => 'Merci d’uploader une image valide (jpeg, png, webp)',
                        ])
                    ],
                ])
                ->add('description', TextareaType::class, [
                    'label' => 'Description de la chambre',
                    'required' => true,
                    'attr' => ['class' => 'form-control',],
                ]);
        }

        if ($mode === 'update') {
            $builder
                ->add('name', TextType::class, [
                    'label' => 'Nom de la chambre',
                    'required' => false,
                    'attr' => ['class' => 'form-control',],
                ])
                ->add('picture', FileType::class, [
                    'label' => 'Photo',
                    'mapped' => false,
                    'required' => false,
                    'attr' => ['class' => 'form-control',],
                    'constraints' => [
                        new File([
                            'maxSize' => '10M',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                            ],
                            'mimeTypesMessage' => 'Merci d’uploader une image valide (jpeg, png, webp)',
                        ])
                    ],
                ])
                ->add('description', TextareaType::class, [
                    'label' => 'Description de la chambre',
                    'required' => false,
                    'attr' => ['class' => 'form-control',],
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Room::class,
            'mode' => 'create', // valeur par défaut
            'csrf_protection' => true,
        ]);
    }
}
