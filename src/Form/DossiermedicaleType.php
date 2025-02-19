<?php

namespace App\Form;

use App\Entity\Dossiermedicale;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DossiermedicaleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imc')
            ->add('date', null, [
                'widget' => 'single_text'
            ])
            ->add('observations')
            ->add('ordonnance')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dossiermedicale::class,
        ]);
    }
}
