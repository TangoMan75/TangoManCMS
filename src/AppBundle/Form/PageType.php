<?php

namespace AppBundle\Form;

use Tiloweb\Base64Bundle\Form\Base64Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageType extends AbstractType
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
                'description',
                TextareaType::Class,
                [
                    'label' => 'Description',
                ]
            )
            ->add(
                'cover',
                Base64Type::class,
                [
                    'label'    => 'Couverture',
                    'required' => false,
                ]
            )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'AppBundle\Entity\User',
            ]
        );
    }

    public function getName()
    {
        return 'user';
    }
}
