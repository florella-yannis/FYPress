<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegisterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        # Exo : Ajouter des contraintes de validation sur les champs email et password : contrainte de longueur et de champs vide.
        $builder
            ->add('email', EmailType::class, [
                'label' => "Choisissez un email",
                'constraints' => [
                    new NotBlank([
                        'message' =>'Ce champ ne peut etre vide : {{ value }}'
                    ]),
                    new Length([
                        'min' => 4,
                        'max' =>180,
                        'minMessage' =>'Votre email doit comporter au minimum {{ limit }} caractères.(email : {{ value }})',
                        'maxMessage' =>'Votre email doit comporter au maximum {{ limit }} caractères.(email : {{ value }})',
                    ]),
                ],
            ])
            ->add('password', PasswordType::class, [
                'label' => "Choisissez un mot de passe fort",
                'constraints' => [
                    new NotBlank([
                        'message' =>'Ce champ ne peut etre vide'
                    ]),
                    new Length([
                        'min' => 4,
                        'max' => 255,
                        'minMessage' =>'Votre email doit comporter au minimum {{ limit }} caractères.(email : {{ value }})',
                        'maxMessage' =>'Votre email doit comporter au maximum {{ limit }} caractères.(email : {{ value }})',
                    ]),
                ],
            ])
            ->add('firstname', TextType::class, [
                'label' => "Prénom",
                'constraints' => [
                    new NotBlank()
                ],
            ])
            ->add('lastname', TextType::class, [
                'label' => "Nom",
                'constraints' => [
                    new NotBlank()
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Je m'inscris",
                'validate' => false,
                'attr'=>[
                    'class' =>'d-block mx-auto my-3 col-4 btn btn-warning'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
