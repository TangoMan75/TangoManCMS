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
 * Class LoadGists
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\DataFixtures\ORM
 */
class LoadGists implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
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
        return 12;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        $faker = Factory::create('fr_FR');

        // findBy seems to be the only working method in fixtures
        $sections = $em->getRepository('AppBundle:Section')->findAll();
        $tags = $em->getRepository('AppBundle:Tag')->findAll();
        $users = $em->getRepository('AppBundle:User')->findBy([], null, 100);

        $links = [
            'https://gist.github.com/axooh/ec2348455e1414727676',
            'https://gist.github.com/damlys/9282d9081d6607244b5abfc1f5b6cdeb',
            'https://gist.github.com/FabianSchmick/40a9a406df6214e68853e8886587ffb7',
            'https://gist.github.com/Infernosquad/6106749',
            'https://gist.github.com/sergiors/75bbadcece53ed89f632',
            'https://gist.github.com/vovadocent/7b4a58d7d9e8abb3c68dd82607c2bbf0',
            'https://gist.github.com/jeremiahmarks/cdd92b98c355ff820e6a',
            'https://gist.github.com/eduardoslompo/1285847',
            'https://gist.github.com/tomekwojcik/2349469',
            'https://gist.github.com/pkpp1233/6a389aeb16c7d31aa769',
        ];

        foreach ($users as $user) {

            shuffle($links);

            // Creates between 1 & 10 gists for each user
            for ($i = 0; $i < mt_rand(1, 10); $i++) {

                $post = new Post();
                $post->setUser($user)
                    ->setTitle($faker->sentence(4, true))
                    ->setText('<p>'.$faker->text(mt_rand(100, 255)).'</p>')
                    ->setLink($links[$i])
                    ->setCreated($faker->dateTimeThisYear($max = 'now'))
                    ->addSection($sections[mt_rand(0, count($sections) - 1)])
                    ->setPublished($i % 2);

                // Adds between 1 & 5 random tags to post
                shuffle($tags);
                for ($j = 0; $j < mt_rand(1, 5); $j++) {
                    $post->addTag($tags[$j]);
                }

                $em->persist($post);
            }

            $em->flush();
        }
    }
}
