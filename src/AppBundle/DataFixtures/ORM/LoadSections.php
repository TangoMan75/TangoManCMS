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
 * @author  Matthias Morin <matthias.morin@gmail.com>
 * @package AppBundle\DataFixtures\ORM
 */
class LoadSections implements FixtureInterface, ContainerAwareInterface,
                              OrderedFixtureInterface
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
        return 100;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 20; $i++) {
            $section = new Section();
            $section->setTitle($faker->sentence(4, true))
                    ->setSubtitle($faker->sentence(6, true))
                    ->setSummary('<p>'.$faker->text(mt_rand(100, 255)).'</p>')
                    ->setType($i % 2 ? 'section' : 'gallery')
                    ->setPublished($i % 3 ? false : true);

            $em->persist($section);
        }

        $em->flush();
    }
}
