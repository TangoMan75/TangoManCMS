<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use AppBundle\Entity\Role;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadAdmin
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\DataFixtures\ORM
 */
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
        return 3;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        $faker = Factory::create('fr_FR');

        // Pasword encoder
        $encoder = $this->container->get('security.password_encoder');

        // $roleSuperAdmin = $em->getRepository('AppBundle:Role')->findBy(['role' => 'ROLE_SUPER_ADMIN']);
        $roleSuperAdmin = 'ROLE_SUPER_ADMIN';
        $superAdmin = $em->getRepository('AppBundle:User')->findBy(['roles' => $roleSuperAdmin]);

        // Load Super Admin
        if (!$superAdmin) {

            // Generating admin account with pwd: "321" if not exits
            $user = new User();
            $user->setUsername('admin')
                ->setEmail('admin@localhost.dev')
                ->setPassword($encoder->encodePassword($user, '321'))
                ->addRole($superAdmin)
                ->setBio('<p>'.$faker->text(mt_rand(600, 1200)).'</p>');

            $em->persist($user);
            $em->flush();
        }
    }
}
