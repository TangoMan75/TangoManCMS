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
        $comments   = $em->getRepository('AppBundle:Comment')->findAll();
        $pages      = $em->getRepository('AppBundle:Page')->findAll();
        $posts      = $em->getRepository('AppBundle:Post')->findAll();
        $privileges = $em->getRepository('AppBundle:Privilege')->findAll();
        $roles      = $em->getRepository('AppBundle:Role')->findAll();
        $stats      = $em->getRepository('AppBundle:Stat')->findAll();
        $sections   = $em->getRepository('AppBundle:Section')->findAll();
        $tags       = $em->getRepository('AppBundle:Tag')->findAll();
        $users      = $em->getRepository('AppBundle:User')->findAll();

        // Comments
        foreach ($comments as $comment) {
            $comment->setPost($posts[mt_rand(1, count($posts) - 1)]);
            $comment->setUser($users[mt_rand(1, count($users) - 1)]);
            $em->persist($comment);
        }
        $em->flush();

        // Pages
        foreach ($pages as $page) {
            $page->addSection($sections[mt_rand(1, count($sections) - 1)]);
            $em->persist($page);
        }
        $em->flush();

        // Posts
        $j = 0;
        foreach ($posts as $post) {
            $post->addComment($comments[mt_rand(1, count($comments) - 1)]);
            $post->addSection($sections[mt_rand(1, count($sections) - 1)]);

//             shuffle($tags);
//             for ($i = 0; $i < mt_rand(0, 4); $i++){
//                 $post->addTag($tags[$i]);
//             }

             if ($j < count($stats)) {
                 $post->addStat($stats[$j++]);
             }

            $post->setUser($users[mt_rand(1, count($users) - 1)]);
            $em->persist($post);
        }
        $em->flush();

//         // Privileges
//         foreach ($privileges as $privilege) {
//             $privilege->addRole($roles[mt_rand(1, count($roles) - 1)]);
//             $em->persist($privilege);
//         }
//         $em->flush();
//
//         // Roles
//         foreach ($roles as $role) {
//             $role->addPrivilege($privileges[mt_rand(1, count($privileges) - 1)]);
//             // $role->addUser($users[mt_rand(1, count($users) - 1)]);
//             $em->persist($role);
//         }
//         $em->flush();

//         // Stat->Post
//         foreach ($stats as $stat) {
//             $stat->addPost($posts[mt_rand(1, count($posts) - 1)]);
//             $stat->addUser($users[mt_rand(1, count($users) - 1)]);
//             $em->persist($stat);
//         }
//         $em->flush();
//
//         // Stat->Media
//         foreach ($stats as $stat) {
//             $stat->addMedia($medias[mt_rand(1, count($medias) - 1)]);
//             $stat->addUser($users[mt_rand(1, count($users) - 1)]);
//             $em->persist($stat);
//         }
//         $em->flush();
//
//         // Stat->Page
//         foreach ($stats as $stat) {
//             $stat->addPage($pages[mt_rand(1, count($pages) - 1)]);
//             $stat->addUser($users[mt_rand(1, count($users) - 1)]);
//             $em->persist($stat);
//         }
//         $em->flush();

//         // Sections
//         foreach ($sections as $section) {
//             $section->addPage($pages[mt_rand(1, count($pages) - 1)]);
//             $section->addPost($posts[mt_rand(1, count($posts) - 1)]);
//             // $section->addTag($tags[mt_rand(1, count($tags) - 1)]);
//             $em->persist($section);
//         }
//         $em->flush();
//
//         // Tags
//         foreach ($tags as $tag) {
//             $tag->addItem($pages[mt_rand(1, count($pages) - 1)]);
//             $tag->addItem($posts[mt_rand(1, count($posts) - 1)]);
//             $tag->addItem($sections[mt_rand(1, count($sections) - 1)]);
//             $em->persist($tag);
//         }
//         $em->flush();

        // Users
        foreach ($users as $user) {
            $user->addPost($posts[mt_rand(1, count($posts) - 1)]);
            // $user->addRole($roles[mt_rand(1, count($roles) - 1)]);
            // $user->addStat($stats[mt_rand(1, count($stat) - 1)]);
            $em->persist($user);
        }
        $em->flush();
    }
}
