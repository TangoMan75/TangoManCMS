<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditPostType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::Class,
                [
                    'label' => 'Titre',
                ]
            )
            ->add(
                'subtitle',
                TextType::Class,
                [
                    'label' => 'Sous-Titre',
                ]
            )
            ->add(
                'slug',
                TextType::Class,
                [
                    'label' => 'Slug',
                ]
            )
            ->add(
                'content',
                TextareaType::Class,
                [
                    'label'    => 'Contenu',
                    'required' => false,
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'AppBundle\Entity\Post',
            ]
        );
    }

    public function getName()
    {
        return 'post';
    }
}