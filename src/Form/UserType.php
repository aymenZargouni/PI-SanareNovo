<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Medecin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', EmailType::class)
        ->add('password', PasswordType::class)
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
