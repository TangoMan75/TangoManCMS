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
        return 2;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        // Default Privileges
        $privileges = [
            'comment',   true, true, true, true,
            'page',      true, true, true, true,
            'post',      true, true, true, true,
            'privilege', true, true, true, true,
            'role',      true, true, true, true,
            'tag',       true, true, true, true,
            'user',      true, true, true, true,
        ];

        $roles = $em->getRepository('AppBundle:Role')->findAll();

        foreach ($roles as $role) {
            for ($i = 0; $i < count($privileges); $i = $i + 5) {
                $privilege = new Privilege();
                $privilege->setName($privileges[$i])
                    ->setCreate($privileges[$i + 1])
                    ->setRead($privileges[$i + 2])
                    ->setUpdate($privileges[$i + 3])
                    ->setDelete($privileges[$i + 4])
                    ->setRole($role);

                $em->persist($privilege);
            }
        }

        $em->flush();
    }
}
