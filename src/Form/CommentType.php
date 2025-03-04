<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        
        ->add('content', TextareaType::class, [
            'label' => 'Votre commentaire',
            'constraints' => [
                new Assert\NotBlank(['message' => 'Le commentaire ne peut pas être vide.']),
                new Assert\Length([
                    'min' => 3,
                    'max' => 1000,
                    'minMessage' => 'Le commentaire doit contenir au moins {{ limit }} caractères.',
                    'maxMessage' => 'Le commentaire ne peut pas dépasser {{ limit }} caractères.'
                ])
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
