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
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom du client',
                'required' => false,
            ])
            ->add('status', EnumType::class, [
                'class' => BookingStatus::class,
                'label' => 'Statut',
                'choice_label' => fn(BookingStatus $choice) => $choice->value,
                'placeholder' => 'Choisir un statut',
                'required' => false,
            ])
            ->add('startingDate', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'label' => 'Date d\'arrivée',
                'required' => false,
            ])
            ->add('endingDate', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'label' => 'Date de départ',
                'required' => false,
            ])
            ->add('createdAt', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'label' => 'Date de création',
                'required' => false,
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
