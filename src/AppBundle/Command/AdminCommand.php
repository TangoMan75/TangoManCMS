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
        $this->setName('admin')
            ->setDescription('Creates ROLE_SUPER_ADMIN account based on parameters.yml');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $superAdmin = $em->getRepository('AppBundle:Role')->findBy(['role'=>'ROLE_SUPER_ADMIN']);

        die(dump($superAdmin));

        // Creates super admin account
        if (!$em->getRepository('AppBundle:User')->findBy(['roles'=>$superAdmin])) {

            $email = $this->getContainer()->getParameter('mailer_from');
            $username = $this->getContainer()->getParameter('super_admin_username');
            $pwd = $this->getContainer()->getParameter('super_admin_pwd');

            $encoder = $this->getContainer()->get('security.password_encoder');
            $user = new User();
            $user->setUsername($username)
                ->setEmail($email)
                ->setPassword($encoder->encodePassword($user, $pwd))
                ->addRole($superAdmin)
                ->setBio('Ceci est le compte super administrateur.');

            $em->persist($user);
            $em->flush();

            $output->writeln('<question>'.$user->getUsername().' account created with password: "'.$pwd.'"</question>');
        } else {
            $output->writeln('<question>Sorry, at least one account with ROLE_SUPER_ADMIN exists already.</question>');
        }
    }

}
