<?php

namespace App\Form;

use App\Entity\Consultation;
use App\Entity\Dossiermedicale;
use App\Entity\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ConsultationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('date', null, [
            'widget' => 'single_text',
        ])
        ->add('motif')
        ->add('typeconsultation', ChoiceType::class, [
            'choices'  => [
                '💻 En ligne' => 'En ligne',
                '🏥 Présentiel' => 'Présentiel',
            ],
            'expanded' => false, // Afficher en menu déroulant
            'multiple' => false,
            'placeholder' => 'Sélectionner un type de consultation',
        ])
        ->add('status', ChoiceType::class, [
            'choices'  => [
                '🟠 En attente' => 'En attente',
                '✅ Terminée' => 'Terminée',
            ],
            'expanded' => false,
            'multiple' => false,
            'placeholder' => 'Sélectionner un statut',
        ])
        ->add('dossiermedicale', EntityType::class, [
            'class' => Dossiermedicale::class,
            'choice_label' => 'ordonnance',
        ])
        ->add('nom_service', EntityType::class, [
            'class' => Service::class,
            'choice_label' => 'nom',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Consultation::class,
        ]);
    }
}