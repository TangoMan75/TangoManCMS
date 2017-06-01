<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Vote;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadVotes
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\DataFixtures\ORM
 */
class LoadVotes implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
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
        return 70;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        for ($i = 0; $i < 200; $i++) {
            $vote = new Vote();
            $vote->setValue(1);
            $em->persist($vote);
        }

        $em->flush();
    }
}
