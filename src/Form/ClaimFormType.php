<?php

namespace App\Form;

use App\Entity\Claim;
use App\Entity\Technicien;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
        ->add('technicien', EntityType::class, [
            'class' => Technicien::class,
            'choice_label' => 'nom', // Affiche le nom du technicien dans la liste déroulante
            'placeholder' => 'Sélectionner un technicien',
            'required' => true,
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

