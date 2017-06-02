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
            'Privilège - Supprimer',   'danger',  'ROLE_PRIVILEGE_DELETE', 'ROLE_SUPER_ADMIN',
            'Privilège - Modifier',    'danger',  'ROLE_PRIVILEGE_UPDATE', 'ROLE_SUPER_ADMIN',
            'Privilège - Créer',       'danger',  'ROLE_PRIVILEGE_CREATE', 'ROLE_SUPER_ADMIN',
            'Privilège - Lire',        'danger',  'ROLE_PRIVILEGE_READ',   'ROLE_SUPER_ADMIN',
            'Role - Supprimer',        'danger',  'ROLE_ROLE_DELETE',      'ROLE_SUPER_ADMIN',
            'Role - Modifier',         'danger',  'ROLE_ROLE_UPDATE',      'ROLE_SUPER_ADMIN',
            'Role - Créer',            'danger',  'ROLE_ROLE_CREATE',      'ROLE_SUPER_ADMIN',
            'Role - Lire',             'danger',  'ROLE_ROLE_READ',        'ROLE_SUPER_ADMIN',
            'Page - Supprimer',        'warning', 'ROLE_PAGE_DELETE',      'ROLE_ADMIN',
            'Page - Modifier',         'warning', 'ROLE_PAGE_UPDATE',      'ROLE_ADMIN',
            'Page - Créer',            'warning', 'ROLE_PAGE_CREATE',      'ROLE_ADMIN',
            'Page - Lire',             'warning', 'ROLE_PAGE_READ',        'ROLE_ADMIN',
            'Utilisateur - Supprimer', 'warning', 'ROLE_USER_DELETE',      'ROLE_ADMIN',
            'Utilisateur - Modifier',  'warning', 'ROLE_USER_UPDATE',      'ROLE_ADMIN',
            'Utilisateur - Créer',     'warning', 'ROLE_USER_CREATE',      'ROLE_ADMIN',
            'Utilisateur - Lire',      'warning', 'ROLE_USER_READ',        'ROLE_ADMIN',
            'Étiquette - Supprimer',   'success', 'ROLE_TAG_DELETE',       'ROLE_SUPER_USER',
            'Étiquette - Modifier',    'success', 'ROLE_TAG_UPDATE',       'ROLE_SUPER_USER',
            'Étiquette - Créer',       'success', 'ROLE_TAG_CREATE',       'ROLE_SUPER_USER',
            'Étiquette - Lire',        'success', 'ROLE_TAG_READ',         'ROLE_SUPER_USER',
            'Publication - Supprimer', 'success', 'ROLE_POST_DELETE',      'ROLE_SUPER_USER',
            'Publication - Modifier',  'success', 'ROLE_POST_UPDATE',      'ROLE_SUPER_USER',
            'Publication - Créer',     'success', 'ROLE_POST_CREATE',      'ROLE_SUPER_USER',
            'Publication - Lire',      'success', 'ROLE_POST_READ',        'ROLE_SUPER_USER',
            'Commentaire - Supprimer', 'primary', 'ROLE_COMMENT_DELETE',   'ROLE_USER',
            'Commentaire - Modifier',  'primary', 'ROLE_COMMENT_UPDATE',   'ROLE_USER',
            'Commentaire - Créer',     'primary', 'ROLE_COMMENT_CREATE',   'ROLE_USER',
            'Commentaire - Lire',      'primary', 'ROLE_COMMENT_READ',     'ROLE_USER',
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
