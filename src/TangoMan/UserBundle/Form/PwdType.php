<?php

namespace TangoMan\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PwdType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'password',
                RepeatedType::Class,
                [
                    'type'           => PasswordType::Class,
                    'first_options'  => ['label' => 'Votre mot de passe'],
                    'second_options' => ['label' => 'Confirmez votre mot de passe'],
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
                'data_class' => 'TangoMan\UserBundle\Entity\User',
            ]
        );
    }

    public function getName()
    {
        return 'password';
    }
}
