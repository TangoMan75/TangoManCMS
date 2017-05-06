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
        $email    = $this->container->getParameter('mailer_from');
        $username = $this->container->getParameter('super_admin_username');
        $pwd      = $this->container->getParameter('super_admin_pwd');
        $encoder  = $this->container->get('security.password_encoder');

        // $roleSuperAdmin = $em->getRepository('AppBundle:Role')->findBy(['role' => 'ROLE_SUPER_ADMIN']);
        // $superAdmin = $em->getRepository('AppBundle:User')->findBy(['roles' => $roleSuperAdmin]);

        // Load Super Admin
        $roleSuperAdmin = 'ROLE_SUPER_ADMIN';
        $superAdmin = $em->getRepository('AppBundle:User')->findByRole($roleSuperAdmin);

        if (!$superAdmin) {
            // Generating super admin account with default password
            $user = new User();
            $user->setUsername($username)
                ->setEmail($email)
                ->setPassword($encoder->encodePassword($user, $pwd))
                ->addRole($roleSuperAdmin)
                ->setBio('<p>Ceci est le compte super administrateur.</p>');

            $em->persist($user);
            $em->flush();
        }
    }
}
