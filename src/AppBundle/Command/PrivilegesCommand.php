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
            'Commentaire - Créer'     => 'ROLE_COMMENT_CREATE',
            'Commentaire - Lire'      => 'ROLE_COMMENT_READ',
            'Commentaire - Modifier'  => 'ROLE_COMMENT_UPDATE',
            'Commentaire - Supprimer' => 'ROLE_COMMENT_DELETE',
            'Page - Créer'            => 'ROLE_PAGE_CREATE',
            'Page - Lire'             => 'ROLE_PAGE_READ',
            'Page - Modifier'         => 'ROLE_PAGE_UPDATE',
            'Page - Supprimer'        => 'ROLE_PAGE_DELETE',
            'Publication - Créer'     => 'ROLE_POST_CREATE',
            'Publication - Lire'      => 'ROLE_POST_READ',
            'Publication - Modifier'  => 'ROLE_POST_UPDATE',
            'Publication - Supprimer' => 'ROLE_POST_DELETE',
            'Privilège - Créer'       => 'ROLE_PRIVILEGE_CREATE',
            'Privilège - Lire'        => 'ROLE_PRIVILEGE_READ',
            'Privilège - Modifier'    => 'ROLE_PRIVILEGE_UPDATE',
            'Privilège - Supprimer'   => 'ROLE_PRIVILEGE_DELETE',
            'Role - Créer'            => 'ROLE_ROLE_CREATE',
            'Role - Lire'             => 'ROLE_ROLE_READ',
            'Role - Modifier'         => 'ROLE_ROLE_UPDATE',
            'Role - Supprimer'        => 'ROLE_ROLE_DELETE',
            'Étiquette - Créer'       => 'ROLE_TAG_CREATE',
            'Étiquette - Lire'        => 'ROLE_TAG_READ',
            'Étiquette - Modifier'    => 'ROLE_TAG_UPDATE',
            'Étiquette - Supprimer'   => 'ROLE_TAG_DELETE',
            'Utilisateur - Créer'     => 'ROLE_USER_CREATE',
            'Utilisateur - Lire'      => 'ROLE_USER_READ',
            'Utilisateur - Modifier'  => 'ROLE_USER_UPDATE',
            'Utilisateur - Supprimer' => 'ROLE_USER_DELETE',
        ];

        foreach ($privileges as $name => $type) {
            if (!$em->getRepository('AppBundle:Privilege')->findBy(['privilege' => $privileges[$i + 3]])) {
                $privilege = new Privilege();
                $privilege->setName($name)
                    ->setType($type);

                $em->persist($privilege);
                $output->writeln(
                    'Privilege "'.$privilege->getName().'" created.</question>'
                );
            } else {
                $output->writeln('Privilege "'.$name.'" exists already.</question>');
            }
        }

        $em->flush();
    }
}
