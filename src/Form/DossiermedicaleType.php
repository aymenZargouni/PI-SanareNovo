<?php

namespace App\Form;

use App\Entity\Dossiermedicale;
use App\Entity\Consultation;
use App\Entity\Patient;
use App\Form\EntityType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType as TypeEntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class DossiermedicaleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('patient', TypeEntityType::class, [
            'class' => Patient::class,
            'choice_label' => 'fullname',
            'placeholder' => 'Sélectionner Le Nom&Prenom de Patient',
            'required' => true,
        ])
            ->add('imc')
            ->add('date', null, [
                'widget' => 'single_text',
            ])
            ->add('observations')
            ->add('ordonnance')
            ->add('consultations', TypeEntityType::class, [
                'class' => Consultation::class,
                'choice_label' => function (Consultation $consultation) {
                    return $consultation->getId() . ' - ' . $consultation->getMotif();
                },
                'multiple' => true,  
                'expanded' => true,  
                'by_reference' => false, 
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dossiermedicale::class,
        ]);
    }
}
