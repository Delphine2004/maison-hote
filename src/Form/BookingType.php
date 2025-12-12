<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Client;
use App\Entity\Room;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startingDate', null, [
                'widget' => 'single_text',
            ])
            ->add('endingDate', null, [
                'widget' => 'single_text',
            ])
            ->add('totalAmount', MoneyType::class, [
                'currency'    => 'EUR',
                'scale'       => 2,
                'divisor'     => 100,  // Gestion automatique centimes - euros
                'required'    => true,
            ])
            ->add('status')
            ->add('createdAt', null, [
                'widget' => 'single_text',
            ])
            ->add('updatedAt', null, [
                'widget' => 'single_text',
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => 'id',
            ])
            ->add('room', EntityType::class, [
                'class' => Room::class,
                'choice_label' => 'id',
            ])
            ->add('createdBy', EntityType::class, [
                'class' => Client::class,
                'choice_label' => 'id',
            ])
            ->add('updatedByClient', EntityType::class, [
                'class' => Client::class,
                'choice_label' => 'id',
            ])
            ->add('updatedByUser', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
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
