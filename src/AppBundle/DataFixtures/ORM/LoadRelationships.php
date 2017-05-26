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
 * Class LoadRelations
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\DataFixtures\ORM
 */
class LoadRelationships implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
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
        return 14;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        // findBy seems to be the only working method in fixtures
        $posts = $em->getRepository('AppBundle:Post')->findAll();
        $users = $em->getRepository('AppBundle:User')->findAll();
        $votes = $em->getRepository('AppBundle:Vote')->findAll();

        // Posts->Users
        foreach ($posts as $post) {
            $post->setUser($users[mt_rand(1, count($users) - 1)]);
            $em->persist($post);
        }
        $em->flush();

         // Votes->Posts
         foreach ($votes as $vote) {
             $vote->setUser($users[mt_rand(1, count($users) - 1)]);
             $vote->setItem($posts[mt_rand(1, count($posts) - 1)]);
             $em->persist($vote);
         }
         $em->flush();
    }
}
