<?php

/**
 * This file is part of the TangoManCMS package.
 *
 * Copyright (c) 2018 Matthias Morin <matthias.morin@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\Form\Admin;

use Doctrine\ORM\EntityRepository;
use Tiloweb\Base64Bundle\Form\Base64Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AdminNewUserType
 *
 * @author Matthias Morin <matthias.morin@gmail.com>
 */
class AdminNewUserType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'avatar',
                Base64Type::class,
                [
                    'label'    => 'Avatar',
                    'required' => false,
                ]
            )
            ->add(
                'username',
                TextType::Class,
                [
                    'label' => 'Pseudo',
                ]
            )
            ->add(
                'email',
                EmailType::Class,
                [
                    'label' => 'Email',
                ]
            )
            ->add(
                'bio',
                TextareaType::Class,
                [
                    'label'    => 'Biographie',
                    'required' => false,
                ]
            )
            ->add(
                'password',
                PasswordType::Class,
                [
                    'label' => 'Mot de passe',
                ]
            )
            ->add(
                'roles',
                EntityType::class,
                [
                    'label'         => 'Roles',
                    'class'         => 'AppBundle:Role',
                    'multiple'      => true,
                    'expanded'      => false,
                    'required'      => false,
                    'by_reference'  => false,
                    'query_builder' => function (EntityRepository $em) {
                        return $em->createQueryBuilder('role');
                    },
                ]
            )
            ->add(
                'privileges',
                EntityType::class,
                [
                    'label'         => 'Privileges',
                    'class'         => 'AppBundle:Privilege',
                    'multiple'      => true,
                    'expanded'      => true,
                    'required'      => false,
                    'by_reference'  => false,
                    'query_builder' => function (EntityRepository $em) {
                        return $em->createQueryBuilder('privilege');
                    },
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
                'data_class' => 'AppBundle\Entity\User',
            ]
        );
    }

    public function getName()
    {
        return 'user';
    }
}
