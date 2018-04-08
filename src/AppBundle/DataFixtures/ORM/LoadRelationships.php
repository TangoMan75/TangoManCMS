<?php

/**
 * This file is part of the TangoManCMS package.
 *
 * Copyright (c) 2018 Matthias Morin <matthias.morin@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadRelations
 *
 * @author Matthias Morin <matthias.morin@gmail.com>
 */
class LoadRelationships implements FixtureInterface, ContainerAwareInterface,
                                   OrderedFixtureInterface
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
        return 160;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        // findBy seems to be the only working method in fixtures
        $comments = $em->getRepository('AppBundle:Comment')->findAll();
        $pages    = $em->getRepository('AppBundle:Page')->findAll();
        $sites    = $em->getRepository('AppBundle:Site')->findAll();
        $posts    = $em->getRepository('AppBundle:Post')->findAll();
        //        $privileges = $em->getRepository('AppBundle:Privilege')->findAll();
        $roles     = $em->getRepository('AppBundle:Role')->findAll();
        $sections  = $em->getRepository('AppBundle:Section')->findBy(
            ['type' => 'section']
        );
        $galleries = $em->getRepository('AppBundle:Section')->findBy(
            ['type' => 'gallery']
        );
        $tags      = $em->getRepository('AppBundle:Tag')->findAll();
        $users     = $em->getRepository('AppBundle:User')->findAll();
        $votes     = $em->getRepository('AppBundle:Vote')->findAll();

        // USERS
        foreach ($users as $user) {
            $user->addRole($roles[mt_rand(1, count($roles) - 1)]);
            $em->persist($user);
        }
        $em->flush();

        // PAGES
        foreach ($pages as $page) {
            shuffle($sites);
            shuffle($sections);
            shuffle($galleries);
            for ($i = 0; $i < mt_rand(0, 4); $i++) {
                $page->addSection($sections[$i]);
                $page->addSection($galleries[$i]);
                $page->addSite($sites[$i]);
            }

            //            shuffle($tags);
            //            for ($i = 0; $i < mt_rand(0, 4); $i++) {
            //                $page->addTag($tags[$i]);
            //            }

            $em->persist($page);
        }
        $em->flush();

        // SECTIONS
        //        foreach ($sections as $section) {
        //            shuffle($tags);
        //            for ($i = 0; $i < mt_rand(0, 4); $i++) {
        //                $section->addTag($tags[$i]);
        //            }
        //
        //            $em->persist($section);
        //        }
        //        $em->flush();

        // POSTS
        $j = 0;
        foreach ($posts as $post) {
            if ($post->getType() == 'post') {
                $post->addSection($sections[mt_rand(1, count($sections) - 1)]);
            } else {
                $post->addSection($galleries[mt_rand(1, count($sections) - 1)]);
            }

            shuffle($tags);
            for ($i = 0; $i < mt_rand(0, 4); $i++) {
                $post->addTag($tags[$i]);
            }

            $post->setUser($users[mt_rand(1, count($users) - 1)]);

            $em->persist($post);
        }
        $em->flush();

        // COMMENTS
        foreach ($comments as $comment) {
            $comment->setUser($users[mt_rand(1, count($users) - 1)]);
            $comment->setPost($posts[mt_rand(1, count($posts) - 1)]);

            $em->persist($comment);
        }
        $em->flush();

        // VOTES
        foreach ($votes as $vote) {
            $vote->setUser($users[mt_rand(1, count($users) - 1)]);
            $vote->setPost($posts[mt_rand(1, count($posts) - 1)]);
            $em->persist($vote);
            $em->flush();
        }
    }
}
