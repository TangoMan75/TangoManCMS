<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Page;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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

        // Load Pages
        for ($i = 1; $i <= 10; $i++) {

            $page = new Page();
            // Pages do not have auto id strategy
            $page->setId($i)
                 ->setTitle($faker->sentence(4, true));
//                 ->setDescription('<p>'.$faker->text(mt_rand(600, 1200)).'</p>');

            $tags = $em->getRepository('AppBundle:Tag')->findAll();

            for ($j = 0; $j < mt_rand(0, 5); $j++) {
                $page->addTag($tags[mt_rand(0, 5)]);
            }

            $em->persist($page);

        }

        $em->flush();
    }
}
