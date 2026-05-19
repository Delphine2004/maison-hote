<?php

namespace App\Form;

use App\Entity\User;

use App\Utils\RegexPatterns;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
                'required' => true,
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
                'required' => true,
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'required' => true,
            ])
            ->add('zipCode', TextType::class, [
                'label' => 'Code postal',
                'required' => true,
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'required' => true,
            ])
            ->add('password', RepeatedType::class, [
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
                    new Assert\NotBlank(['message' => 'Le mot de passe est obligatoire.']),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'Le mot de passe ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                    new Assert\Regex([
                        'pattern' => RegexPatterns::PASSWORD,
                        'message' => 'Le mot de passe doit contenir au moins 12 caractères incluant une majuscule, une minuscule, un chiffre et un caractère spécial.',
                    ]),
                ],
            ])

            ->add('rgpdConsent', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter le traitement des données.'
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
