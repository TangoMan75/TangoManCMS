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
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        // Default Roles
        $roles = [
            'Administrateur'       => 'ROLE_ADMIN',
            'Super Administrateur' => 'ROLE_SUPER_ADMIN',
            'Super Utilisateur'    => 'ROLE_SUPER_USER',
            'Utilisateur'          => 'ROLE_USER',
        ];

        foreach ($roles as $name) {
            // findBy is the only working method in fixtures
            if (!$em->getRepository('AppBundle:Role')->findBy(['name' => $name])) {
                $role = new Role();
                $role->setName($name);
                $em->persist($role);
                $output->writeln('<question>Role "'.$role.'" created with success.</question>');
            }
        }

        $em->flush();
    }
}