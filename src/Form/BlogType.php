<?php

namespace App\Form;

use App\Entity\Blog;
use App\Entity\Category;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints as Assert;

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
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'label' => 'Upload Image',
                'allow_delete' => true,
                'download_uri' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                        'mimeTypesMessage' => 'Please upload a valid image (JPEG, PNG, WEBP)',
                    ])
                ],
            ])

            ->add('Category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true, // mettez à false si vous voulez une liste déroulante au lieu de checkboxes
                'attr' => [
                    'class' => 'custom-checkboxes', // Classe CSS personnalisée
                ],
            ])
            ;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Blog::class,
        ]);
    }
}
