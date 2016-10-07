<?php
/**
 * Created by PhpStorm.
 * User: thibaulthenry
 * Date: 18/05/2016
 * Time: 14:35
 */
namespace AppBundle\Command;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AdminCommand extends ContainerAwareCommand
{
    /**
     * creates command with description
     */
    protected function configure()
    {
        $this
            ->setName('admin')
            ->setDescription('Creates admin account on remote server.')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ( !$this->getContainer()->get('em')->repository('AppBundle:User')->findByRoles(['ROLE_ADMIN']) ){

            $encoder = $this->getContainer()->get('security.password_encoder');
            // Generating admin account with pwd: "321"
            $user = new User();
            $user->setUsername('admin');
            $user->setEmail('tech@argus-lab.com');
            $user->setPassword($encoder->encodePassword($user, "321"));
            $user->setRoles(['ROLE_ADMIN']);
            $this->getContainer()->get('em')->save($user);
            $output->writeln('<question>"Admin" account created with password: "321"</question>');

        } else {

            $output->writeln('<question>"Admin" account exists already.</question>');

        }
    }

}
