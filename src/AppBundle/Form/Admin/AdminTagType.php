<?php

namespace AppBundle\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminTagType extends AbstractType
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
                ChoiceType::Class,
                [
                    'label'   => 'Type',
                    'choices' => [
                        'default' => 'default',
                        'primary' => 'primary',
                        'info'    => 'info',
                        'success' => 'success',
                        'warning' => 'warning',
                        'danger'  => 'danger',
                    ],
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
                'data_class' => 'AppBundle\Entity\Tag',
            ]
        );
    }

    public function getName()
    {
        return 'tag';
    }
}
