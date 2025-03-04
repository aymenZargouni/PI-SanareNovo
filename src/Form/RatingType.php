<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RatingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('score', ChoiceType::class, [
            'choices' => [
                '1 ⭐' => 1,
                '2 ⭐' => 2,
                '3 ⭐' => 3,
                '4 ⭐' => 4,
                '5 ⭐' => 5,
            ],
            'expanded' => true, // Afficher sous forme de boutons radio
            'multiple' => false,
            'label' => 'Notez cet article :'
        ]);
        

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
