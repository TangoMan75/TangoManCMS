<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Post;
use AppBundle\Entity\Tag;
use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
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

    public function getOrder()
    {
        return 3;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        // Load Tags
        $tags = [
            'default' => 'default',
            'primary' => 'primary',
            'info'    => 'info',
            'success' => 'success',
            'warning' => 'warning',
            'danger'  => 'danger',
            'import'  => 'default',
        ];

        foreach ($tags as $name => $type) {
            // findBy is the only working method in fixtures
            if (!$manager->getRepository('AppBundle:Tag')->findBy(['name' => $name])) {
                $tag = new Tag();
                $tag->setName($name);
                $tag->setType($type);
                $manager->persist($tag);
            }
        }

        $manager->flush();
    }
}
