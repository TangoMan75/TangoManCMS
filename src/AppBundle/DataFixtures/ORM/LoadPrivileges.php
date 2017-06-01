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
            'comment',
            'page',
            'post',
            'privilege',
            'role',
            'tag',
            'user',
        ];

        foreach ($privileges as $name) {
            $privilege = new Privilege();
            $privilege->setName($name)
                ->setCanCreate(true)
                ->setCanRead(true)
                ->setCanUpdate(true)
                ->setCanDelete(true);

            $em->persist($privilege);
        }

        $em->flush();
    }
}
