<?php

namespace AppBundle\Form\Admin;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminEditPrivilegeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::Class,
                [
                    'label' => 'Nom',
                ]
            )
            ->add(
                'type',
                TextType::Class,
                [
                    'label'    => 'Type',
                    'required' => false,
                ]
            )
            ->add(
                'label',
                ChoiceType::Class,
                [
                    'label'   => 'Label',
                    'choices' => [
                        'Défaut'    => 'default',
                        'Principal' => 'primary',
                        'Info'      => 'info',
                        'Succès'    => 'success',
                        'Alerte'    => 'warning',
                        'Danger'    => 'danger',
                    ],
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
                'users',
                EntityType::class,
                [
                    'label'         => 'Utilisateurs',
                    'class'         => 'AppBundle:User',
                    'multiple'      => true,
                    'expanded'      => false,
                    'required'      => false,
                    'by_reference'  => false,
                    'query_builder' => function (EntityRepository $em) {
                        return $em->createQueryBuilder('user')
                            ->orderBy('user.username');
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
                'data_class' => 'AppBundle\Entity\Privilege',
            ]
        );
    }

    public function getName()
    {
        return 'privilege';
    }
}
