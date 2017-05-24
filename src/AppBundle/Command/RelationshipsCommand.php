<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RelationshipsCommand extends ContainerAwareCommand
{
    /**
     * Creates command with description
     */
    protected function configure()
    {
        $this->setName('relationships')
            ->setDescription('Tests relations');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        // findBy seems to be the only working method in fixtures
        $comments = $em->getRepository('AppBundle:Comment')->findAll();
        $pages = $em->getRepository('AppBundle:Page')->findAll();
        $posts = $em->getRepository('AppBundle:Post')->findAll();
        $privileges = $em->getRepository('AppBundle:Privilege')->findAll();
        $roles = $em->getRepository('AppBundle:Role')->findAll();
        $stats = $em->getRepository('AppBundle:Stat')->findAll();
        $sections = $em->getRepository('AppBundle:Section')->findAll();
        $tags = $em->getRepository('AppBundle:Tag')->findAll();
        $users = $em->getRepository('AppBundle:User')->findAll();

        $output->writeln(count($comments).' Comments found');
        $output->writeln(count($pages).' Pages found');
        $output->writeln(count($posts).' Posts found');
        $output->writeln(count($privileges).' Privileges found');
        $output->writeln(count($roles).' Roles found');
        $output->writeln(count($stats).' Stats found');
        $output->writeln(count($sections).' Sections found');
        $output->writeln(count($tags).' Tags found');
        $output->writeln(count($users).' Users found');

        // Comments->Posts
        $output->writeln('Linking Comments->Posts...');
        foreach ($comments as $comment) {
            $comment->setPost($posts[mt_rand(1, count($posts) - 1)]);
            $em->persist($comment);
        }
        $em->flush();
        $output->writeln('Done.');

        // Comments->Users
        $output->writeln('Linking Comments->Users...');
        foreach ($comments as $comment) {
            $comment->setUser($users[mt_rand(1, count($users) - 1)]);
            $em->persist($comment);
        }
        $em->flush();
        $output->writeln('Done.');

        // Pages->Sections
        $output->writeln('Linking Pages->Sections...');
        foreach ($pages as $page) {
            $page->addSection($sections[mt_rand(1, count($sections) - 1)]);
            $em->persist($page);
        }
        $em->flush();
        $output->writeln('Done.');

        // Pages->Stats
        $output->writeln('Linking Pages->Stats...');
        foreach ($pages as $page) {
            $page->addStat($sections[mt_rand(1, count($sections) - 1)]);
            $em->persist($page);
        }
        $em->flush();
        $output->writeln('Done.');

        // Posts->Comments
        $output->writeln('Linking Posts->Comments...');
        $j = 0;
        foreach ($posts as $post) {
            $post->addComment($comments[mt_rand(1, count($comments) - 1)]);
            $post->addSection($sections[mt_rand(1, count($sections) - 1)]);

            shuffle($tags);
            for ($i = 0; $i < mt_rand(0, 4); $i++) {
                $post->addTag($tags[$i]);
            }

            if ($j < count($stats)) {
                $post->addStat($stats[$j++]);
            }

            $post->setUser($users[mt_rand(1, count($users) - 1)]);
            $em->persist($post);
        }
        $em->flush();
        $output->writeln('Done.');

        // Privileges
        $output->writeln('Linking Privileges...');
        foreach ($privileges as $privilege) {
            $privilege->addRole($roles[mt_rand(1, count($roles) - 1)]);
            $em->persist($privilege);
        }
        $em->flush();
        $output->writeln('Done.');

        // Roles
        $output->writeln('Linking Roles...');
        foreach ($roles as $role) {
            $role->addPrivilege($privileges[mt_rand(1, count($privileges) - 1)]);
            // $role->addUser($users[mt_rand(1, count($users) - 1)]);
            $em->persist($role);
        }
        $em->flush();
        $output->writeln('Done.');

        // Stat->Post
        $output->writeln('Linking Stat->Post...');
        foreach ($stats as $stat) {
            $stat->addPost($posts[mt_rand(1, count($posts) - 1)]);
            $stat->addUser($users[mt_rand(1, count($users) - 1)]);
            $em->persist($stat);
        }
        $em->flush();
        $output->writeln('Done.');

        // Stat->Media
        $output->writeln('Linking Stat->Media...');
        foreach ($stats as $stat) {
            $stat->addMedia($medias[mt_rand(1, count($medias) - 1)]);
            $stat->addUser($users[mt_rand(1, count($users) - 1)]);
            $em->persist($stat);
        }
        $em->flush();
        $output->writeln('Done.');

        // Stat->Page
        $output->writeln('Linking Stat->Page...');
        foreach ($stats as $stat) {
            $stat->addPage($pages[mt_rand(1, count($pages) - 1)]);
            $stat->addUser($users[mt_rand(1, count($users) - 1)]);
            $em->persist($stat);
        }
        $em->flush();
        $output->writeln('Done.');

        // Sections
        $output->writeln('Linking Sections...');
        foreach ($sections as $section) {
            $section->addPage($pages[mt_rand(1, count($pages) - 1)]);
            $section->addPost($posts[mt_rand(1, count($posts) - 1)]);
            // $section->addTag($tags[mt_rand(1, count($tags) - 1)]);
            $em->persist($section);
        }
        $em->flush();
        $output->writeln('Done.');

        // Tags
        $output->writeln('Linking Tags...');
        foreach ($tags as $tag) {
            $tag->addItem($pages[mt_rand(1, count($pages) - 1)]);
            $tag->addItem($posts[mt_rand(1, count($posts) - 1)]);
            $tag->addItem($sections[mt_rand(1, count($sections) - 1)]);
            $em->persist($tag);
        }
        $em->flush();
        $output->writeln('Done.');

        // Users
        $output->writeln('Linking Users...');
        foreach ($users as $user) {
            $user->addPost($posts[mt_rand(1, count($posts) - 1)]);
            $user->addRole($roles[mt_rand(1, count($roles) - 1)]);
            $user->addStat($stats[mt_rand(1, count($stat) - 1)]);
            $em->persist($user);
        }
        $em->flush();
        $output->writeln('Done.');
    }
}