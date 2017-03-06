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

class LoadAdmin implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
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
        return 1;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        // Pasword encoder
        $encoder = $this->container->get('security.password_encoder');

        // Load Super Admin
        if (!$manager->getRepository('AppBundle:User')->findByRole('ROLE_SUPER_ADMIN')) {

            // Generating admin account with pwd: "321" if not exits
            $user = new User();
            $user->setUsername("admin")
                 ->setEmail("admin@localhost.dev")
                 ->setPassword($encoder->encodePassword($user, '321'))
                 ->addRole('ROLE_SUPER_ADMIN')
                 ->setBio("<p>".$faker->text(mt_rand(600, 1200))."</p>");

            $manager->persist($user);
            $manager->flush();
        }
    }
}