<?php

namespace AppBundle\Form\Admin;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class AdminEditMediaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::Class,
                [
                    'label' => 'Titre',
                ]
            )
            ->add(
                'text',
                TextareaType::Class,
                [
                    'label'    => 'Description',
                    'required' => false,
                ]
            )
            ->add(
                'link',
                TextType::Class,
                [
                    'label' => 'Lien',
                ]
            )
            ->add(
                'user',
                EntityType::class,
                [
                    'label'         => 'Auteur',
                    'class'         => 'AppBundle:User',
                    'placeholder'   => 'Selectionner un utilisateur',
                    'empty_data'    => null,
                    'multiple'      => false,
                    'expanded'      => false,
                    'required'      => false,
                    'query_builder' => function (EntityRepository $em) {
                        return $em->createQueryBuilder('u')
                            ->orderBy('u.username');
                    },
                ]
            )
            ->add(
                'page',
                EntityType::class,
                [
                    'label'         => 'Page',
                    'class'         => 'AppBundle:Page',
                    'placeholder'   => 'Selectionner une page',
                    'empty_data'    => null,
                    'multiple'      => false,
                    'expanded'      => false,
                    'required'      => false,
                    'query_builder' => function (EntityRepository $em) {
                        return $em->createQueryBuilder('p')
                            ->orderBy('p.title');
                    },
                ]
            )
            ->add(
                'published',
                CheckboxType::class,
                [
                    'label' => 'Publier',
                    'required' => false,
                ]
            )
            ->add(
                'tags',
                EntityType::class,
                [
                    'label'         => 'Étiquette',
                    'class'         => 'AppBundle:Tag',
                    'multiple'      => true,
                    'expanded'      => true,
                    'required'      => false,
                    'query_builder' => function (EntityRepository $em) {
                        return $em->createQueryBuilder('t');
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
                'data_class' => 'AppBundle\Entity\Media',
            ]
        );
    }

    public function getName()
    {
        return 'media';
    }
}
