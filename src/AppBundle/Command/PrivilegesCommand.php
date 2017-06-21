<?php

namespace AppBundle\Command;

use AppBundle\Entity\Privilege;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PrivilegesCommand extends ContainerAwareCommand
{
    /**
     * Creates command with description
     */
    protected function configure()
    {
        $this->setName('privileges')
            ->setDescription('Creates default privileges');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        // Default Privileges
        $privileges = [
            'Privilège - Supprimer',   'danger',  'CAN_DELETE_PRIVILEGE', 'ROLE_SUPER_ADMIN',
            'Privilège - Modifier',    'danger',  'CAN_UPDATE_PRIVILEGE', 'ROLE_SUPER_ADMIN',
            'Privilège - Lire',        'danger',  'CAN_READ_PRIVILEGE',   'ROLE_SUPER_ADMIN',
            'Privilège - Créer',       'danger',  'CAN_CREATE_PRIVILEGE', 'ROLE_SUPER_ADMIN',
            'Role - Supprimer',        'danger',  'CAN_DELETE_ROLE',      'ROLE_SUPER_ADMIN',
            'Role - Modifier',         'danger',  'CAN_UPDATE_ROLE',      'ROLE_SUPER_ADMIN',
            'Role - Lire',             'danger',  'CAN_READ_ROLE',        'ROLE_SUPER_ADMIN',
            'Role - Créer',            'danger',  'CAN_CREATE_ROLE',      'ROLE_SUPER_ADMIN',
            'Page - Supprimer',        'warning', 'CAN_DELETE_PAGE',      'ROLE_ADMIN',
            'Page - Modifier',         'warning', 'CAN_UPDATE_PAGE',      'ROLE_ADMIN',
            'Page - Lire',             'warning', 'CAN_READ_PAGE',        'ROLE_ADMIN',
            'Page - Créer',            'warning', 'CAN_CREATE_PAGE',      'ROLE_ADMIN',
            'Utilisateur - Supprimer', 'warning', 'CAN_DELETE_USER',      'ROLE_ADMIN',
            'Utilisateur - Modifier',  'warning', 'CAN_UPDATE_USER',      'ROLE_ADMIN',
            'Utilisateur - Lire',      'warning', 'CAN_READ_USER',        'ROLE_ADMIN',
            'Utilisateur - Créer',     'warning', 'CAN_CREATE_USER',      'ROLE_ADMIN',
            'Étiquette - Supprimer',   'success', 'CAN_DELETE_TAG',       'ROLE_SUPER_USER',
            'Étiquette - Modifier',    'success', 'CAN_UPDATE_TAG',       'ROLE_SUPER_USER',
            'Étiquette - Lire',        'success', 'CAN_READ_TAG',         'ROLE_SUPER_USER',
            'Étiquette - Créer',       'success', 'CAN_CREATE_TAG',       'ROLE_SUPER_USER',
            'Publication - Supprimer', 'success', 'CAN_DELETE_POST',      'ROLE_SUPER_USER',
            'Publication - Modifier',  'success', 'CAN_UPDATE_POST',      'ROLE_SUPER_USER',
            'Publication - Lire',      'success', 'CAN_READ_POST',        'ROLE_SUPER_USER',
            'Publication - Créer',     'success', 'CAN_CREATE_POST',      'ROLE_SUPER_USER',
            'Commentaire - Supprimer', 'primary', 'CAN_DELETE_COMMENT',   'ROLE_USER',
            'Commentaire - Modifier',  'primary', 'CAN_UPDATE_COMMENT',   'ROLE_USER',
            'Commentaire - Lire',      'primary', 'CAN_READ_COMMENT',     'ROLE_USER',
            'Commentaire - Créer',     'primary', 'CAN_CREATE_COMMENT',   'ROLE_USER',
        ];

        $superAdmin = $em->getRepository('AppBundle:Role')->findOneBy(['type' => 'ROLE_SUPER_ADMIN']);

        for ($i = 0; $i < count($privileges); $i = $i + 4) {
            if (!$em->getRepository('AppBundle:Privilege')->findBy(['privilege' => $privileges[$i + 3]])) {

                $role = $em->getRepository('AppBundle:Role')->findOneBy(['type' => $privileges[$i + 3]]);
                $privilege = new Privilege();
                $privilege
                    ->setName($privileges[$i])
                    ->setLabel($privileges[$i + 1])
                    ->setType($privileges[$i + 2])
                    ->addRole($superAdmin)
                    ->addRole($role);

                $em->persist($privilege);
                $output->writeln(
                    'Privilege "'.$privilege->getName().'" created.</question>'
                );
            } else {
                $output->writeln('Privilege "'.$privileges[$i].'" exists already.</question>');
            }
        }

        $em->flush();
    }
}
