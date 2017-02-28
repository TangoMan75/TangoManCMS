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
            ->setDescription('Creates ROLE_ADMIN account based on parameters.yml')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // When no admin found
        if ( !$this->getContainer()->get('em')->repository('AppBundle:User')->findByRoles(['ROLE_ADMIN']) ) {

            $email = $this->getContainer()->getParameter('mailer_from');
            $username = $this->getContainer()->getParameter('site_author');

            $encoder = $this->getContainer()->get('security.password_encoder');
            // Generating admin account with pwd: "321"
            $user = new User();
            $user->setUsername($username)
                ->setEmail($email)
                ->setPassword($encoder->encodePassword($user, "321"))
                ->setRoles(['ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_SUPER_USER', 'ROLE_USER'])
                ->setBio("<p>".$faker->text(mt_rand(600, 1200))."</p>")
            ;

            $this->getContainer()->get('em')->save($user);
            $output->writeln('<question>'.$username.' account created with password: "321"</question>');

        } else {

            $output->writeln('<question>'.$username.' account exists already.</question>');

        }
    }

}
