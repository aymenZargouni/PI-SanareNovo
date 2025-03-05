<?php

namespace App\Form;

use App\Entity\user;
use App\Entity\Patient;
use Symfony\Component\Form\AbstractType;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;

class PatientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullname')
            ->add('gender', ChoiceType::class, [
                'label' => 'Gender',
                'choices' => [
                    'Male' => 'Male',
                    'Female' => 'Female',
                ],
                'required' => true
            ])
            ->add('adress')
            ->add('email', EmailType::class, [
                'mapped' => false,
                'constraints' => [
                    new Assert\NotBlank(['message' => "L'email est obligatoire."]),
                    new Assert\Email(['message' => "Veuillez entrer une adresse email valide."]),
                ],
            ])
            ->add('password', PasswordType::class, [
                'mapped' => false,
                'constraints' => [
                    new Assert\NotBlank(['message' => "Password est obligatoire."]),
                    new Assert\Length([
                        'min' => 8,
                        'minMessage' => "Le mot de passe doit contenir au moins 8 caractères.",
                    ]),
                ]
                ])
                ->add('captcha', Recaptcha3Type::class, [
                    'constraints' => new Recaptcha3(),
                    'action_name' => 'patientRegister',
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Patient::class,
        ]);
    }
}
