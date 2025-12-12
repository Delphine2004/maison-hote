<?php

namespace App\Form;

use App\Entity\Period;
use App\Entity\Rate;
use App\Entity\Room;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class RateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $mode = $options['mode'];

        if ($mode === 'create') {
            $builder
                ->add('amount', MoneyType::class, [
                    'label' => 'Prix / nuit',
                    'currency'    => 'EUR',
                    'scale'       => 2,
                    'divisor'     => 100,  // Gestion automatique centimes - euros
                    'required'    => true,
                    'attr' => ['class' => 'form-control'],
                ])
                ->add('room', EntityType::class, [
                    'class' => Room::class,
                    'label' => 'Chambre',
                    'choice_label' => 'name',
                    'required'    => true,
                    'attr' => ['class' => 'form-control'],
                ])
                ->add('period', EntityType::class, [
                    'class' => Period::class,
                    'label' => 'Période',
                    'choice_label' => 'name',
                    'required'    => true,
                    'attr' => ['class' => 'form-control'],
                ])
            ;
        }

        if ($mode === 'update') {
            $builder
                ->add('amount', MoneyType::class, [
                    'label' => 'Prix / nuit',
                    'currency'    => 'EUR',
                    'scale'       => 2,
                    'divisor'     => 100,  // Gestion automatique centimes - euros
                    'required'    => false,
                    'attr' => ['class' => 'form-control'],
                ])
                ->add('room', EntityType::class, [
                    'class' => Room::class,
                    'choice_label' => 'id',
                    'required'    => false,
                    'attr' => ['class' => 'form-control'],
                ])
                ->add('period', EntityType::class, [
                    'class' => Period::class,
                    'choice_label' => 'id',
                    'required'    => false,
                    'attr' => ['class' => 'form-control'],
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rate::class,
            'mode' => 'create',
        ]);
    }
}
