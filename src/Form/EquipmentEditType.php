<?php

namespace App\Form;

use App\Entity\Equipment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
class EquipmentEditType extends AbstractType // form pour la mise à jour un équipeemnt
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'label' => 'Nom de l\'équipement',
            'attr' => ['placeholder' => 'Ex: Ordinateur portable']
        ])
        ->add('model', TextType::class, [
            'label' => 'Modèle',
            'attr' => ['placeholder' => 'Ex: Dell Latitude 5420']
        ])
       
        ->add('dateAchat', DateType::class, [
            'label' => 'Date d\'achat',
            'widget' => 'single_text',
        ])
        ->add('prix', MoneyType::class, [
            'label' => 'Prix',
            'currency' => 'EUR', 
        ])
      ;
}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipment::class,
        ]);
    }
}
