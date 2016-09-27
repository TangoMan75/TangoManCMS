<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::Class, [
                'label' => 'Votre pseudo'
            ])
            ->add('email', EmailType::Class, [
                'label' => 'Votre email'
            ])
            ->add('password', RepeatedType::Class, [
                'type' => PasswordType::Class,
                'first_options'  => ['label' => 'Votre mot de passe'],
                'second_options' => ['label' => 'Confirmez votre mot de passe'],
            ])
//            ->add('token')
//            ->add('roles', ChoiceType::class, [
//                'multiple'  => true,
//                'choices'   => [
//                    'Administrateur'   => "ROLE_ADMIN",
//                    'Utilisateur' => "ROLE_PARTNER"
//                ],
//                'expanded'  => true
//            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User'
        ));
    }
}
