<?php

namespace App\Form;

use App\Entity\User;
use App\Utils\RegexPatterns;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $mode = $options['mode'];

        // Création pour l'admin: création d'utilisateur
        if ($mode === 'createUser') {
            $builder
                ->add('login', TextType::class, [
                    'label' => 'Nom utilisateur',
                    'required' => true,
                ])
                ->add('email', EmailType::class, [
                    'label' => 'Adresse e-mail',
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
            ;
        }

        // Création pour l'employé
        if ($mode === 'createClient') {
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
            ;
        }

        // Pour l'admin : modification de l'email de l'utilisateur
        if ($mode === 'updateUserByAdmin') {
            $builder
                ->add('login', TextType::class, [
                    'label' => 'Nom utilisateur',
                    'required' => true,
                ])
                ->add('email', EmailType::class, [
                    'label' => 'Adresse e-mail',
                    'required' => false,
                ]);
        }

        // Pour l'admin : modification de son email et de son mot de passe
        if ($mode === 'updateAdmin') {
            $builder
                ->add('email', EmailType::class, [
                    'label' => 'Adresse e-mail',
                    'required' => false,
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
                ]);
        }

        // Pour l'utilisateur : modification mot de passe
        if ($mode === 'updateUser') {
            $builder
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
                        new Assert\NotBlank(message: 'Le mot de passe est obligatoire.'),
                        new Assert\Length(
                            max: 255,
                            maxMessage: 'Le mot de passe ne peut pas dépasser {{ limit }} caractères.',
                        ),
                        new Assert\Regex(
                            pattern: RegexPatterns::PASSWORD,
                            message: 'Le mot de passe doit contenir au moins 12 caractères incluant une majuscule, une minuscule, un chiffre et un caractère spécial.',
                        ),
                    ],
                ]);
        }

        // Pour le client et l'employé
        if ($mode === 'updateClient') {
            $builder
                ->add('firstName', TextType::class, [
                    'label' => 'Prénom',
                    'required' => false,
                ])
                ->add('lastName', TextType::class, [
                    'label' => 'Nom',
                    'required' => false,
                ])
                ->add('phone', TextType::class, [
                    'label' => 'Téléphone',
                    'required' => false,
                ])
                ->add('address', TextType::class, [
                    'label' => 'Adresse',
                    'required' => false,
                ])
                ->add('zipCode', TextType::class, [
                    'label' => 'Code postal',
                    'required' => false,
                ])
                ->add('city', TextType::class, [
                    'label' => 'Ville',
                    'required' => false,
                ])
                ->add('email', EmailType::class, [
                    'label' => 'Adresse e-mail',
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
                ]);
        }

        if ($mode === 'updateClientByStaff') {
            $builder
                ->add('firstName', TextType::class, [
                    'label' => 'Prénom',
                    'required' => false,
                ])
                ->add('lastName', TextType::class, [
                    'label' => 'Nom',
                    'required' => false,
                ])
                ->add('phone', TextType::class, [
                    'label' => 'Téléphone',
                    'required' => false,
                ])
                ->add('address', TextType::class, [
                    'label' => 'Adresse',
                    'required' => false,
                ])
                ->add('zipCode', TextType::class, [
                    'label' => 'Code postal',
                    'required' => false,
                ])
                ->add('city', TextType::class, [
                    'label' => 'Ville',
                    'required' => false,
                ])
                ->add('email', EmailType::class, [
                    'label' => 'Adresse e-mail',
                    'required' => true,
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'mode' => null, // valeur par défaut
        ]);
    }
}
