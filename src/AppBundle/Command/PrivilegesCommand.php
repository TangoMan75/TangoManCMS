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
            'comment',
            'page',
            'post',
            'privilege',
            'role',
            'tag',
            'user',
        ];

        foreach ($privileges as $name) {
            if (!$em->getRepository('AppBundle:Privilege')->findBy(['name' => $name])) {

                $privilege = new Privilege();
                $privilege->setName($name)
                    ->setCanCreate(true)
                    ->setCanRead(true)
                    ->setCanUpdate(true)
                    ->setCanDelete(true);

                $em->persist($privilege);
                $em->flush();

                $output->writeln(
                    '<question>Privilege "'.$name.'" created.</question>'
                );
            } else {
                $output->writeln('<question>Privilege "'.$name.'" exists already.</question>');
            }
        }

    }
}