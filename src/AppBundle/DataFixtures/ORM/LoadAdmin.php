<?php

namespace AppBundle\DataFixtures\ORM;

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

    /**
     * @return int
     */
    public function getOrder()
    {
        return 2;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        $faker = Factory::create('fr_FR');

        // Pasword encoder
        $encoder = $this->container->get('security.password_encoder');

        // Load Super Admin
        if (!$em->getRepository('AppBundle:User')->findByRole('ROLE_SUPER_ADMIN')) {

            // Generating admin account with pwd: "321" if not exits
            $user = new User();
            $user->setUsername("admin")
                ->setEmail("admin@localhost.dev")
                ->setPassword($encoder->encodePassword($user, '321'))
                ->addRole('ROLE_SUPER_ADMIN')
                ->setBio('<p>'.$faker->text(mt_rand(600, 1200)).'</p>');

            $em->persist($user);
            $em->flush();
        }
    }
}