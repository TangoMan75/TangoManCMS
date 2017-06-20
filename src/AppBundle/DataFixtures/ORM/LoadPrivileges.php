<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Privilege;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadPrivileges
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\DataFixtures\ORM
 */
class LoadPrivileges implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
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
        // Default Privileges
        $privileges = [
            'Privilège - Supprimer',   'danger',  'CAN_DELETE_PRIVILEGE', 'ROLE_SUPER_ADMIN',
            'Privilège - Modifier',    'danger',  'CAN_UPDATE_PRIVILEGE', 'ROLE_SUPER_ADMIN',
            'Privilège - Lire',        'danger',  'CAN_READ_PRIVILEGE',   'ROLE_SUPER_ADMIN',
            'Privilège - Créer',       'danger',  'CAN_CREATE_PRIVILEGE', 'ROLE_SUPER_ADMIN',
            'Role - Supprimer',        'danger',  'CAN_DELETE_ROLE',      'ROLE_SUPER_ADMIN',
            'Role - Modifier',         'danger',  'CAN_UPDATE_ROLE',      'ROLE_SUPER_ADMIN',
            'Role - Lire',             'danger',  'CAN_READ_ROLE',        'ROLE_SUPER_ADMIN',
            'Role - Créer',            'danger',  'CAN_CREATE_ROLE',      'ROLE_SUPER_ADMIN',
            'Page - Supprimer',        'warning', 'CAN_DELETE_PAGE',      'ROLE_ADMIN',
            'Page - Modifier',         'warning', 'CAN_UPDATE_PAGE',      'ROLE_ADMIN',
            'Page - Lire',             'warning', 'CAN_READ_PAGE',        'ROLE_ADMIN',
            'Page - Créer',            'warning', 'CAN_CREATE_PAGE',      'ROLE_ADMIN',
            'Utilisateur - Supprimer', 'warning', 'CAN_DELETE_USER',      'ROLE_ADMIN',
            'Utilisateur - Modifier',  'warning', 'CAN_UPDATE_USER',      'ROLE_ADMIN',
            'Utilisateur - Lire',      'warning', 'CAN_READ_USER',        'ROLE_ADMIN',
            'Utilisateur - Créer',     'warning', 'CAN_CREATE_USER',      'ROLE_ADMIN',
            'Étiquette - Supprimer',   'success', 'CAN_DELETE_TAG',       'ROLE_SUPER_USER',
            'Étiquette - Modifier',    'success', 'CAN_UPDATE_TAG',       'ROLE_SUPER_USER',
            'Étiquette - Lire',        'success', 'CAN_READ_TAG',         'ROLE_SUPER_USER',
            'Étiquette - Créer',       'success', 'CAN_CREATE_TAG',       'ROLE_SUPER_USER',
            'Publication - Supprimer', 'success', 'CAN_DELETE_POST',      'ROLE_SUPER_USER',
            'Publication - Modifier',  'success', 'CAN_UPDATE_POST',      'ROLE_SUPER_USER',
            'Publication - Lire',      'success', 'CAN_READ_POST',        'ROLE_SUPER_USER',
            'Publication - Créer',     'success', 'CAN_CREATE_POST',      'ROLE_SUPER_USER',
            'Commentaire - Supprimer', 'primary', 'CAN_DELETE_COMMENT',   'ROLE_USER',
            'Commentaire - Modifier',  'primary', 'CAN_UPDATE_COMMENT',   'ROLE_USER',
            'Commentaire - Lire',      'primary', 'CAN_READ_COMMENT',     'ROLE_USER',
            'Commentaire - Créer',     'primary', 'CAN_CREATE_COMMENT',   'ROLE_USER',
        ];

        for ($i = 0; $i < count($privileges); $i = $i + 4) {
            $role = $em->getRepository('AppBundle:Role')->findOneBy(['type' => $privileges[$i + 3]]);

            $privilege = new Privilege();
            $privilege
                ->setName($privileges[$i])
                ->setLabel($privileges[$i + 1])
                ->setType($privileges[$i + 2])
                ->addRole($role);

            $em->persist($privilege);
        }

        $em->flush();
    }
}
