<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Post;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadArticles
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\DataFixtures\ORM
 */
class LoadArticles implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
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
        return 120;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        $faker = Factory::create('fr_FR');

        // Get images
        $rootdir = $this->container->getParameter('kernel.root_dir').'/../web';
        $fileNames = array_map(
            function ($filename) {
                return basename($filename);
            },
            glob($rootdir.'/uploads/images/*.{jpg,JPG,jpeg,JPEG}', GLOB_BRACE)
        );

        // Create post for each image
        for ($i = 0; $i < count($fileNames); $i++) {

            $post = new Post();
            $post
                ->setCreated($faker->dateTimeThisYear($max = 'now'))
                ->setImageFileName($fileNames[$i])
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
