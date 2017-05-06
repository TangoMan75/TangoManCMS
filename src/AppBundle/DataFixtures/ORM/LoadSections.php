<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Section;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadSections
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\DataFixtures\ORM
 */
class LoadSections implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
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
        return 6;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        $faker = Factory::create('fr_FR');
        $pages = $em->getRepository('AppBundle:Page')->findAll();

        foreach ($pages as $page) {
            // Creates between 1 & 5 sections for each page
            for ($i = 0; $i < mt_rand(1, 5); $i++) {
                $section = new Section();
                $section->setTitle($faker->sentence(4, true))
                    ->setPages($page)
                    ->setPublished($i % 2);

                $em->persist($section);
            }
        }

        $em->flush();
    }
}
