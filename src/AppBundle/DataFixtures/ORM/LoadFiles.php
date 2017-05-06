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
        return 13;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {

        $faker = Factory::create('fr_FR');

        // Gets users
        // findBy seems to be the only working method in fixtures
        $users = $em->getRepository('AppBundle:User')->findBy([], null, 100);

        // Gets section
        $sections = $em->getRepository('AppBundle:Section')->findAll();

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

        foreach ($users as $user) {

            // Creates between 1 & 10 medias for each user
            for ($i = 0; $i < mt_rand(1, 10); $i++) {

                for ($j = 0; $j < count($pptx); $j++) {
                    $doc = new Post();
                    $doc->addCategory('pptx')
                        ->addCategory('file')
                        ->addCategory('document')
                        ->setTitle($faker->sentence(4, true))
                        ->setText($faker->text(mt_rand(100, 255)))
                        ->setDocumentFileName($pptx[$j])
                        ->setCreated($faker->dateTimeThisYear($max = 'now'))
                        ->setUser($user)
                        ->addSection($sections[mt_rand(0, count($sections) - 1)])
                        ->setPublished($i % 2);
                    $em->persist($doc);
                }

                for ($j = 0; $j < count($pdf); $j++) {
                    $doc = new Post();
                    $doc->addCategory('pdf')
                        ->addCategory('file')
                        ->addCategory('document')
                        ->setTitle($faker->sentence(4, true))
                        ->setText($faker->text(mt_rand(100, 255)))
                        ->setDocumentFileName($pdf[$j])
                        ->setCreated($faker->dateTimeThisYear($max = 'now'))
                        ->addSection($sections[mt_rand(0, count($sections) - 1)])
                        ->setUser($user)
                        ->setPublished($i % 2);
                    $em->persist($doc);
                }
            }

            $em->flush();
        }
    }
}
