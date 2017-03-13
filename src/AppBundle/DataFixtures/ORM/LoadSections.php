<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Section;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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

        // Load Pages
        $pages = $em->getRepository('AppBundle:Page')->findAll();

        foreach ($pages as $page) {

            // Load Sections
            for ($i = 0; $i < mt_rand(1, 10); $i++) {
                $section = new Section();
                $section->setPage($page)
                        ->setTitle($faker->sentence(4, true));

                $em->persist($section);
            }

            $em->flush();
        }
    }
}
