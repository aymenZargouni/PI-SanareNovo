<?php

namespace App\Form;

use App\Entity\Service;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('chef_service')
            ->add('nbr_salle', null, [
                'data' => 0,
                'attr' => ['readonly' => true], // Empêche l'édition côté frontend
                'disabled' => true, // Désactive l'édition du champ
            ])
            ->add('capacite')
            ->add('etat', ChoiceType::class, [
                'choices' => [
                    'vide' => 0 ,
                    'plein' => 1 ,
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'État',
            ])
        ;
    }
    

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }
}
