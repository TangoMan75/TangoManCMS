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

        // Load Privileges
        $roles = $em->getRepository('AppBundle:Role')->findAll();

        for ($i = 0; $i < count($roles); $i = $i + 3) {
            // findBy is the only working method in fixtures
            if (!$em->getRepository('AppBundle:Privilege')->findBy(['name' => $roles[$i]])) {
                $privilege = new Privilege();
                $privilege->setName($roles[$i])
                    ->setType($roles[$i + 1])
                    ->setLabel($roles[$i + 2])
                    ->setReadOnly();

                $em->persist($privilege);
            }
        }

        $em->flush();
    }
}
