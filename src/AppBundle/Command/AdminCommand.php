<?php

namespace AppBundle\Command;


use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AdminCommand extends ContainerAwareCommand
{
    /**
     * Creates command with description
     */
    protected function configure()
    {
        $this
            ->setName('admin')
            ->setDescription('Creates ROLE_SUPER_ADMIN account based on parameters.yml')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // When no admin found
        if ( !$this->getContainer()->get('em')->repository('AppBundle:User')->findByRole('ROLE_SUPER_ADMIN') ) {

            $email = $this->getContainer()->getParameter('mailer_from');
            $username = $this->getContainer()->getParameter('site_author');
            $pwd = $this->getContainer()->getParameter('super_admin_pwd');

            $encoder = $this->getContainer()->get('security.password_encoder');
            $user = new User();
            $user->setUsername($username)
                ->setEmail($email)
                ->setPassword($encoder->encodePassword($user, $pwd))
                ->addRole('ROLE_SUPER_ADMIN')
                ->setBio("Ceci est le compte super administrateur.")
            ;

            $this->getContainer()->get('em')->save($user);
            $output->writeln('<question>'.$username.' account created with password: "'.$pwd.'"</question>');

        } else {

            $output->writeln('<question>'.$username.' account exists already.</question>');

        }
    }

}
