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
        return 4;
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

        foreach ($users as $user) {

            // Creates random Post amount for each user
            for ($j = 0; $j < mt_rand(1, 10); $j++) {

                $post = new Post();
                $text = "<p>".$faker->text(mt_rand(600, 2400))."</p>";
                $post->setUser($user)
                     ->setTitle($faker->sentence(4, true))
                     ->setSubtitle($faker->sentence(4, true))
                     ->setContent($text)
                     ->setDateCreated($faker->dateTimeThisYear($max = 'now'));

                $tags = $em->getRepository('AppBundle:Tag')->findAll();

                for ($k = 0; $k < mt_rand(0, 5); $k++) {
                    $post->addTag($tags[mt_rand(0, 5)]);
                }

                $em->persist($post);
            }

            $em->flush();
        }
    }
}
