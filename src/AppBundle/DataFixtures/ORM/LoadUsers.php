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
use AppBundle\Entity\User;
use Faker\Factory;

class LoadUsers implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        for ($i=0; $i < 60; $i++){

            $username = $faker->userName;
            $email    = $faker->safeEmail;
            $password = $faker->password;
            $token    = $faker->uuid;
            $role     = 'user';

            $user = new User();
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setPassword($password);
            $user->setToken($token);
            $user->setRole($role);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
