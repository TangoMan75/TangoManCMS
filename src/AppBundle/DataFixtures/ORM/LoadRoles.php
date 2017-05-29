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
        return 2;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        // Default roles
        $roles = [
            'Utilisateur'          => 'ROLE_USER',
            'Super Utilisateur'    => 'ROLE_SUPER_USER',
            'Administrateur'       => 'ROLE_ADMIN',
            'Super Administrateur' => 'ROLE_SUPER_ADMIN',
        ];

        $privileges = $em->getRepository('AppBundle:Privilege')->findAll();

        foreach ($roles as $key => $item) {
            $role = new Role();
            $role->setName($key)
                ->setType($item);

            foreach ($privileges as $privilege) {
                $role->addPrivilege($privilege);
            }

            $em->persist($role);
        }

        $em->flush();
    }
}
