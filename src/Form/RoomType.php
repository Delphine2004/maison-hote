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
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Validator\Constraints\File;

class RoomType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {

        $mode = $options['mode'];

        if ($mode === 'update') {
            $builder
                ->add('name', TextType::class, [
                    'label' => 'Nom de la chambre',
                    'required' => false,
                ])
                ->add('capacity', IntegerType::class, [
                    'label' => 'Capacité de la chambre',
                    'required' => false,
                ])
                ->add('rate', MoneyType::class, [
                    'label' => 'Prix / nuit',
                    'currency'    => 'EUR',
                    'scale'       => 2,
                    'required'    => false,
                ])
                ->add('picture', FileType::class, [
                    'label' => 'Photo',
                    'mapped' => false,
                    'required' => false,
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
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Room::class,
            'mode' => null,
            'csrf_protection' => true,
        ]);
    }
}
