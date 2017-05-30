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
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class AdminNewMediaType extends AbstractType
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
                'subtitle',
                TextType::Class,
                [
                    'label'    => 'Sous-titre',
                    'required' => false,
                ]
            )
            ->add(
                'imageFile',
                VichImageType::class,
                [
                    'label'         => 'Image',
                    'required'      => false,
                    'allow_delete'  => false,
                    'download_link' => false,
                ]
            )
            ->add(
                'documentFile',
                VichFileType::class,
                [
                    'label'         => 'Document',
                    'required'      => false,
                    'allow_delete'  => false,
                    'download_link' => false,
                ]
            )
            ->add(
                'link',
                TextType::Class,
                [
                    'label'    => 'Lien',
                    'required' => false,
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
                'sections',
                EntityType::class,
                [
                    'label'         => 'Sections',
                    'class'         => 'AppBundle:Section',
                    'placeholder'   => 'Selectionner une section',
                    'by_reference'  => false,
                    'empty_data'    => null,
                    'multiple'      => false,
                    'expanded'      => false,
                    'required'      => false,
                    'query_builder' => function (EntityRepository $em) {
                        return $em->createQueryBuilder('s')
                            ->orderBy('s.title');
                    },
                ]
            )
            ->add(
                'published',
                CheckboxType::class,
                [
                    'label'    => 'Publier',
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
                'data_class' => 'AppBundle\Entity\Post',
            ]
        );
    }

    public function getName()
    {
        return 'media';
    }
}
