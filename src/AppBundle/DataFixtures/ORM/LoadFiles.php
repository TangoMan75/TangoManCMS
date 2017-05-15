<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Post;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadFiles
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\DataFixtures\ORM
 */
class LoadFiles implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
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
        return 11;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {

        $faker = Factory::create('fr_FR');

        $rootdir = $this->container->getParameter('kernel.root_dir')."/../web";
        // Get pptx
        $pptx = array_map(
            function ($filename) {
                return basename($filename);
            },
            glob($rootdir."/uploads/documents/*.{pptx,PPTX}", GLOB_BRACE)
        );

        // Get pdf
        $pdf = array_map(
            function ($filename) {
                return basename($filename);
            },
            glob($rootdir."/uploads/documents/*.{pdf,PDF}", GLOB_BRACE)
        );

        for ($i = 0; $i < count($pptx); $i++) {
            $doc = new Post();
            $doc
                ->setCreated($faker->dateTimeThisYear($max = 'now'))
                ->setDocumentFileName($pptx[$i])
                ->setHits(mt_rand(0, 50))
                ->setPublished($i % 2)
                ->setText($faker->text(mt_rand(100, 255)))
                ->setTitle($faker->sentence(4, true))
                ->setType('pptx');
            $em->persist($doc);
        }

        for ($i = 0; $i < count($pdf); $i++) {
            $doc = new Post();
            $doc
                ->setCreated($faker->dateTimeThisYear($max = 'now'))
                ->setDocumentFileName($pdf[$i])
                ->setHits(mt_rand(0, 50))
                ->setPublished($i % 2)
                ->setText($faker->text(mt_rand(100, 255)))
                ->setTitle($faker->sentence(4, true))
                ->setType('pdf');
            $em->persist($doc);
        }

        $em->flush();
    }
}
