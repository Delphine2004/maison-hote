<?php

namespace App\Form;

use App\DTO\SearchBooking;
use App\Enum\BookingStatus;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class SearchBookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', TextType::class, [
                'label' => 'Référence',
                'required' => false,
                'attr' => ['class' => 'form-control',],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom du client',
                'required' => false,
                'attr' => ['class' => 'form-control',],
            ])
            ->add('status', EnumType::class, [
                'class' => BookingStatus::class,
                'label' => 'Statut',
                'choice_label' => fn(BookingStatus $choice) => $choice->value,
                'placeholder' => 'Choisir un statut',
                'required' => false,
                'attr' => ['class' => 'form-select',],
            ])
            ->add('startingDate', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'label' => 'Date d\'arrivée',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('endingDate', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'label' => 'Date de départ',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('createdAt', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'label' => 'Date de création',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchBooking::class,
            'method' => 'GET',
        ]);
    }
}
