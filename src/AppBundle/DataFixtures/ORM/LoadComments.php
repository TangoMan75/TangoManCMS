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

use AppBundle\Entity\Comment;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadComments
 *
 * @author  Matthias Morin <matthias.morin@gmail.com>core/pdo.php
 */
class LoadComments implements FixtureInterface, ContainerAwareInterface,
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
        return 150;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 50; $i++) {
            $comment = new Comment();
            $comment
                ->setText('<p>'.$faker->text(mt_rand(300, 1200)).'</p>')
                ->setCreated($faker->dateTimeThisYear($max = 'now'))
                ->setPublished($i % 2);

            $em->persist($comment);
        }

        $em->flush();
    }
}
