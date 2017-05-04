<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Post;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadPosts implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
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
        return 6;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        $faker = Factory::create('fr_FR');

        // Get 10 users
        // findBy seems to be the only working method in fixtures
        $users = $em->getRepository('AppBundle:User')->findBy([], null, 10);

        // Get pages
        $pages = $em->getRepository('AppBundle:Page')->findAll();

        // Get tags
        $tags = $em->getRepository('AppBundle:Tag')->findAll();

        foreach ($users as $user) {

            // Creates between 1 & 10 posts for each user
            for ($i = 0; $i < mt_rand(1, 10); $i++) {

                $post = new Post();
                $post->setUser($users[mt_rand(1, count($users) - 1)])
                    ->setTitle($faker->sentence(4, true))
                    ->setText('<p>'.$faker->text(mt_rand(600, 2400)).'</p>')
                    ->setCreated($faker->dateTimeThisYear($max = 'now'))
                    ->setPage($pages[mt_rand(0, count($pages) - 1)])
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
}
