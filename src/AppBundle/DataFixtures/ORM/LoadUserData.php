<?php
/**
 * Created by PhpStorm.
 * User: MORIN Matthias
 * Date: 22/09/2016
 * Time: 17:02
 */

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Post;
use Faker\Factory;

class LoadUserData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        for ($i=0; $i < 10; $i++){

            $author = $faker->name;
            $title = $faker->sentence(4, true);
            $text = $faker->text;

            $post = new Post();
            $post->setAuthor($author);
            $post->setTitle($title);
            $post->setContent($text);
            $manager->persist($post);
        }

        $manager->flush();
    }
}
