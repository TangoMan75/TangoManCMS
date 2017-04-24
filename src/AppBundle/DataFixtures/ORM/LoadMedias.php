<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Media;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadMedias implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
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
        return 7;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        $faker = Factory::create('fr_FR');

        // Gets users
        // findBy is the only working method in fixtures
        $users = $em->getRepository('AppBundle:User')->findBy([], null, 100);

        // Gets pages
        $pages = $em->getRepository('AppBundle:Page')->findAll();

        $links = [
            'https://www.youtube.com//watch?v=4TlqSWm9wQ8',
            'https://www.youtube.com//watch?v=96veA49pHSY',
            'https://www.youtube.com//watch?v=AFM5QJnB_Hw',
            'https://www.youtube.com//watch?v=azCRuwtE_n0',
            'https://www.youtube.com//watch?v=bdjptzUrgRY',
            'https://www.youtube.com//watch?v=daioWlkH7ZI',
            'https://www.youtube.com//watch?v=FnHaPVP-opw',
            'https://www.youtube.com//watch?v=ITfe-cJbfMc',
            'https://www.youtube.com//watch?v=q9Tq7OUtYNY',
            'https://www.youtube.com//watch?v=UKIJ5lZPQX8',
            'https://www.youtube.com//watch?v=wJhU1BLVezw',
            'https://www.youtube.com//watch?v=WwvwvjNjQaQ',
            'http://www.dailymotion.com/video/x5ghopx_le-nouveau-projet-fou-d-elon-musk-relier-le-cerveau-humain-a-un-ordinateur_tech',
            'http://www.dailymotion.com/video/x5ge5ut_ce-mec-est-le-roi-des-petits-effets-speciaux-du-quotidien_fun',
            'https://gist.github.com/vovadocent/7b4a58d7d9e8abb3c68dd82607c2bbf0',
        ];

        foreach ($users as $user) {

            shuffle($links);

            // Creates between 1 & 10 medias for each user
            for ($i = 0; $i < mt_rand(1, 10); $i++) {

                $media = new Media();
                $media->setUser($user)
                    ->setTitle($faker->sentence(4, true))
                    ->setDescription($faker->text(mt_rand(100, 255)))
                    ->setLink($links[$i])
                    ->setCreated($faker->dateTimeThisYear($max = 'now'))
                    ->setPage($pages[mt_rand(0, count($pages) - 1)])
                    ->setPublished($i % 2);

                $tags = $em->getRepository('AppBundle:Tag')->findAll();

                for ($j = 0; $j < mt_rand(0, 5); $j++) {
                    $media->addTag($tags[mt_rand(0, 5)]);
                }

                $em->persist($media);
            }

            $em->flush();
        }
    }
}
