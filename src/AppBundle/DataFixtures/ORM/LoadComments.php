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

        // findBy is the only working method in fixtures
        // Get Posts
        $posts = $em->getRepository('AppBundle:Post')->findBy([], null, 100);
        // Get Users
        // $users = $em->getRepository('AppBundle:User')->findBy([], null, 100);
        $users = $em->getRepository('AppBundle:User')->findAll();

        foreach ($posts as $post) {

            // Creates random comment amount for each post
            for ($j = 0; $j < mt_rand(1, 10); $j++) {

                $comment = new Comment();
                $text = "<p>".$faker->text(mt_rand(300, 1200))."</p>";
                $comment->setUser($users[mt_rand(1, count($users)-1)])
                    ->setPost($post)
                    ->setContent($text)
                    ->setDateCreated($faker->dateTimeThisYear($max = 'now'));

                $em->persist($comment);

            }

            $em->flush();
        }
    }
}
