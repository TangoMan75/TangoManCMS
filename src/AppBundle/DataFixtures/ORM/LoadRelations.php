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
class LoadRelations implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
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
        // $posts = $em->getRepository('AppBundle:Post')->findBy([], null, 100);

        // findBy seems to be the only working method in fixtures
        $comments = $em->getRepository('AppBundle:Comment')->findAll();
        $comment = $em->getRepository('AppBundle:Comment')->find(1);
        $pages = $em->getRepository('AppBundle:Page')->findAll();
        $page = $em->getRepository('AppBundle:Page')->find(1);
        $posts = $em->getRepository('AppBundle:Post')->findAll();
        $post = $em->getRepository('AppBundle:Post')->find(1);
        $privileges = $em->getRepository('AppBundle:Privilege')->findAll();
        $privilege = $em->getRepository('AppBundle:Privilege')->find(1);
        $roles = $em->getRepository('AppBundle:Role')->findAll();
        $role = $em->getRepository('AppBundle:Role')->find(1);
        $sections = $em->getRepository('AppBundle:Section')->findAll();
        $section = $em->getRepository('AppBundle:Section')->find(1);
        $tags = $em->getRepository('AppBundle:Tag')->findAll();
        $tag = $em->getRepository('AppBundle:Tag')->find(1);
        $users = $em->getRepository('AppBundle:User')->findAll();
        $user = $em->getRepository('AppBundle:User')->find(1);

        $comment->setPost($posts[mt_rand(1, count($posts) - 1)]);
        $comment->setUser($users[mt_rand(1, count($users) - 1)]);
        $em->persist($comment);

        $page->addSection($sections[mt_rand(1, count($sections) - 1)]);
        $em->persist($page);

        $post->addComment($comment[mt_rand(1, count($comment) - 1)]);
        $post->addSection($sections[mt_rand(1, count($sections) - 1)]);
        $post->addTag($tags[mt_rand(1, count($tags) - 1)]);
        $post->setUser($users[mt_rand(1, count($users) - 1)]);
        $em->persist($post);

        $privilege->addRole($roles[mt_rand(1, count($roles) - 1)]);
        $em->persist($privilege);

        $role->addPrivilege($privileges[mt_rand(1, count($privileges) - 1)]);
        // $role->addUser($users[mt_rand(1, count($users) - 1)]);
        $em->persist($role);

        $section->addPage($pages[mt_rand(1, count($pages) - 1)]);
        $section->addPost($posts[mt_rand(1, count($posts) - 1)]);
        $section->addTag($tags[mt_rand(1, count($tags) - 1)]);
        $em->persist($section);

        $tag->addPage($pages[mt_rand(1, count($pages) - 1)]);
        $tag->addPost($posts[mt_rand(1, count($posts) - 1)]);
        $tag->addSection($sections[mt_rand(1, count($sections) - 1)]);
        $em->persist($tag);

        $user->addPost($posts[mt_rand(1, count($posts) - 1)]);
        // $user->addRole($roles[mt_rand(1, count($roles) - 1)]);
        $em->persist($user);

        $em->flush();
    }
}
