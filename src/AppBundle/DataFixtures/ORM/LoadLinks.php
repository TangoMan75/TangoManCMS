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
 * Class LoadLinks
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\DataFixtures\ORM
 */
class LoadLinks implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
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
        return 120;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        $faker = Factory::create('fr_FR');

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
            'https://www.dailymotion.com/video/x5232zo_jaguar-i-pace-concept-2016_auto',
            'https://www.dailymotion.com/video/x5s849a',
            'https://www.dailymotion.com/video/x5sgd5i',
            'https://www.dailymotion.com/video/x2fwt7x',
            'https://www.dailymotion.com/video/x5s5oza',
            'https://www.dailymotion.com/video/x5stfst',
            'https://www.dailymotion.com/video/x5uv9wt',
            'https://www.dailymotion.com/video/x3si4sk',
            'https://www.dailymotion.com/video/x2tf0ae',
            'https://www.dailymotion.com/video/x2tf0bd',
            'https://www.dailymotion.com/video/x2qy6bg',
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
            'https://giphy.com/gifs/13ljyWMOwpvK4U',
            'https://giphy.com/gifs/bfd8hxbntgfBu',
            'https://giphy.com/gifs/7r5ERglWWUXC',
            'https://giphy.com/gifs/137eyPFEzNCi76',
            'https://giphy.com/gifs/e8ojIgE5pyr2U',
            'https://giphy.com/gifs/eELh4DHfPnWpO',
            'https://giphy.com/gifs/ZwdBbV1icmAso',
            'https://giphy.com/gifs/JHsos369mkc0M',
            'https://giphy.com/gifs/jd8uxAbZhuhPy',
            'https://giphy.com/gifs/3o6ZsYzxYAGlnacUTK',
            'https://giphy.com/gifs/26p3IyOBuGqFW',
            'https://giphy.com/gifs/J1YcBgUveXNkY',
            'https://twitter.com/Largus_auto/status/886978767826112513',
            'https://twitter.com/Largus_auto/status/892688963609268224',
            'https://twitter.com/Largus_auto/status/892687864596762624',
            'https://twitter.com/Largus_auto/status/892687311087042560',
            'https://twitter.com/Largus_auto/status/892685984357392384',
            'https://twitter.com/Largus_auto/status/892651699487346688',
            'https://twitter.com/Largus_auto/status/890237263879262208',
            'https://twitter.com/Largus_auto/status/890236661442813952',
            'https://twitter.com/Largus_auto/status/890235742370312193',
            'https://twitter.com/Largus_auto/status/890234768482344960',
            'https://twitter.com/Largus_auto/status/890233608044253185',
            'https://twitter.com/Largus_auto/status/888062202787356672',
            'https://twitter.com/Largus_auto/status/888061622836703233',
            'https://twitter.com/Largus_auto/status/888060780255510529',
        ];

        shuffle($links);
        for ($i = 0; $i < count($links); $i++) {
            $post = new Post();
            $post
                ->setCreated($faker->dateTimeThisYear($max = 'now'))
                ->setLink($links[$i])
                ->setPublished($i % 2)
                ->setText('<p>'.$faker->text(mt_rand(100, 255)).'</p>')
                ->setTitle($faker->sentence(4, true))
                ->setViews(mt_rand(0, 100));

            $em->persist($post);
        }

        $em->flush();
    }
}
