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
        return 4;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        // Get total Users count
        $userCount = $this->container->get('em')->repository('AppBundle:User')->count();

        // Get total Users count
        $postCount = $this->container->get('em')->repository('AppBundle:Post')->count();

        for ($i = 1; $i <= 10; $i++) {
            // Get random number
            $rnd = mt_rand(1, $userCount);
            // Load random user
            $user = $this->container->get('em')->repository('AppBundle:User')->find($rnd);

            // Get random number
            $rnd = mt_rand(1, $postCount);
            // Load random post
            $post = $this->container->get('em')->repository('AppBundle:Post')->find($rnd);

            if ($user && $post) {
                // Load Comments
                $commentCount = mt_rand(0, 10);
                for ($j = 0; $j < $commentCount; $j++) {
                    $comment = new Comment();
                    $commentLength = mt_rand(300, 1200);
                    $text = "<p>".$faker->text($commentLength)."</p>";
                    $comment->setUser($user)
                            ->setPost($post)
                            ->setContent($text)
                            ->setDateCreated($faker->dateTimeThisYear($max = 'now'));

                    $manager->persist($comment);

                }
            }

            $manager->flush();
        }
    }
}
