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
        return 3;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        // Load Tags
        $tags = [
            'Défaut'    => 'default',
            'Principal' => 'primary',
            'Info'      => 'info',
            'Succès'    => 'success',
            'Alerte'    => 'warning',
            'Danger'    => 'danger',
        ];

        foreach ($tags as $name => $type) {
            // findBy is the only working method in fixtures
            if (!$em->getRepository('AppBundle:Tag')->findBy(['name' => $name])) {
                $tag = new Tag();
                $tag->setName($name);
                $tag->setType($type);
                $tag->setReadOnly();
                $em->persist($tag);
            }
        }

        $em->flush();
    }
}
