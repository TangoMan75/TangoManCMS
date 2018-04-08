<?php

/**
 * This file is part of the TangoManCMS package.
 *
 * Copyright (c) 2018 Matthias Morin <matthias.morin@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class NewPostType
 *
 * @author Matthias Morin <matthias.morin@gmail.com>
 */
class NewPostType extends AbstractType
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
                'text',
                TextareaType::Class,
                [
                    'label'    => 'Contenu',
                    'required' => false,
                ]
            )
            ->add(
                'published',
                CheckboxType::class,
                [
                    'label'    => 'Publier',
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
