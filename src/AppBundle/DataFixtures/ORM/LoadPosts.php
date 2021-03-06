<?php

/**
 * This file is part of the TangoManCMS package.
 *
 * Copyright (c) 2018 Matthias Morin <matthias.morin@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Post;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadPosts
 *
 * @author Matthias Morin <matthias.morin@gmail.com>
 */
class LoadPosts implements FixtureInterface, ContainerAwareInterface,
                           OrderedFixtureInterface
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
        return 110;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 10; $i++) {

            $post = new Post();
            $post
                ->setCreated($faker->dateTimeThisYear($max = 'now'))
                ->setPublished($i % 2)
                ->setSubtitle($faker->sentence(6, true))
                ->setSummary('<p>'.$faker->text(mt_rand(100, 255)).'</p>')
                ->setText('<p>'.$faker->text(mt_rand(600, 2400)).'</p>')
                ->setTitle($faker->sentence(4, true))
                ->setType('post')
                ->setViews(mt_rand(0, 100));

            $em->persist($post);
        }

        $em->flush();
    }
}
