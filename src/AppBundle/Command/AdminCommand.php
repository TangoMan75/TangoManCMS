<?php

/*
 * This file is part of the TangoManCMS package.
 *
 * (c) Matthias Morin <matthias.morin@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
             ->setDescription(
                 'Creates ROLE_SUPER_ADMIN account based on parameters.yml'
             );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em       = $this->getContainer()->get('doctrine')->getManager();
        $username = $this->getContainer()->getParameter('super_admin_username');

        $roleSuperAdmin = $em->getRepository('AppBundle:Role')->findOneBy(
            ['type' => 'ROLE_SUPER_ADMIN']
        );
        //        $superAdmin = $em->getRepository('AppBundle:User')->findBy(['roles' => $roleSuperAdmin]);

        $superAdmin = $em->getRepository('AppBundle:User')->findBy(
            ['username' => $username]
        );

        // Creates super admin account
        if ( ! $superAdmin) {

            $email   = $this->getContainer()->getParameter('mailer_from');
            $pwd     = $this->getContainer()->getParameter('super_admin_pwd');
            $encoder = $this->getContainer()->get('security.password_encoder');

            $user = new User();
            $user
                ->addRole($roleSuperAdmin)
                ->setBio('<p>Ceci est le compte super administrateur.</p>')
                ->setEmail($email)
                ->setPassword($encoder->encodePassword($user, $pwd))
                ->setUsername($username);

            $em->persist($user);
            $em->flush();

            $output->writeln(
                $user.' account created with password: "'.$pwd.'"'
            );
        } else {
            $output->writeln('Sorry, user "'.$username.'" exists already.');
        }
    }
}
