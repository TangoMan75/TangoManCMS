<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Post;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadArticles implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $rootdir;

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
        $this->rootdir = $this->container->getParameter('kernel.root_dir')."/../web";

        $faker = Factory::create('fr_FR');

        // Get users
        $users = $em->getRepository('AppBundle:User')->findAll();

        // Get pages
        $pages = $em->getRepository('AppBundle:Page')->findAll();

        // Get tags
        $tags = $em->getRepository('AppBundle:Tag')->findAll();

        // Get images
        $fileNames = array_map(
            function ($filename) {
                return basename($filename);
            },
            glob($this->rootdir."/uploads/images/*.{jpg,JPG,jpeg,JPEG}", GLOB_BRACE)
        );

        // Creates between 1 & 10 posts for each user
        for ($i = 0; $i < count($fileNames); $i++) {

            $post = new Post();
            $post->addCategory('post')
                ->setTitle($faker->sentence(4, true))
                ->setText('<p>'.$faker->text(mt_rand(600, 2400)).'</p>')
                ->setImageFileName($fileNames[$i])
                ->setCreated($faker->dateTimeThisYear($max = 'now'))
                ->setUser($users[mt_rand(1, count($users) - 1)])
                ->setPage($pages[mt_rand(1, count($pages) - 1)])
                ->setPublished($i % 2);

            // Adds between 1 & 5 random tags to post
            shuffle($tags);
            for ($j = 0; $j < mt_rand(1, 5); $j++) {
                $post->addTag($tags[$j]);
            }

            $em->persist($post);
        }

        $em->flush();
    }
}
