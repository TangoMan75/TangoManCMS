<?php

namespace AppBundle\Form;

use Tiloweb\Base64Bundle\Form\Base64Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("avatar", Base64Type::class, [
                'label' => 'Votre avatar',
                'required' => false
            ])
            ->add('username', TextType::Class, [
                'label' => 'Votre pseudo'
            ])
            ->add('email', RepeatedType::Class, [
                'type' => EmailType::Class,
                'first_options'  => ['label' => 'Votre email'],
                'second_options' => ['label' => 'Confirmez votre email']
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\User'
        ]);
    }

    public function getName()
    {
        return 'user';
    }
}
