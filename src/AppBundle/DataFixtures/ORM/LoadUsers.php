<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Post;
use AppBundle\Entity\Tag;
use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUsers implements FixtureInterface, ContainerAwareInterface
{
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $encoder = $this->container->get('security.password_encoder');

        if (!$this->container->get('em')->repository('AppBundle:User')->findByRoles(['ROLE_ADMIN'])) {

            // Generating admin account with pwd: "321" if not exits
            $user = new User();
            $user->setUsername("admin");
            $user->setEmail("tech@argus-lab.com");
            $user->setPassword($encoder->encodePassword($user, "321"));
            $user->setRoles(['ROLE_ADMIN']);

            $manager->persist($user);
        }

        $tags = explode(' ', 'default primary info success warning danger');
        foreach ($tags as $name) {
            $tag = new Tag();
            $tag->setName($name);
            $tag->setType($name);
            $manager->persist($tag);
            $tagCollection[] = $tag;
        }

        $userCount = 10;
        for ($i = 0; $i < $userCount; $i++) {

            $user = new User();
            $username = $faker->userName;
            $user->setUsername($username);
            $user->setEmail($username.'@'.$faker->safeEmailDomain);
            $user->setPassword($encoder->encodePassword($user, $username));
            $user->setRoles(['ROLE_USER']);
            $user->setDateCreated($faker->dateTimeThisYear($max = 'now'));

            $manager->persist($user);

            $postCount = mt_rand(0, 10);
            for ($j = 0; $j < $postCount; $j++) {
                $post = new Post();
                $postLength = mt_rand(600, 2400);
                $text = "<p>".$faker->text($postLength)."</p>";
                $post->setUser($user);
                $post->setTitle($faker->sentence(4, true));
                $post->setContent($text);
                $post->setDateCreated($faker->dateTimeThisYear($max = 'now'));

                shuffle($tagCollection);
                $labelCount = mt_rand(0, 6);
                for ($k = 0; $k < $labelCount; $k++) {
                    $post->addTag($tagCollection[$k]);
                }

                $manager->persist($post);

                $commentCount = mt_rand(0, 10);
                for ($k = 0; $k < $commentCount; $k++) {

                    $comment = new Comment();
                    $commentLength = mt_rand(300, 1200);
                    $text = "<p>".$faker->text($commentLength)."</p>";
                    $comment->setUser($user);
                    $comment->setPost($post);
                    $comment->setContent($text);
                    $comment->setDateCreated($faker->dateTimeThisYear($max = 'now'));

                    $manager->persist($comment);
                }
            }
        }

        $manager->flush();
    }
}
