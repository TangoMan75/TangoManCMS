<?php

namespace TangoMan\UserBundle\Command;

use TangoMan\UserBundle\Entity\Role;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RolesCommand extends ContainerAwareCommand
{
    /**
     * Creates command with description
     */
    protected function configure()
    {
        $this->setName('tangoman:user:roles')
            ->setDescription('Creates default roles');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();


        // Default roles
        $roles = [
            'glyphicon glyphicon-pawn',   'primary', 'Utilisateur',          'ROLE_USER',
            'glyphicon glyphicon-bishop', 'success', 'Super Utilisateur',    'ROLE_SUPER_USER',
            'glyphicon glyphicon-tower',  'warning', 'Administrateur',       'ROLE_ADMIN',
            'glyphicon glyphicon-king',   'danger',  'Super Administrateur', 'ROLE_SUPER_ADMIN',
        ];

        for ($i = 0; $i < count($roles); $i = $i + 4) {
            if (!$em->getRepository('UserBundle:Role')->findBy(['role' => $roles[$i + 3]])) {
                $role = new Role();
                $role->setIcon($roles[$i])
                    ->setLabel($roles[$i + 1])
                    ->setName($roles[$i + 2])
                    ->setType($roles[$i + 3]);

                $em->persist($role);
                $output->writeln(
                    'Role "'.$role->getName().'" created.</question>'
                );
            } else {
                $output->writeln('Role "'.$roles[$i + 2].'" exists already.</question>');
            }
        }

        $em->flush();
    }
}
