<?php

/*
 * This file is part of the TangoManCMS package.
 *
 * (c) Matthias Morin <matthias.morin@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\Form\Admin;

use AppBundle\Form\DataTransformer\RolesTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tiloweb\Base64Bundle\Form\Base64Type;

class AdminEditUserType extends AbstractType
{

    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * AdminEditUserType constructor.
     *
     * @param ObjectManager $manager
     */
    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

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
                'slug',
                TextType::Class,
                [
                    'label' => 'Slug',
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

        $builder->get('roles')->addModelTransformer(
            new RolesTransformer($this->manager)
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
