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

        // Default roles
        $roles = [
            'Utilisateur'          => 'ROLE_USER',
            'Super Utilisateur'    => 'ROLE_SUPER_USER',
            'Administrateur'       => 'ROLE_ADMIN',
            'Super Administrateur' => 'ROLE_SUPER_ADMIN',
        ];

        $privileges = $em->getRepository('AppBundle:Privilege')->findAll();

        foreach ($roles as $key => $item) {

            // findBy is the only working method in fixtures
            if (!$em->getRepository('AppBundle:Role')->findBy(['role' => $item])) {
                $role = new Role();
                $role->setName($key)
                    ->setRole($item);

                $em->persist($role);
                $output->writeln(
                    'Role "'.$role.'" created.</question>'
                );
            } else {
                $output->writeln('Role "'.$key.'" exists already.</question>');
            }
        }

        $em->flush();
    }
}