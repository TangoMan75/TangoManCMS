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
        return 11;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        $this->rootdir = $this->container->getParameter('kernel.root_dir')."/../web";

        $faker = Factory::create('fr_FR');

        // Gets users
        // findBy is the only working method in fixtures
        $users = $em->getRepository('AppBundle:User')->findBy([], null, 100);

        // Gets pages
        $pages = $em->getRepository('AppBundle:Page')->findAll();

        foreach ($users as $user) {

            // Creates between 1 & 10 posts for each user
            for ($i = 0; $i < mt_rand(1, 10); $i++) {

                $fileNames = array_map(
                    function ($filename) {
                        return basename($filename);
                    },
                    glob($this->rootdir."/uploads/images/*.{jpg,JPG,jpeg,JPEG}", GLOB_BRACE)
                );

                for ($j = 0; $j < count($fileNames); $j++) {
                    $post = new Post();
                    $post->addCategory('post')
                        ->setTitle($faker->sentence(4, true))
                        ->setText('<p>'.$faker->text(mt_rand(600, 2400)).'</p>')
                        ->setImageFileName($fileNames[$j])
                        ->setCreated($faker->dateTimeThisYear($max = 'now'))
                        ->setUser($user)
                        ->setPage($pages[mt_rand(0, count($pages) - 1)])
                        ->setPublished($i % 2);

                    // Adds between 1 & 5 tags to post
	                $tags = $em->getRepository('AppBundle:Tag')->findAll();
	                for ($j = 0; $j < mt_rand(0, 5); $j++) {
	                    $post->addTag($tags[mt_rand(0, 5)]);
	                }

                    $em->persist($post);
                }
            }

            $em->flush();
        }
    }
}
