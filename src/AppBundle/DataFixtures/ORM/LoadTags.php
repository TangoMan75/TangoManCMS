<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Tag;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadTags implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
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
        return 4;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        // Load Tags
        $tags = [
            'Défaut'       ,'default'     ,'default',
            'Principal'    ,'primary'     ,'primary',
            'Info'         ,'info'        ,'info',
            'Succès'       ,'success'     ,'success',
            'Alerte'       ,'warning'     ,'warning',
            'Danger'       ,'danger'      ,'danger',
            'Argus 360'    ,'argus360'    ,'success',
            'Article'      ,'post'        ,'primary',
            'Dailymotion'  ,'dailymotion' ,'danger',
            'Document'     ,'document'    ,'default',
            'Embeddable'   ,'embeddable'  ,'default',
            'En Avant'     ,'featured'    ,'primary',
            'Fichier'      ,'file'        ,'default',
            'Gist'         ,'gist'        ,'primary',
            'Image'        ,'image'       ,'success',
            'Lien'         ,'link'        ,'warning',
            'Photo'        ,'picture'     ,'success',
            'Publié'       ,'published'   ,'success',
            'Theta S'      ,'thetas'      ,'success',
            'Vidéo'        ,'video'       ,'danger',
            'Vimeo'        ,'vimeo'       ,'danger',
            'Youtube'      ,'youtube'     ,'danger',
        ];

        for ($i = 0; $i < count($tags); $i = $i + 3) {
            // findBy is the only working method in fixtures
            if (!$em->getRepository('AppBundle:Tag')->findBy(['name' => $tags[$i]])) {
                $tag = new Tag();
                $tag->setName($tags[$i])
                    ->setType($tags[$i+1])
                    ->setLabel($tags[$i+2])
                    ->setReadOnly();

                $em->persist($tag);
            }
        }

        $em->flush();
    }
}
