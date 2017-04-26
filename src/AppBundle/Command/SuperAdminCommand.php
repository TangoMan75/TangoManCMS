<?php

namespace AppBundle\Command;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SuperAdminCommand extends ContainerAwareCommand
{
    /**
     * Creates command with description
     */
    protected function configure()
    {
        $this->setName('superadmin')
            ->setDescription('Creates ROLE_SUPER_ADMIN account.');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        // Creates super admin account
        if (!$em->getRepository('AppBundle:User')->findByRole('ROLE_SUPER_ADMIN')) {

            $encoder = $this->getContainer()->get('security.password_encoder');
            $user = new User();
            $user->setUsername('superadmin')
                ->setEmail('tangoman@free.fr')
                ->setPassword($encoder->encodePassword($user, '321'))
                ->addRole('ROLE_SUPER_ADMIN')
                ->setBio('Ceci est le compte super administrateur.');

            $em->persist($user);
            $em->flush();

            $output->writeln('<question>superadmin account created with password: 321</question>');
        } else {
            $output->writeln('<question>Sorry, at least one account with ROLE_SUPER_ADMIN exists already.</question>');
        }
    }

}
