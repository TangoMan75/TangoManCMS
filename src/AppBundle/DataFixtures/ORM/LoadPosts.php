<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Post;
use AppBundle\Entity\Section;
use AppBundle\Entity\Tag;
use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadPosts
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\DataFixtures\ORM
 */
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
        return 8;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        $faker = Factory::create('fr_FR');

        // findBy seems to be the only working method in fixtures
        $users    = $em->getRepository('AppBundle:User')->findBy([], null, 10);
        $sections = $em->getRepository('AppBundle:Section')->findAll();
        $tags     = $em->getRepository('AppBundle:Tag')->findAll();

        foreach ($users as $user) {
            // Creates between 1 & 10 posts for each user
            for ($i = 0; $i < mt_rand(1, 10); $i++) {

                $post = new Post();
                $post->setUser($users[mt_rand(1, count($users) - 1)])
                    ->setTitle($faker->sentence(4, true))
                    ->setText('<p>'.$faker->text(mt_rand(600, 2400)).'</p>')
                    ->setCreated($faker->dateTimeThisYear($max = 'now'))
                    ->addSection($sections[mt_rand(0, count($sections) - 1)])
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
