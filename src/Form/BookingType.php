<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Room;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\IsTrue;


class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $mode = $options['mode'];

        // Création de la réservation 
        if ($mode === 'createBooking') {
            $builder
                ->add('bookingConsent', CheckboxType::class, [
                    'mapped' => false,
                    'constraints' => [
                        new IsTrue([
                            'message' => 'Vous devez accepter le traitement des données.'
                        ]),
                    ],
                ]);
        }
        // Chambre mise hors service
        if ($mode === 'blockRoom') {
            $builder
                ->add('room', EntityType::class, [
                    'class' => Room::class,
                    'choice_label' => 'name', // propriété affichée
                    'placeholder' => 'Choisir',
                    'label' => 'Chambre',
                    'required' => true,
                ])
                ->add('startingDate', DateType::class, [
                    'widget' => 'single_text',
                    'input' => 'datetime_immutable',
                    'label' => 'Date d\'arrivée',
                    'required' => true,
                ])
                ->add('endingDate', DateType::class, [
                    'widget' => 'single_text',
                    'input' => 'datetime_immutable',
                    'label' => 'Date de départ',
                    'required' => true,
                ]);
        }

        // Modification des dates
        if ($mode === 'updateBookingPeriod') {
            $builder
                ->add('startingDate', DateType::class, [
                    'widget' => 'single_text',
                    'input' => 'datetime_immutable',
                    'label' => 'Nouvel date d\'arrivée',
                    'required' => false,
                ])
                ->add('endingDate', DateType::class, [
                    'widget' => 'single_text',
                    'input' => 'datetime_immutable',
                    'label' => 'Nouvelle date de départ',
                    'required' => false,
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
            'mode' => null,
        ]);
    }
}
