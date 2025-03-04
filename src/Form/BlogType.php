<?php

namespace App\Form;

use App\Entity\Blog;
use App\Entity\Category;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Form\Type\VichImageType;

class BlogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Title cannot be empty.']),
                    new Assert\Length([
                        'min' => 5,
                        'max' => 255,
                        'minMessage' => 'Title must be at least 5 characters long.',
                    ]),
                ],
            ])
            ->add('content', TextareaType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Content cannot be empty.']),
                    new Assert\Length([
                        'min' => 10,
                        'minMessage' => 'Content must be at least 10 characters long.',
                    ]),
                ],
            ])
            ->add('Category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true, // Checkboxes
                'attr' => [
                    'class' => 'custom-checkboxes',
                ],
                'constraints' => [
                    new Assert\Count([
                        'min' => 1,
                        'minMessage' => 'Veuillez sélectionner au moins une catégorie.',
                    ]),
                ],
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => $options['is_new'], // Image obligatoire uniquement à la création
                'label' => 'Upload Image',
                'allow_delete' => true,
                'download_uri' => true,
                'constraints' => $options['is_new'] ? [
                    new Assert\NotNull([
                        'message' => 'L\'image est obligatoire pour un nouveau blog.',
                    ]),
                    new Assert\File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG ou PNG).',
                    ]),
                ] : [],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Blog::class,
            'is_new' => false, // Par défaut, c'est une mise à jour
        ]);
    }
}
