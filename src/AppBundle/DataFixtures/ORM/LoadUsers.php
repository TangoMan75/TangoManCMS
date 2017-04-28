<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
        return 3;
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
        $roles = ['ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_SUPER_USER', 'ROLE_USER'];

        // Load Users
        for ($i = 1; $i <= 100; $i++) {

            // Makes sure user doesn't exist
            // findBy is the only working method in fixtures
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
