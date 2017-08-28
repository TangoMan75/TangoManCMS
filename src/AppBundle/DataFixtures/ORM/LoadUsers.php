<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadUsers
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\DataFixtures\ORM
 */
class LoadUsers implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
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
        return 40;
    }

    /**
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        $faker = Factory::create('fr_FR');

        // Pasword encoder
        $encoder = $this->container->get('security.password_encoder');

        // Default roles
        $roles = $em->getRepository('AppBundle:Role')->findAll();
        $roleUser = $em->getRepository('AppBundle:Role')->findOneBy(['type' => 'ROLE_USER']);

        // Generating user account with password '321'
        $user = new User();
        $user
            ->setUsername('user')
            ->setEmail('user@localhost.dev')
            ->setPassword($encoder->encodePassword($user, '321'))
            ->addRole($roleUser)
            ->setBio('<p>Ceci est un simple compte utilisateur.</p>')
        ;

        $em->persist($user);
        $em->flush();

        // Load Users
        for ($i = 0; $i < 1000; $i++) {
            // Makes sure user doesn't exist
            do {
                $username = $faker->userName;
            } while ($em->getRepository('AppBundle:User')->findBy(['username' => $username]));

            $user = new User();
            $user->setUsername($username)
                ->setEmail($username.'@'.$faker->safeEmailDomain)
                // ->setEmail($username.'@'.$faker->freeEmailDomain)
                // ->setEmail($faker->email)
                // ->setPassword($encoder->encodePassword($user, $username))
                ->setPassword($i % 2 ? '$2y$13$lPJ8KtvstiwLmvTAX3SxoOL3/Nj.sZQBwh7Pyldw0GAl8mnyU8Wg.' : null)
                ->addRole($roles[mt_rand(0, 3)])
                // ->setAvatar('data:image/jpeg;base64,'.$faker->regexify('[A-Za-z0-9/+=]{1000}'))
                ->setCreated($faker->dateTimeThisYear($max = 'now'))
                ->setBio('<p>'.$faker->text(mt_rand(600, 1200)).'</p>');

            $em->persist($user);

            // Manager flushes every ten persisted items
            // This avoids memory overflow when persisting large numbers of fixtures
            if ($i % 10 === 0) {
                $em->flush();
            }
        }
    }
}
