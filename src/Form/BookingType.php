<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Room;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startingDate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Arrivée',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('endingDate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Départ',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('totalAmount', MoneyType::class, [
                'label' => 'Montant total',
                'currency'    => 'EUR',
                'scale'       => 2,
                'divisor'     => 100,  // Gestion automatique centimes - euros
                'required'    => true,
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'label' => 'Client',
                'choice_label' => 'name',
                'required' => true,
                'attr' => ['class' => 'form-control', 'data-controller' => 'autocomplete'],
            ])
            ->add('room', EntityType::class, [
                'class' => Room::class,
                'label' => 'Chambre',
                'choice_label' => 'name',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
