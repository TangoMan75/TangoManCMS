<?php

namespace AppBundle\Command;

use AppBundle\Entity\Tag;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RelationsCommand extends ContainerAwareCommand
{
    /**
     * Creates command with description
     */
    protected function configure()
    {
        $this->setName('relations')
             ->setDescription('Tests relations');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        // findBy seems to be the only working method in fixtures
        $comments   = $em->getRepository('AppBundle:Comment')->findAll();
        $pages      = $em->getRepository('AppBundle:Page')->findAll();
        $posts      = $em->getRepository('AppBundle:Post')->findAll();
        $privileges = $em->getRepository('AppBundle:Privilege')->findAll();
        $roles      = $em->getRepository('AppBundle:Role')->findAll();
        $sections   = $em->getRepository('AppBundle:Section')->findAll();
        $tags       = $em->getRepository('AppBundle:Tag')->findAll();
        $users      = $em->getRepository('AppBundle:User')->findAll();

        $output->writeln(count($comments).' Comments found');
        $output->writeln(count($pages).' Pages found');
        $output->writeln(count($posts).' Posts found');
        $output->writeln(count($privileges).' Privileges found');
        $output->writeln(count($roles).' Roles found');
        $output->writeln(count($sections).' Sections found');
        $output->writeln(count($tags).' Tags found');
        $output->writeln(count($users).' Users found');

        // Comments
        $output->writeln('Linking Comments...');
        foreach ($comments as $comment) {
            $comment->setPost($posts[mt_rand(1, count($posts) - 1)]);
            $comment->setUser($users[mt_rand(1, count($users) - 1)]);
            $em->persist($comment);
        }
        $em->flush();
        $output->writeln('Linking Comments done.');

        // Pages
        $output->writeln('Linking Pages...');
        foreach ($pages as $page) {
            $page->addSection($sections[mt_rand(1, count($sections) - 1)]);
            $em->persist($page);
        }
        $em->flush();
        $output->writeln('Linking Pages done.');

        // Posts
        $output->writeln('Linking Posts...');
        foreach ($posts as $post) {
            $post->addComment($comments[mt_rand(1, count($comments) - 1)]);
            $post->addSection($sections[mt_rand(1, count($sections) - 1)]);
            // $post->addTag($tags[mt_rand(1, count($tags) - 1)]);
            $post->setUser($users[mt_rand(1, count($users) - 1)]);
            $em->persist($post);
        }
        $em->flush();
        $output->writeln('Linking Posts done.');

        // // Privileges
        // $output->writeln('Linking Privileges...');
        // foreach ($privileges as $privilege) {
        //     $privilege->addRole($roles[mt_rand(1, count($roles) - 1)]);
        //     $em->persist($privilege);
        // }
        // $em->flush();
        // $output->writeln('Linking Privileges done.');

        // // Roles
        // $output->writeln('Linking Roles...');
        // foreach ($roles as $role) {
        //     $role->addPrivilege($privileges[mt_rand(1, count($privileges) - 1)]);
        //     // $role->addUser($users[mt_rand(1, count($users) - 1)]);
        //     $em->persist($role);
        // }
        // $em->flush();
        // $output->writeln('Linking Roles done.');

        // Sections
        $output->writeln('Linking Sections...');
        foreach ($sections as $section) {
            $section->addPage($pages[mt_rand(1, count($pages) - 1)]);
            $section->addPost($posts[mt_rand(1, count($posts) - 1)]);
            // $section->addTag($tags[mt_rand(1, count($tags) - 1)]);
            $em->persist($section);
        }
        $em->flush();
        $output->writeln('Linking Sections done.');

        // // Tags
        // $output->writeln('Linking Tags...');
        // foreach ($tags as $tag) {
        //     $tag->addItem($pages[mt_rand(1, count($pages) - 1)]);
        //     $tag->addItem($posts[mt_rand(1, count($posts) - 1)]);
        //     $tag->addItem($sections[mt_rand(1, count($sections) - 1)]);
        //     $em->persist($tag);
        // }
        // $em->flush();
        // $output->writeln('Linking Tags done.');

        // Users
        $output->writeln('Linking Users...');
        foreach ($users as $user) {
            $user->addPost($posts[mt_rand(1, count($posts) - 1)]);
            // $user->addRole($roles[mt_rand(1, count($roles) - 1)]);
            $em->persist($user);
        }
        $em->flush();
        $output->writeln('Linking Users done.');
    }
}