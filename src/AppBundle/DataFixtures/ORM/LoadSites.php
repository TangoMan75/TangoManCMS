<?php

/*
 * This file is part of the TangoManCMS package.
 *
 * (c) Matthias Morin <matthias.morin@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Site;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadSites
 *
 * @author  Matthias Morin <matthias.morin@gmail.com>core/pdo.php
 */
class LoadSites implements FixtureInterface, ContainerAwareInterface,
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
        return 80;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 10; $i++) {
            $section = new Site();
            $section->setTitle($faker->sentence(4, true))
                    ->setSubtitle($faker->sentence(6, true))
                    ->setSummary('<p>'.$faker->text(mt_rand(100, 255)).'</p>')
                    ->setPublished($i % 3 ? false : true);

            $em->persist($section);
        }

        $em->flush();
    }
}
