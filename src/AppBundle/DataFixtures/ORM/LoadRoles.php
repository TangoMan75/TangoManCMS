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
        return 20;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        // Default roles
        $roles = [
            'Utilisateur',          'ROLE_USER',        'glyphicon-pawn',
            'Super Utilisateur',    'ROLE_SUPER_USER',  'glyphicon-bishop',
            'Administrateur',       'ROLE_ADMIN',       'glyphicon-tower',
            'Super Administrateur', 'ROLE_SUPER_ADMIN', 'glyphicon-king',
        ];

//        $privileges = $em->getRepository('AppBundle:Privilege')->findAll();

        for ($i = 0; $i < count($roles); $i = $i + 3) {
            $role = new Role();
            $role->setIcon($roles[$i + 2])
                ->setName($roles[$i])
                ->setType($roles[$i + 1]);

            $em->persist($role);
        }

        $em->flush();
    }
}
