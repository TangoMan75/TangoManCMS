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

class LoadUsers implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
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
        return 2;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        // Pasword encoder
        $encoder = $this->container->get('security.password_encoder');

        // Load Users
        $userCount = 100;
        for ($i = 1; $i <= $userCount; $i++) {

            $roles = ['ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_SUPER_USER', 'ROLE_USER'];

            $user = new User();
            $username = $faker->userName;
            $user->setUsername($username)
                 ->setEmail($username.'@'.$faker->safeEmailDomain)
                 ->setPassword($encoder->encodePassword($user, $username))
                 ->addRole($roles[mt_rand(0, 3)])
//                 ->setAvatar('data:image/jpeg;base64,'.$faker->regexify('[A-Za-z0-9/+=]{1000}'))
                 ->setDateCreated($faker->dateTimeThisYear($max = 'now'))
                 ->setBio("<p>".$faker->text(mt_rand(600, 1200))."</p>");

            $manager->persist($user);

            // Manager flushes every ten persisted items 
            // This is mandatory when persisting large numbers of fixtures
            // Which can cause a memory overflow
            if ($i % 10 === 0) {
                $manager->flush();
            }
        }
    }
}
