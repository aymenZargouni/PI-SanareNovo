<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Medecin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints as Assert;

class MedecinAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullname')
            ->add('dateEmbauche', null, [
                'widget' => 'single_text',
                'data'=>new \DateTime(),
            ])
            ->add('specilite',ChoiceType::class,[
            'choices'=>[
                'Cardiologue' => 'Cardiologue',
                'Dermatologie' => 'Dermatologie',
                'Gastronentérologie'=>'Gastronentérologie',
                'Neurologie'=>'Neurologie',
                'Pédiatrie'=>'Pédiatrie',
                'Psychiatrie'=> 'Psychiatrie',
                'Chirugie'=>'Chirugie',
                'Ophtalmologie'=>'Ophtalmologie',
                'Néphrologie'=>'Néphrologie'
            ]])
            ->add('email', EmailType::class, [
                'mapped'=>false,
                'required'=>false,
                'constraints' => [
                    new Assert\NotBlank(['message' => "L'email est obligatoire."]),
                    new Assert\Email(['message' => "Veuillez entrer une adresse email valide."]),
                ],
            ])
            ->add('password', PasswordType::class, [
                'mapped' => false,
                'required'=>false,
                'constraints' => [
                    new Assert\NotBlank(['message' => "Le mot de passe est obligatoire."]),
                    new Assert\Length([
                        'min' => 8,
                        'minMessage' => "Le mot de passe doit contenir au moins {{ limit }} caractères.",
                    ]),
                ],
            ]);
        }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Medecin::class,
        ]);
    }
}
