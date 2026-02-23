<?php

namespace App\Form;

use App\Entity\User;
use App\Enum\UserRole;
use App\Utils\RegexPatterns;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $mode = $options['mode'];

        // Pour l'admin: création d'utilisateur
        if ($mode === 'createUser') {
            $builder
                ->add('login', TextType::class, [
                    'label' => 'Nom utilisateur',
                    'required' => true,
                    'attr' => ['class' => 'form-control'],
                ])
                ->add('email', EmailType::class, [
                    'label' => 'Adresse e-mail',
                    'required' => true,
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'exemple@email.com'
                    ],
                ])
                ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'first_options' => [
                        'label' => 'Mot de passe',
                        'attr' => ['class' => 'form-control'],
                    ],
                    'second_options' => [
                        'label' => 'Confirmer le mot de passe',
                        'attr' => ['class' => 'form-control'],
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

        // Pour l'employé et le visiteur
        if ($mode === 'createClient') {
            $builder

                ->add('firstName', TextType::class, [
                    'label' => 'Prénom',
                    'required' => true,
                    'attr' => ['class' => 'form-control'],
                ])
                ->add('lastName', TextType::class, [
                    'label' => 'Nom',
                    'required' => true,
                    'attr' => ['class' => 'form-control'],
                ])
                ->add('email', EmailType::class, [
                    'label' => 'Adresse e-mail',
                    'required' => true,
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'exemple@email.com'
                    ],
                ])
                ->add('phone', TextType::class, [
                    'label' => 'Téléphone',
                    'required' => true,
                    'attr' => ['class' => 'form-control'],
                ])
                ->add('address', TextType::class, [
                    'label' => 'Adresse',
                    'required' => true,
                    'attr' => ['class' => 'form-control'],
                ])
                ->add('zipCode', TextType::class, [
                    'label' => 'Code postal',
                    'required' => true,
                    'attr' => ['class' => 'form-control'],
                ])
                ->add('city', TextType::class, [
                    'label' => 'Ville',
                    'required' => true,
                    'attr' => ['class' => 'form-control'],
                ])
                ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'first_options' => [
                        'label' => 'Mot de passe',
                        'attr' => ['class' => 'form-control'],
                    ],
                    'second_options' => [
                        'label' => 'Confirmer le mot de passe',
                        'attr' => ['class' => 'form-control'],
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

        // Pour l'admin : modification de l'email de l'utilisateur
        if ($mode === 'updateUserByAdmin') {
            $builder
                ->add('login', TextType::class, [
                    'label' => 'Nom utilisateur',
                    'required' => true,
                    'attr' => ['class' => 'form-control'],
                ])
                ->add('email', EmailType::class, [
                    'label' => 'Adresse e-mail',
                    'required' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'exemple@exemple.com'
                    ],
                ]);
        }

        // Pour l'admin : modification de son email et de son mot de passe
        if ($mode === 'updateAdmin') {
            $builder
                ->add('email', EmailType::class, [
                    'label' => 'Adresse e-mail',
                    'required' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'exemple@email.com'
                    ],
                ])
                ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'first_options' => [
                        'label' => 'Mot de passe',
                        'attr' => ['class' => 'form-control'],
                    ],
                    'second_options' => [
                        'label' => 'Confirmer le mot de passe',
                        'attr' => ['class' => 'form-control'],
                    ],
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
                        'attr' => ['class' => 'form-control'],
                    ],
                    'second_options' => [
                        'label' => 'Confirmer le mot de passe',
                        'attr' => ['class' => 'form-control'],
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
                    'attr' => ['class' => 'form-control'],
                ])
                ->add('lastName', TextType::class, [
                    'label' => 'Nom',
                    'required' => false,
                    'attr' => ['class' => 'form-control'],
                ])
                ->add('phone', TextType::class, [
                    'label' => 'Téléphone',
                    'required' => false,
                    'attr' => ['class' => 'form-control'],
                ])
                ->add('address', TextType::class, [
                    'label' => 'Adresse',
                    'required' => false,
                    'attr' => ['class' => 'form-control'],
                ])
                ->add('zipCode', TextType::class, [
                    'label' => 'Code postal',
                    'required' => false,
                    'attr' => ['class' => 'form-control'],
                ])
                ->add('city', TextType::class, [
                    'label' => 'Ville',
                    'required' => false,
                    'attr' => ['class' => 'form-control'],
                ])
                ->add('email', EmailType::class, [
                    'label' => 'Adresse e-mail',
                    'required' => true,
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'exemple@email.com'
                    ],
                ])
                ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'first_options' => [
                        'label' => 'Mot de passe',
                        'attr' => ['class' => 'form-control'],
                    ],
                    'second_options' => [
                        'label' => 'Confirmer le mot de passe',
                        'attr' => ['class' => 'form-control'],
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
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'mode' => 'create', // valeur par défaut
        ]);
    }
}
