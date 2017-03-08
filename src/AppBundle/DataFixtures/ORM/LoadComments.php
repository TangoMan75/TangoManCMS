<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Post;
use AppBundle\Entity\Tag;
use AppBundle\Entity\User;
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
        return 5;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        $faker = Factory::create('fr_FR');

        // Get Users
        // findBy is the only working method in fixtures
        $users = $em->getRepository('AppBundle:User')->findBy([], null, 10);
        // Get Posts
        $posts = $em->getRepository('AppBundle:Post')->findBy([], null, 10);

        foreach ($users as $user) {
            foreach ($posts as $post) {
                $comment = new Comment();
                $commentLength = mt_rand(300, 1200);
                $text = "<p>".$faker->text($commentLength)."</p>";
                $comment->setUser($user)
                    ->setPost($post)
                    ->setContent($text)
                    ->setDateCreated($faker->dateTimeThisYear($max = 'now'));

                $em->persist($comment);
            }

            $em->flush();
        }
    }
}
