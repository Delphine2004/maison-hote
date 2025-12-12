<?php

namespace App\Form;

use App\Entity\Period;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;


class PeriodType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $mode = $options['mode'];
        if ($mode === 'create') {
            $builder
                ->add('name', TextType::class, [
                    'label' => 'Nom de la période',
                    'required' => true,
                    'attr' => ['class' => 'form-control'],
                ])
                ->add('startingDate',  DateType::class, [
                    'label' => 'Date de début',
                    'required' => true,
                    'widget' => 'single_text',
                    'html5' => true,
                    'attr' => ['class' => 'form-control',],
                ])
                ->add('endingDate', DateType::class, [
                    'label' => 'Date de fin',
                    'required' => true,
                    'widget' => 'single_text',
                    'html5' => true,
                    'attr' => ['class' => 'form-control',],
                ])
            ;
        }

        if ($mode === 'update') {
            $builder
                ->add('name', TextType::class, [
                    'label' => 'Nom de la période',
                    'required' => false,
                    'attr' => ['class' => 'form-control'],
                ])
                ->add('startingDate',  DateType::class, [
                    'label' => 'Date de début',
                    'required' => false,
                    'widget' => 'single_text',
                    'html5' => true,
                    'attr' => ['class' => 'form-control',],
                ])
                ->add('endingDate', DateType::class, [
                    'label' => 'Date de fin',
                    'required' => false,
                    'widget' => 'single_text',
                    'html5' => true,
                    'attr' => ['class' => 'form-control',],
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Period::class,
            'mode' => 'create',
        ]);
    }
}
