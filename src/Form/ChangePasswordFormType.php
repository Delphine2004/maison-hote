<?php

namespace App\Form;

use App\Utils\RegexPatterns;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Mot de passe',
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                ],
                'label' => false,
                'required' => true,
                'mapped' => false, // n'est pas mappé avec la bd car il sera hashé
                'constraints' => [
                    new Assert\NotBlank(message: 'Le mot de passe est obligatoire.'),
                    new Assert\Length(
                        max: 255,
                        maxMessage: 'Le mot de passe ne peut pas dépasser {{ limit }} caractères.',
                    ),
                    new Assert\Regex(
                        pattern: RegexPatterns::PASSWORD,
                        message: 'Le mot de passe doit contenir au moins 12 caractères incluant une majuscule, une minuscule, un chiffre et un caractère spécial.',
                    )
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
