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
        return 3;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        // Load Tags
        $tags = explode(' ', 'default primary info success warning danger');
        foreach ($tags as $name) {
            $tag = new Tag();
            $tag->setName($name);
            $tag->setType($name);
            $manager->persist($tag);
            $tagCollection[] = $tag;
        }

        // Get total Users count
        $userCount = $this->container->get('em')->repository('AppBundle:User')->count();

        for ($i = 1; $i <= 10; $i++) {
            // Get random number
            $rnd = mt_rand(1, $userCount);
            // Load random user
            $user = $this->container->get('em')->repository('AppBundle:User')->find($rnd);

            dump($rnd);
            dump($user);

            if($user) {
                // Creates random Post amount for each user
                $postCount = mt_rand(1, 10);
                for ($j = 1; $j < $postCount; $j++) {
                    $post = new Post();
                    $postLength = mt_rand(600, 2400);
                    $text = "<p>".$faker->text($postLength)."</p>";
                    $post->setUser($user)
                         ->setTitle($faker->sentence(4, true))
                         ->setContent($text)
                         ->setDateCreated($faker->dateTimeThisYear($max = 'now'));

                    // Sets random amount of tags to User's Post
                    shuffle($tagCollection);
                    $labelCount = mt_rand(0, 6);
                    for ($k = 0; $k < $labelCount; $k++) {
                        $post->addTag($tagCollection[$k]);
                    }

                    $manager->persist($post);
                }
            }
        }

        $manager->flush();
    }
}
