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
        return 10;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        // Default Privileges
        $privileges = [
            'Commentaire Créer'     => 'ROLE_COMMENT_CREATE',
            'Commentaire Lire'      => 'ROLE_COMMENT_READ',
            'Commentaire Modifier'  => 'ROLE_COMMENT_UPDATE',
            'Commentaire Supprimer' => 'ROLE_COMMENT_DELETE',
            'Page Créer'            => 'ROLE_PAGE_CREATE',
            'Page Lire'             => 'ROLE_PAGE_READ',
            'Page Modifier'         => 'ROLE_PAGE_UPDATE',
            'Page Supprimer'        => 'ROLE_PAGE_DELETE',
            'Publication Créer'     => 'ROLE_POST_CREATE',
            'Publication Lire'      => 'ROLE_POST_READ',
            'Publication Modifier'  => 'ROLE_POST_UPDATE',
            'Publication Supprimer' => 'ROLE_POST_DELETE',
            'Privilège Créer'       => 'ROLE_PRIVILEGE_CREATE',
            'Privilège Lire'        => 'ROLE_PRIVILEGE_READ',
            'Privilège Modifier'    => 'ROLE_PRIVILEGE_UPDATE',
            'Privilège Supprimer'   => 'ROLE_PRIVILEGE_DELETE',
            'Role Créer'            => 'ROLE_ROLE_CREATE',
            'Role Lire'             => 'ROLE_ROLE_READ',
            'Role Modifier'         => 'ROLE_ROLE_UPDATE',
            'Role Supprimer'        => 'ROLE_ROLE_DELETE',
            'Étiquette Créer'       => 'ROLE_TAG_CREATE',
            'Étiquette Lire'        => 'ROLE_TAG_READ',
            'Étiquette Modifier'    => 'ROLE_TAG_UPDATE',
            'Étiquette Supprimer'   => 'ROLE_TAG_DELETE',
            'Utilisateur Créer'     => 'ROLE_USER_CREATE',
            'Utilisateur Lire'      => 'ROLE_USER_READ',
            'Utilisateur Modifier'  => 'ROLE_USER_UPDATE',
            'Utilisateur Supprimer' => 'ROLE_USER_DELETE',
        ];

        foreach ($privileges as $name => $type) {
            $privilege = new Privilege();
            $privilege->setName($name)
                ->setType($type);

            $em->persist($privilege);
        }

        $em->flush();
    }
}
