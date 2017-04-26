<?php

namespace AppBundle\Command;

use AppBundle\Entity\Tag;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TagsCommand extends ContainerAwareCommand
{
    /**
     * Creates command with description
     */
    protected function configure()
    {
        $this->setName('tags')
            ->setDescription('Creates default tags');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        // Load Tags
        $tags = [
            'Défaut'    => 'default',
            'Principal' => 'primary',
            'Info'      => 'info',
            'Succès'    => 'success',
            'Alerte'    => 'warning',
            'Danger'    => 'danger',
        ];

        foreach ($tags as $name => $type) {
            // findBy is the only working method in fixtures
            if (!$em->getRepository('AppBundle:Tag')->findBy(['name' => $name])) {
                $tag = new Tag();
                $tag->setName($name)
                    ->setType($type)
                    ->setReadOnly();

                $em->persist($tag);
                $output->writeln('<question>Tag '.$tag.' created with type'.$type.'</question>');
            }
        }

        $em->flush();
    }
}