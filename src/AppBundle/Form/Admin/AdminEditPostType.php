<?php

namespace AppBundle\Form\Admin;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class AdminEditPostType extends AbstractType
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
                'slug',
                TextType::Class,
                [
                    'label'    => 'Slug',
                    'required' => false,
                ]
            )
            ->add(
                'imageFile',
                VichImageType::class,
                [
                    'label'         => 'Image de couverture',
                    'allow_delete'  => true,
                    'download_link' => true,
                    'required'      => false,
                ]
            )
            ->add(
                'sections',
                EntityType::class,
                [
                    'label'         => 'Section',
                    'class'         => 'AppBundle:Section',
                    'placeholder'   => 'Selectionner une section',
                    'by_reference'  => false,
                    // 'empty_data'    => null,
                    'multiple'      => true,
                    'expanded'      => false,
                    'required'      => false,
                    'query_builder' => function (EntityRepository $em) {
                        return $em->createQueryBuilder('s')
                                  ->orderBy('s.title');
                    },
                ]
            )
            ->add(
                'tags',
                EntityType::class,
                [
                    'label'         => 'Étiquette',
                    'class'         => 'AppBundle:Tag',
                    // 'empty_data'    => null,
                    'multiple'      => true,
                    'expanded'      => true,
                    'required'      => false,
                    'query_builder' => function (EntityRepository $em) {
                        return $em->createQueryBuilder('t');
                    },
                ]
            )
            ->add(
                'text',
                TextareaType::Class,
                [
                    'label'    => 'Contenu',
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
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'AppBundle\Entity\Post',
            ]
        );
    }

    public function getName()
    {
        return 'post';
    }
}
