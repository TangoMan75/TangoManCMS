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
            ->setDescription('Creates admin account on remote server.')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ( !$this->getContainer()->get('em')->repository('AppBundle:User')->findByRoles(['ROLE_ADMIN']) ) {

            $encoder = $this->getContainer()->get('security.password_encoder');
            // Generating admin account with pwd: "321"
            $user = new User();
            $user->setUsername("admin")
                ->setEmail($this->getContainer()->getParameter('mailer_from'))
                ->setPassword($encoder->encodePassword($user, "321"))
                ->setRoles(['ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_SUPER_USER', 'ROLE_USER'])
                ->setBio("<p>".$faker->text(mt_rand(600, 1200))."</p>")
            ;

            $this->getContainer()->get('em')->save($user);
            $output->writeln('<question>"Admin" account created with password: "321"</question>');

        } else {

            $output->writeln('<question>"Admin" account exists already.</question>');

        }
    }

}
