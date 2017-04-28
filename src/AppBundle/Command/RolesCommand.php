<?php

namespace AppBundle\Command;

use AppBundle\Entity\Role;
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
        $this->setName('roles')
            ->setDescription('Creates default roles');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        // Default Roles
        $roles = [
            'Utilisateur',
            'ROLE_USER',
            0,
            'Super Utilisateur',
            'ROLE_SUPER_USER',
            1,
            'Administrateur',
            'ROLE_ADMIN',
            2,
            'Super Administrateur',
            'ROLE_SUPER_ADMIN',
            3,
        ];

        for ($i = 0; $i < 12; $i = $i + 3) {
            // findBy is the only working method in fixtures
            if (!$em->getRepository('AppBundle:Role')->findBy(['role' => $roles[$i + 1]])) {
                $role = new Role();
                $role->setName($roles[$i])
                    ->setRole($roles[$i + 1])
                    ->setHierarchy($roles[$i + 2])
                    ->setReadOnly();

                $em->persist($role);
                $output->writeln(
                    '<question>Role "'.$role.'" created with hierarchy level "'.$roles[$i + 2].'".</question>'
                );
            } else {
                $output->writeln('<question>Role "'.$roles[$i].'" exists already.</question>');
            }
        }

        $em->flush();
    }
}