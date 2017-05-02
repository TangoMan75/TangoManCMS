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
            'Défaut'    ,'default'  ,'label'    ,'default',
            'Principal' ,'primary'  ,'label'    ,'primary',
            'Info'      ,'info'     ,'label'    ,'info',
            'Succès'    ,'success'  ,'label'    ,'success',
            'Alerte'    ,'warning'  ,'label'    ,'warning',
            'Danger'    ,'danger'   ,'label'    ,'danger',
            'Article'   ,'post'     ,'post'     ,'default',
            'Document'  ,'document' ,'document' ,'default',
            'Pdf'       ,'pdf'      ,'document' ,'default',
            'Image'     ,'picture'  ,'picture'  ,'default',
            'Lien'      ,'link'     ,'link'     ,'default',
            'Vidéo'     ,'video'    ,'video'    ,'default',
        ];

        for ($i = 0; $i < count($tags); $i = $i + 4) {
            // findBy is the only working method in fixtures
            if (!$em->getRepository('AppBundle:Tag')->findBy(['name' => $i])) {
                $tag = new Tag();
                $tag->setName($tags[$i])
                    ->setType($tags[$i+1])
                    ->setCategory($tags[$i+2])
                    ->setLabel($tags[$i+3])
                    ->setReadOnly();

                $em->persist($tag);
            }
        }

        $em->flush();
    }
}
