<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Comment;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadComments implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
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

        // findBy is the only working method in fixtures
        // Get 100 Posts
        $posts = $em->getRepository('AppBundle:Post')->findBy([], null, 100);
        // Get Users
        // $users = $em->getRepository('AppBundle:User')->findBy([], null, 100);
        $users = $em->getRepository('AppBundle:User')->findAll();

        foreach ($posts as $post) {

            // Creates random comment amount for each post
            for ($i = 0; $i < mt_rand(1, 10); $i++) {

                $comment = new Comment();
                $comment->setUser($users[mt_rand(1, count($users)-1)])
                        ->setPost($post)
                        ->setContent('<p>'.$faker->text(mt_rand(300, 1200)).'</p>')
                        ->setCreated($faker->dateTimeThisYear($max = 'now'))
                        ->setPublished($i%2);

                $em->persist($comment);

            }

            $em->flush();
        }
    }
}
