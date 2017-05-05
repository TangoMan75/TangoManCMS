<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Role;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadRoles
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\DataFixtures\ORM
 */
class LoadRoles implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
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
        return 1;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        $roles = [
            'Utilisateur',          'ROLE_USER',        0,
            'Super Utilisateur',    'ROLE_SUPER_USER',  1,
            'Administrateur',       'ROLE_ADMIN',       2,
            'Super Administrateur', 'ROLE_SUPER_ADMIN', 3,
        ];

        for ($i = 0; $i < 12; $i = $i + 3) {
            // findBy is the only working method in fixtures
            if (!$em->getRepository('AppBundle:Role')->findBy(['role' => $roles[$i + 1]])) {
                $role = new Role();
                $role->setName($roles[$i])
                    ->setRole($roles[$i + 1])
                    ->setHierarchy($roles[$i + 2])
                    ->setReadOnly();

                $em->persist($role);
            }
        }

        $em->flush();
    }
}
