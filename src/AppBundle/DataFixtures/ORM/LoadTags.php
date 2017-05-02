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
            'Article'      ,'post'        ,'primary',
            'Gist'         ,'gist'        ,'primary',
            'Document'     ,'document'    ,'default',
            'Embeddable'   ,'embeddable'  ,'default',
            'Fichier'      ,'file'        ,'default',
            'Image'        ,'image'       ,'success',
            'Photo'        ,'picture'     ,'success',
            'Theta S'      ,'thetas'      ,'success',
            'Argus 360'    ,'argus360'    ,'success',
            'Lien'         ,'link'        ,'warning',
            'Vidéo'        ,'video'       ,'danger',
            'Youtube'      ,'youtube'     ,'danger',
            'Vimeo'        ,'vimeo'       ,'danger',
            'Daily Motion' ,'dailymotion' ,'danger',
        ];

        for ($i = 0; $i < count($tags); $i = $i + 4) {
            // findBy is the only working method in fixtures
            if (!$em->getRepository('AppBundle:Tag')->findBy(['name' => $tags[$i]])) {
                $tag = new Tag();
                $tag->setName($tags[$i])
                    ->setType($tags[$i+1])
                    ->setLabel($tags[$i+3])
                    ->setReadOnly();

                $em->persist($tag);
            }
        }

        $em->flush();
    }
}
