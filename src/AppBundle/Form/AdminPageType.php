<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class AdminPageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'id',
                IntegerType::Class,
                [
                    'label' => 'Id',
                ]
            )
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
                    'label' => 'Slug',
                ]
            )
            ->add(
                'sections',
                EntityType::class,
                [
                    'label'         => 'Sections',
                    'class'         => 'AppBundle:Page',
                    'multiple'      => true,
                    'expanded'      => false,
                    'required'      => false,
                    'query_builder' => function (EntityRepository $pageManager) {
                        return $pageManager->createQueryBuilder('p')
                            ->join('p.sections', 'sections');
                    },
                ]
            )
            ->add(
                'tags',
                EntityType::class,
                [
                    'label'         => 'Étiquette',
                    'class'         => 'AppBundle:Tag',
                    'multiple'      => true,
                    'expanded'      => false,
                    'required'      => false,
                    'query_builder' => function (EntityRepository $itemManager) {
                        return $itemManager->createQueryBuilder('t')
                            ->join('t.items', 'items');
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
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'AppBundle\Entity\Page',
            ]
        );
    }

    public function getName()
    {
        return 'page';
    }
}