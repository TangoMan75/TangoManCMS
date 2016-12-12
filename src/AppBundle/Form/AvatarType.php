<?php

namespace AppBundle\Form;


use Tiloweb\Base64Bundle\Form\Base64Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AvatarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add("avatar", Base64Type::class, [
            'label' => 'Votre avatar',
            'required' => false
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\User'
        ]);
    }

    public function getName()
    {
        return 'avatar';
    }
}
