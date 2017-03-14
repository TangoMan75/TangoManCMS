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

        // Load Tags
        $tags = $em->getRepository('AppBundle:Tag')->findAll();

        foreach ($pages as $page) {

            // Creates random section amount for each page
            for ($i = 0; $i < mt_rand(1, 10); $i++) {
                $section = new Section();
                $section->addPage($page)
                    ->setTitle($faker->sentence(4, true));

                for ($j = 0; $j < mt_rand(0, 5); $j++) {
                    $section->addTag($tags[mt_rand(0, 5)]);
                }

                $em->persist($section);
            }

            $em->flush();
        }
    }
}
