<?php
/**
 * Created by PhpStorm.
 * User: MORIN Matthias
 * Date: 22/09/2016
 * Time: 17:02
 */

namespace AppBundle\DataFixtures\ORM;


use AppBundle\Entity\Post;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUsers implements FixtureInterface, ContainerAwareInterface
{
    private $container;

    /**
     * @param mixed $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $encoder = $this->container->get('security.password_encoder');

        // Generating admin account with pwd: "321"
        $user = new User();
        $user->setUsername("admin");
        $user->setEmail( $faker->safeEmail);
        $user->setPassword($encoder->encodePassword($user, "321"));
        $user->setToken(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
        $user->setRoles(['ROLE_ADMIN']);

        $manager->persist($user);

        for ($i=0; $i < 10; $i++){

            $user = new User();
            $username = $faker->userName;
            $user->setUsername($username);
            $user->setEmail($faker->safeEmail);
            $user->setPassword($encoder->encodePassword($user, $username));
            $user->setToken(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
            $user->setRoles(['ROLE_USER']);

            $manager->persist($user);

            $rand = mt_rand(1, 5);
            for ($j=0; $j < $rand; $j++) {
                $post = new Post();
                $post->setUser($user);
                $post->setTitle($faker->sentence(4, true));
                $post->setContent($faker->text(600));
                $post->setDateCreated($faker->dateTimeThisYear($max = 'now'));

                $manager->persist($post);
            }
        }

        $manager->flush();
    }
}
