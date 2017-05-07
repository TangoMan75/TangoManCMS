<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Page;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadPages
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\DataFixtures\ORM
 */
class LoadPages implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
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
        return 7;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        $faker = Factory::create('fr_FR');
        $tags = $em->getRepository('AppBundle:Tag')->findAll();
        $sections = $em->getRepository('AppBundle:Section')->findAll();

        // Load 10 Pages
        for ($i = 1; $i <= 10; $i++) {

            $page = new Page();
            $page->setTitle($faker->sentence(4, true))
                ->setPublished($i % 2);

            // Adds between 1 & 5 random sections to post
            shuffle($sections);
            for ($j = 0; $j < mt_rand(1, 5); $j++) {
                $page->addSection($sections[$j]);
            }

            // Adds between 0 & 5 random tags to post
            shuffle($tags);
            for ($j = 0; $j < mt_rand(0, 5); $j++) {
                $page->addTag($tags[$j]);
            }

            $em->persist($page);
        }

        $em->flush();
    }
}
