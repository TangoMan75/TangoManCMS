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
        return 150;
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
//        $privileges = $em->getRepository('AppBundle:Privilege')->findAll();
        $roles      = $em->getRepository('AppBundle:Role')->findAll();
        $sections   = $em->getRepository('AppBundle:Section')->findAll();
//        $tags       = $em->getRepository('AppBundle:Tag')->findAll();
        $users      = $em->getRepository('AppBundle:User')->findAll();
        $votes      = $em->getRepository('AppBundle:Vote')->findAll();

        foreach ($users as $user) {
            $user->addRole($roles[mt_rand(1, count($roles) - 1)]);
            $em->persist($user);
        }
        $em->flush();

        foreach ($pages as $page) {
            shuffle($sections);
            for ($i = 0; $i < mt_rand(0, 4); $i++) {
                $page->addSection($sections[$i]);
            }

//            shuffle($tags);
//            for ($i = 0; $i < mt_rand(0, 4); $i++) {
//                $page->addTag($tags[$i]);
//            }

            $em->persist($page);
        }
        $em->flush();

//        foreach ($sections as $section) {
//            shuffle($tags);
//            for ($i = 0; $i < mt_rand(0, 4); $i++) {
//                $section->addTag($tags[$i]);
//            }
//
//            $em->persist($section);
//        }
//        $em->flush();

        $j = 0;
        foreach ($posts as $post) {
            $post->addSection($sections[mt_rand(1, count($sections) - 1)]);

//            shuffle($tags);
//            for ($i = 0; $i < mt_rand(0, 4); $i++) {
//                $post->addTag($tags[$i]);
//            }

            $post->setUser($users[mt_rand(1, count($users) - 1)]);

            $em->persist($post);
        }
        $em->flush();

        foreach ($comments as $comment) {
            $comment->setUser($users[mt_rand(1, count($users) - 1)]);
            $comment->setPost($posts[mt_rand(1, count($posts) - 1)]);

            $em->persist($comment);
        }
        $em->flush();

        foreach ($votes as $i => $vote) {
            $vote->setUser($users[mt_rand(1, count($users) - 1)]);
            $vote->setPost($posts[mt_rand(1, count($posts) - 1)]);
            $em->persist($vote);
            $em->flush();
        }
    }
}
