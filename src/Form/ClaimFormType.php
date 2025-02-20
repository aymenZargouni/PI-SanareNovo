<?php

namespace App\Form;

use App\Entity\Claim;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClaimFormType extends AbstractType // form pour la Réclamation
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('reclamation', TextType::class, [
            'label' => 'Description de la réclamation',
            'required' => true,
            'attr' => ['class' => 'form-control'],
        ])
      ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Claim::class,  
        ]);
    }
}

