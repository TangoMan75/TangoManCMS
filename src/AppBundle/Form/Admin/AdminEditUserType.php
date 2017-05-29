<?php

namespace AppBundle\Form\Admin;

use Tiloweb\Base64Bundle\Form\Base64Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminEditUserType extends AbstractType
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
