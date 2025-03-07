<?php

namespace App\Form;

use App\Entity\Consultation;
use App\Entity\Dossiermedicale;
use App\Entity\Service;
use App\Entity\Patient;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType as TypeEntityType;

class ConsultationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isUpdate = $options['updatee'] ?? false;

        $builder
        ->add('patient', TypeEntityType::class, [
            'class' => Patient::class,
            'choice_label' => 'fullname',
            'placeholder' => 'Sélectionner Le Nom&Prenom de Patient',
            'required' => true,
        ])
            ->add('date', null, [
                'widget' => 'single_text',
                'required' => !$isUpdate,
            ])
            ->add(child: 'motif')
            ->add('typeconsultation', ChoiceType::class, [
                'choices'  => [
                    '💻 En ligne' => 'En ligne',
                    '🏥 Présentiel' => 'Présentiel',
                ],
                'expanded' => false, 
                'multiple' => false,
                'placeholder' => 'Sélectionner un type de consultation',
            ]);

        
        if ($options['is_update']) {
            $builder->add('status', ChoiceType::class, [
                'choices'  => [
                    '🟠 En attente' => 'En attente',
                    '✅ Terminée' => 'Terminée',
                ],
                'expanded' => false,
                'multiple' => false,
                'placeholder' => 'Sélectionner un statut',
            ]);
        }

        $builder
            ->add('dossiermedicale', EntityType::class, [
                'class' => Dossiermedicale::class,
                'choice_label' => 'ordonnance',
                'placeholder' => 'Sélectionner un dossier médical',
                'required' => true,
            ])
            ->add('nom_service', EntityType::class, [
                'class' => Service::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionner un service',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Consultation::class,
            'is_update' => false, // Default: new consultation
        ]);
    }
}
