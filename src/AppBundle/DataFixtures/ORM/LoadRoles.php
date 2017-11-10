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
 * @author  Matthias Morin <matthias.morin@gmail.com>
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
        return 10;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        // Default roles
        $roles = [
            'glyphicon glyphicon-pawn',   'primary', 'Utilisateur',          'ROLE_USER',
            'glyphicon glyphicon-bishop', 'success', 'Super Utilisateur',    'ROLE_SUPER_USER',
            'glyphicon glyphicon-tower',  'warning', 'Administrateur',       'ROLE_ADMIN',
            'glyphicon glyphicon-king',   'danger',  'Super Administrateur', 'ROLE_SUPER_ADMIN',
        ];

        for ($i = 0; $i < count($roles); $i = $i + 4) {
            $role = new Role();
            $role->setIcon($roles[$i])
                ->setLabel($roles[$i + 1])
                ->setName($roles[$i + 2])
                ->setType($roles[$i + 3])
            ;

            $em->persist($role);
        }

        $em->flush();
    }
}
