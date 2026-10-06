<?php

namespace App\Form;


use App\Entity\Room;
use App\DTO\SearchReviewDTO;

use App\Enum\ReviewStatus;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class SearchReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', TextType::class, [
                'label' => 'Référence',
                'required' => false,
            ])
            ->add('rate', TextType::class, [
                'label' => 'Note ',
                'required' => false,
            ])
            ->add('status', EnumType::class, [
                'class' => ReviewStatus::class,
                'label' => 'Statut',
                'choice_label' => fn(ReviewStatus $choice) => $choice->value,
                'placeholder' => 'Choisir un statut',
                'required' => false,
            ])
            ->add('createdAt', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'label' => 'Date de création',
                'required' => false,
            ])
            ->add('room', EntityType::class, [
                'class' => Room::class,
                'label' => 'Chambre',
                'choice_label' => 'name',
                'placeholder' => 'Aucune chambre',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchReviewDTO::class,
            'method' => 'GET',
        ]);
    }
}
