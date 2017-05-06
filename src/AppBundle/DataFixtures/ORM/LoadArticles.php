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
 * Class LoadArticles
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\DataFixtures\ORM
 */
class LoadArticles implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
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
        return 9;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        $faker = Factory::create('fr_FR');

        // Get users
        $users = $em->getRepository('AppBundle:User')->findAll();

        // Get section
        $sections = $em->getRepository('AppBundle:Section')->findAll();

        // Get tags
        $tags = $em->getRepository('AppBundle:Tag')->findAll();

        // Get images
        $rootdir = $this->container->getParameter('kernel.root_dir').'/../web';
        $fileNames = array_map(
            function ($filename) {
                return basename($filename);
            },
            glob($rootdir.'/uploads/images/*.{jpg,JPG,jpeg,JPEG}', GLOB_BRACE)
        );

        // Create post for each image
        for ($i = 0; $i < count($fileNames); $i++) {

            $post = new Post();
            $post->addCategory('post')
                ->setTitle($faker->sentence(4, true))
                ->setText('<p>'.$faker->text(mt_rand(600, 2400)).'</p>')
                ->setImageFileName($fileNames[$i])
                ->setCreated($faker->dateTimeThisYear($max = 'now'))
                ->setUser($users[mt_rand(1, count($users) - 1)])
                ->addSection($sections[mt_rand(1, count($sections) - 1)])
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
