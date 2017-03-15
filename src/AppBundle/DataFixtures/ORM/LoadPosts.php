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

    public function getOrder()
    {
        return 5;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        $faker = Factory::create('fr_FR');

        // Gets users
        // findBy is the only working method in fixtures
        $users = $em->getRepository('AppBundle:User')->findBy([], null, 100);

        // Gets pages
        $pages = $em->getRepository('AppBundle:Page')->findAll();

        foreach ($users as $user) {

            // Creates between 1 & 10 posts for each user
            for ($i = 0; $i < mt_rand(1, 10); $i++) {

                $post = new Post();
                $post->setUser($user)
                     ->setTitle($faker->sentence(4, true))
                     ->setContent('<p>'.$faker->text(mt_rand(600, 2400)).'</p>')
                     ->setCreated($faker->dateTimeThisYear($max = 'now'))
                     ->setPage($pages[mt_rand(0, count($pages)-1)])
                     ->setPublished($i%2);

                $tags = $em->getRepository('AppBundle:Tag')->findAll();

                for ($j = 0; $j < mt_rand(0, 5); $j++) {
                    $post->addTag($tags[mt_rand(0, 5)]);
                }

                $em->persist($post);
            }

            $em->flush();
        }
    }
}
