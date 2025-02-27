<?php

namespace App\Form;

use App\Entity\RendezVous;
use App\Entity\Patient;
use App\Entity\Medecin;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RendezvousType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateR', DateType::class, [
                'widget' => 'single_text',
                'label' => '📅 Date du rendez-vous'
            ])
            ->add('motif', TextType::class, [
                'label' => '📝 Motif du rendez-vous'
            ]);

            if ($options['is_update']) {
                $builder->add('statut', ChoiceType::class, [
                'choices' => [
                    '🟠 En attente' => 'En attente',
                    '✅ Confirmé' => 'Confirmé',
                    '❌ Annulé' => 'Annulé',
                    '🏥 Terminé' => 'Terminé'
                ],
                'label' => '🔖 Statut'
            ]); 
        }
        $builder
            ->add('patient', EntityType::class, [
                'class' => Patient::class,
                'choice_label' => 'fullname', // Supposons que Patient ait un champ nom
                'label' => '🧑‍⚕️ Fullname Patient',
                'data' => $options['patient'],
                'attr' => ['readonly' => true]
            ])
            ->add('medecin', EntityType::class, [
                'class' => Medecin::class,
                'choice_label' => 'fullname', // Supposons que Medecin ait un champ nom
                'label' => '👨‍⚕️ Médecin'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RendezVous::class,
            'is_update' => false,
            'patient' => null,
        ]);
    }
}
