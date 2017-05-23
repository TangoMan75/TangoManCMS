<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Stat;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadStat
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\DataFixtures\ORM
 */
class LoadStats implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
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
        return 6;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
//        for ($i = 0; $i < 25; $i++) {
//            $stat = new Stat();
//            $stat
//                ->setDislikes(mt_rand(0, 100))
//                ->setLikes(mt_rand(0, 100))
//                ->setStars(mt_rand(0, 5))
//                ->setViews(mt_rand(0, 100));
//
//            $em->persist($stat);
//        }
//
//        $em->flush();
    }
}
