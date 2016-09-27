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

        $username = "admin";
        $email    = $faker->safeEmail;
        $password = '$2y$13$inrUj0hzQtPL2qvJ7/vtC.7QSNV.LmzUzBCMazMunMRdYjMMI2.Ha';
        $token    = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $role     = ['ROLE_ADMIN'];

        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword($password);
        $user->setToken($token);
        $user->setRoles($role);

        $manager->persist($user);

        for ($i=0; $i < 10; $i++){

            $username = $faker->userName;
            $email    = $faker->safeEmail;
            $password = $faker->password;
            $token    = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
            $role     = ['ROLE_USER'];

            $user = new User();
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setPassword($password);
            $user->setToken($token);
            $user->setRoles($role);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
