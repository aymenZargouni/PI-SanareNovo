<?php

namespace App\Form;

use App\Entity\Salle;
use App\Entity\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SalleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type')
            ->add('etat', ChoiceType::class, [
                'choices' => [
                    'Libre' => 0 ,
                    'Réservé' => 1 ,
                ],
                'expanded' => true, // Displays as radio buttons
                'multiple' => false, // Ensures only one option can be selected
                'label' => 'État',
            ])
            ->add('service', EntityType::class, [
                'class' => Service::class,
                'choice_label' => 'id',
                'label' => 'Service',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Salle::class,
        ]);
    }
}
