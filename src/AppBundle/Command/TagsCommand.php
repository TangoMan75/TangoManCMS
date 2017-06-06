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
            'Import',    'default', 'default',
            'Défaut',    'default', 'default',
            'Principal', 'primary', 'primary',
            'Info',      'info',    'info',
            'Succès',    'success', 'success',
            'Alerte',    'warning', 'warning',
            'Danger',    'danger',  'danger',
        ];

        $categories = [
            'Argus 360',   'argus360',    'success',
            'Article',     'post',        'primary',
            'Dailymotion', 'dailymotion', 'danger',
            'Galerie',     'gallery',     'default',
            'Document',    'document',    'default',
            'Dossier',     'folder',      'default',
            'Embed',       'embed',       'default',
            'En Avant',    'featured',    'primary',
            'Fichier',     'file',        'default',
            'Gist',        'gist',        'primary',
            'Image',       'image',       'success',
            'Lien',        'link',        'warning',
            'Photo',       'picture',     'success',
            'Publié',      'published',   'success',
            'Theta S',     'thetas',      'success',
            'Vidéo',       'video',       'danger',
            'Vimeo',       'vimeo',       'danger',
            'Youtube',     'youtube',     'danger',
        ];

        $documents = [
            'Document Libre Office',    'ods',         'default',
            'Document PDF',             'pdf',         'default',
            'Document Word',            'doc',         'default',
            'Fichier Texte',            'txt',         'default',
            'Image GIF',                'gif',         'success',
            'Image JPEG',               'jpeg',        'success',
            'Image JPG',                'jpg',         'success',
            'Image PNG',                'png',         'success',
            'Présentation Power Point', 'pptx',        'default',
            'Tableau CSV',              'csv',         'default',
            'Tableau Excel',            'xls',         'default',
            'Tableau Libre Office',     'odt',         'default',
        ];

        for ($i = 0; $i < count($tags); $i = $i + 3) {
            // findBy is the only working method in fixtures
            if (!$em->getRepository('AppBundle:Tag')->findBy(['type' => $tags[$i+1]])) {
                $tag = new Tag();
                $tag
                    ->setLabel($tags[$i + 2])
                    ->setName($tags[$i])
                    ->setType($tags[$i + 1]);

                $em->persist($tag);

                $output->writeln('Tag "'.$tag.'" created with type :"'.$tags[$i + 1].'"</question>');
            } else {
                $output->writeln('Tag "'.$tags[$i].'" exists already.</question>');
            }
        }

        $em->flush();
    }
}