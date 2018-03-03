<?php

namespace AppBundle\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AdminEditCommentType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'post',
                EntityType::class,
                [
                    'label'         => 'Article',
                    'class'         => 'AppBundle:Post',
                    'query_builder' => function (EntityRepository $em) {
                        return $em->createQueryBuilder('p')
                                  ->orderBy('p.title');
                    },
                ]
            )
            ->add(
                'text',
                TextareaType::class,
                [
                    'label'    => 'Commentaire',
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
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'AppBundle\Entity\Comment',
            ]
        );
    }

    public function getName()
    {
        return 'comment';
    }
}
