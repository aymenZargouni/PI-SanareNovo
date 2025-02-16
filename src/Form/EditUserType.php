<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Medecin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class EditUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', EmailType::class, [
            'constraints' => [
                new Assert\NotBlank(['message' => "L'email est obligatoire."]),
                new Assert\Email(['message' => "Veuillez entrer une adresse email valide."]),
            ],
        ])
        ->add('password', PasswordType::class, [
            'constraints' => [
                new Assert\Length([
                    'min' => 8,
                    'minMessage' => "Le mot de passe doit contenir au moins 8 caractères.",
                ]),
            ]
        ])
        ->add('roles', ChoiceType::class, [
            'choices' => [
                'Coordinateur' => 'ROLE_COORDINATEUR',
                'RH' => 'ROLE_RH',
                'Medecin' => 'ROLE_MEDECIN',
                'Admin' => 'ROLE_ADMIN',
                'Technicien' => 'ROLE_TECHNICIEN'
            ],
        'multiple' => true,
        'expanded' => false,
    ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
