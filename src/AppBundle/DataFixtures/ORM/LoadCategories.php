<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Category;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadCategories
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\DataFixtures\ORM
 */
class LoadCategories implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 50;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        $categories = [
            'Argus 360'                => 'argus360',
            'Article'                  => 'post',
            'Dailymotion'              => 'dailymotion',
            'Galerie'                  => 'gallery',
            'Document'                 => 'document',
            'Dossier'                  => 'folder',
            'Embed'                    => 'embed',
            'En Avant'                 => 'featured',
            'Fichier'                  => 'file',
            'Gist'                     => 'gist',
            'Image'                    => 'image',
            'Lien'                     => 'link',
            'Photo'                    => 'picture',
            'Publié'                   => 'published',
            'Theta S'                  => 'thetas',
            'Vidéo'                    => 'video',
            'Vimeo'                    => 'vimeo',
            'Youtube'                  => 'youtube',
            'Document Libre Office'    => 'ods',
            'Document PDF'             => 'pdf',
            'Document Word'            => 'doc',
            'Fichier Texte'            => 'txt',
            'Image GIF'                => 'gif',
            'Image JPEG'               => 'jpeg',
            'Image JPG'                => 'jpg',
            'Image PNG'                => 'png',
            'Présentation Power Point' => 'pptx',
            'Tableau CSV'              => 'csv',
            'Tableau Excel'            => 'xls',
            'Tableau Libre Office'     => 'odt',
        ];

        foreach ($categories as $name => $type) {
            $category = new Category();
            $category->setName($name)
                ->setType($type);

            $em->persist($category);
        }

        $em->flush();
    }
}
