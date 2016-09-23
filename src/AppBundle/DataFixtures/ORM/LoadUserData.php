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

class LoadUserData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $post = new Post();
        $post->setAuthor('Matthias');
        $post->setTitle('Guy Teub');
        $post->setContent('La teub à Guy');
        $manager->persist($post);

        $manager->persist($post);
        $post = new Post();
        $post->setAuthor('Dark Vador');
        $post->setTitle('Je suis ton Père');
        $post->setContent('Blabla');
        $manager->persist($post);

        $post = new Post();
        $post->setAuthor('Princesse Leya');
        $post->setTitle('Je suis ta Soeur');
        $post->setContent('Blibli');
        $manager->persist($post);

        $manager->persist($post);
        $manager->flush();
    }
}
