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
        $count = $this->container->get('em')->repository('AppBundle:User')->count();

        $userCount = 10;
        for ($i = 0; $i < $userCount; $i++) {
            // Get random number
            $rnd = mt_rand(1, $count);
            // Load random user
            $user = $this->container->get('em')->repository('AppBundle:User')->find($rnd);

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

                    // Load Comments
                    $commentCount = mt_rand(0, 10);
                    for ($k = 0; $k < $commentCount; $k++) {

                        $comment = new Comment();
                        $commentLength = mt_rand(300, 1200);
                        $text = "<p>".$faker->text($commentLength)."</p>";
                        $comment->setUser($user)
                            ->setPost($post)
                            ->setContent($text)
                            ->setDateCreated($faker->dateTimeThisYear($max = 'now'));

                        $manager->persist($comment);

                        // Manager flushes every ten persisted items 
                        // This is mandatory when persisting large numbers of fixtures
                        // Which can cause a memory overflow
                        if ($i % 10 === 0) {
                            $manager->flush();
                        }
                    }
                }

                // $manager->flush();
            }
        }
    }
}
