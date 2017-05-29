<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
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
        $comments   = $em->getRepository('AppBundle:Comment')->findAll();
        $pages      = $em->getRepository('AppBundle:Page')->findAll();
        $posts      = $em->getRepository('AppBundle:Post')->findAll();
        $privileges = $em->getRepository('AppBundle:Privilege')->findAll();
        $roles      = $em->getRepository('AppBundle:Role')->findAll();
        $sections   = $em->getRepository('AppBundle:Section')->findAll();
        $tags       = $em->getRepository('AppBundle:Tag')->findAll();
        $users      = $em->getRepository('AppBundle:User')->findAll();
        $votes      = $em->getRepository('AppBundle:Vote')->findAll();

        $j = 0;
        foreach ($posts as $post) {
            $post->addSection($sections[mt_rand(1, count($sections) - 1)]);

            if ($j < count($comments)) {
                $post->addComment($comments[$j++]);
            }

            shuffle($tags);
            for ($i = 0; $i < mt_rand(0, 4); $i++) {
                $post->addTag($tags[$i]);
            }

            $post->setUser($users[mt_rand(1, count($users) - 1)]);

            if ($j < count($votes)) {
                $post->addVote($votes[$j++]);
            }

            $em->persist($post);
        }
        $em->flush();

        foreach ($pages as $page) {
            $page->addSection($sections[mt_rand(1, count($sections) - 1)]);
            $em->persist($page);
        }
        $em->flush();

        $j = 0;
        foreach ($votes as $vote) {
            if ($j < count($users)) {
                $vote->setUser($users[$j++]);
            }
            $em->persist($vote);
        }
        $em->flush();
    }
}
