<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Media;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadFiles implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $rootdir;

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
        $this->rootdir = $this->container->getParameter('kernel.root_dir')."/../web";

        $faker = Factory::create('fr_FR');

        // Gets users
        // findBy is the only working method in fixtures
        $users = $em->getRepository('AppBundle:User')->findBy([], null, 100);

        // Gets pages
        $pages = $em->getRepository('AppBundle:Page')->findAll();

        // Get pptx
        $pptx = array_map(
            function ($filename) {
                return basename($filename);
            },
            glob($this->rootdir."/uploads/documents/*.{pptx,PPTX}", GLOB_BRACE)
        );

        // Get pdf
        $pdf = array_map(
            function ($filename) {
                return basename($filename);
            },
            glob($this->rootdir."/uploads/documents/*.{pdf,PDF}", GLOB_BRACE)
        );

        foreach ($users as $user) {

            // Creates between 1 & 10 medias for each user
            for ($i = 0; $i < mt_rand(1, 10); $i++) {

                for ($j = 0; $j < count($pptx); $j++) {
                    $doc = new Media();
                    $doc->addCategory('pptx')
                        ->addCategory('file')
                        ->addCategory('document')
                        ->setTitle($faker->sentence(4, true))
                        ->setText($faker->text(mt_rand(100, 255)))
                        ->setDocumentFileName($pptx[$j])
                        ->setCreated($faker->dateTimeThisYear($max = 'now'))
                        ->setUser($user)
                        ->setPage($pages[mt_rand(0, count($pages) - 1)])
                        ->setPublished($i % 2);
                    $em->persist($doc);
                }

                for ($j = 0; $j < count($pdf); $j++) {
                    $doc = new Media();
                    $doc->addCategory('pdf')
                        ->addCategory('file')
                        ->addCategory('document')
                        ->setTitle($faker->sentence(4, true))
                        ->setText($faker->text(mt_rand(100, 255)))
                        ->setDocumentFileName($pdf[$j])
                        ->setCreated($faker->dateTimeThisYear($max = 'now'))
                        ->setPage($pages[mt_rand(0, count($pages) - 1)])
                        ->setUser($user)
                        ->setPublished($i % 2);
                    $em->persist($doc);
                }
            }

            $em->flush();
        }
    }
}
