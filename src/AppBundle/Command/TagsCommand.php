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

        // Default Tags
        $tags = [
            'Alerte'       ,'warning'     ,'warning',
            'Argus 360'    ,'argus360'    ,'success',
            'Article'      ,'post'        ,'primary',
            'Daily Motion' ,'dailymotion' ,'danger',
            'Danger'       ,'danger'      ,'danger',
            'Document'     ,'document'    ,'default',
            'Défaut'       ,'default'     ,'default',
            'Embeddable'   ,'embeddable'  ,'default',
            'Fichier'      ,'file'        ,'default',
            'Gist'         ,'gist'        ,'primary',
            'Image'        ,'image'       ,'success',
            'Info'         ,'info'        ,'info',
            'Lien'         ,'link'        ,'warning',
            'Photo'        ,'picture'     ,'success',
            'Principal'    ,'primary'     ,'primary',
            'Succès'       ,'success'     ,'success',
            'Theta S'      ,'thetas'      ,'success',
            'Vidéo'        ,'video'       ,'danger',
            'Vimeo'        ,'vimeo'       ,'danger',
            'Youtube'      ,'youtube'     ,'danger',
        ];

        for ($i = 0; $i < count($tags); $i = $i + 4) {
            // findBy is the only working method in fixtures
            if (!$em->getRepository('AppBundle:Tag')->findBy(['name' => $tags[$i]])) {
                $tag = new Tag();
                $tag->setName($tags[$i])
                    ->setType($tags[$i + 1])
                    ->setLabel($tags[$i + 2])
                    ->setReadOnly();

                $em->persist($tag);

                $output->writeln('<question>Tag "'.$tag.'" created with type :"'.$tags[$i + 1].'"</question>');
            } else {
                $output->writeln('<question>Tag "'.$tags[$i].'" exists already.</question>');
            }
        }

        $em->flush();
    }
}