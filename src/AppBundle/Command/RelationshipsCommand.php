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
        $sections = $em->getRepository('AppBundle:Section')->findAll();
        $tags = $em->getRepository('AppBundle:Tag')->findAll();
        $users = $em->getRepository('AppBundle:User')->findAll();
        $votes = $em->getRepository('AppBundle:Vote')->findAll();

        $output->writeln(count($comments).' Comments found');
        $output->writeln(count($pages).' Pages found');
        $output->writeln(count($posts).' Posts found');
        $output->writeln(count($privileges).' Privileges found');
        $output->writeln(count($roles).' Roles found');
        $output->writeln(count($sections).' Sections found');
        $output->writeln(count($tags).' Tags found');
        $output->writeln(count($users).' Users found');
        $output->writeln(count($votes).' Votes found');

        /**
         * COMMENTS
         */

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

        /**
         * PAGES
         */

        // Pages->Sections
        $output->writeln('Linking Pages->Sections...');
        foreach ($pages as $page) {
            $page->addSection($sections[mt_rand(1, count($sections) - 1)]);
            $em->persist($page);
        }
        $em->flush();
        $output->writeln('Done.');

        // Pages->Tags
        $output->writeln('Linking Pages->Tags...');
        foreach ($pages as $page) {
            $page->addTag($tags[mt_rand(1, count($tags) - 1)]);
            $em->persist($page);
        }
        $em->flush();
        $output->writeln('Done.');

        /**
         * POSTS
         */

        // Posts->Comments
        $output->writeln('Linking Posts->Comments...');
        foreach ($posts as $post) {
            $post->addComment($comments[mt_rand(1, count($comments) - 1)]);
            $em->persist($post);
        }
        $em->flush();
        $output->writeln('Done.');

        // Posts->Sections
        $output->writeln('Linking Posts->Sections...');
        foreach ($posts as $post) {
            $post->addSection($sections[mt_rand(1, count($sections) - 1)]);
            $em->persist($post);
        }
        $em->flush();
        $output->writeln('Done.');

        // Posts->Tags
        $output->writeln('Linking Posts->Tags...');
        foreach ($posts as $post) {
            $post->addTag($tags[mt_rand(1, count($tags) - 1)]);
            $em->persist($post);
        }
        $em->flush();
        $output->writeln('Done.');

        // Posts->User
        $output->writeln('Linking Posts->User...');
        foreach ($posts as $post) {
            $post->setUser($users[mt_rand(1, count($users) - 1)]);
            $em->persist($post);
        }
        $em->flush();
        $output->writeln('Done.');

        // Posts->Votes
        $output->writeln('Linking Posts->Votes...');
        foreach ($posts as $post) {
            $post->addVote($votes[mt_rand(1, count($votes) - 1)]);
            $em->persist($post);
        }
        $em->flush();
        $output->writeln('Done.');

        /**
         * PRIVILEGES
         */

        // Privileges->Roles
        $output->writeln('Linking Privileges->Roles...');
        foreach ($privileges as $privilege) {
            $privilege->addRole($roles[mt_rand(1, count($roles) - 1)]);
            $em->persist($privilege);
        }
        $em->flush();
        $output->writeln('Done.');

        /**
         * ROLES
         */

        // Roles->Privileges
        $output->writeln('Linking Roles->Privileges...');
        foreach ($roles as $role) {
            $role->addPrivilege($privileges[mt_rand(1, count($privileges) - 1)]);
            $em->persist($role);
        }
        $em->flush();
        $output->writeln('Done.');

        // Roles->Users
        $output->writeln('Linking Roles->Users...');
        foreach ($roles as $role) {
            $role->addUser($users[mt_rand(1, count($users) - 1)]);
            $em->persist($role);
        }
        $em->flush();
        $output->writeln('Done.');

        /**
         * SECTIONS
         */

        // Sections->Pages
        $output->writeln('Linking Sections->Pages...');
        foreach ($sections as $section) {
            $section->addPage($pages[mt_rand(1, count($pages) - 1)]);
            $em->persist($section);
        }
        $em->flush();
        $output->writeln('Done.');

        // Sections->Posts
        $output->writeln('Linking Sections->Posts...');
        foreach ($sections as $section) {
            $section->addPost($posts[mt_rand(1, count($posts) - 1)]);
            $em->persist($section);
        }
        $em->flush();
        $output->writeln('Done.');

        // Sections->Tags
        $output->writeln('Linking Sections->Tags...');
        foreach ($sections as $section) {
            $section->addTag($tags[mt_rand(1, count($tags) - 1)]);
            $em->persist($section);
        }
        $em->flush();
        $output->writeln('Done.');

        /**
         * TAGS
         */

        // Tags->Pages
        $output->writeln('Linking Tags->Pages...');
        foreach ($tags as $tag) {
            $tag->addPage($pages[mt_rand(1, count($pages) - 1)]);
            $em->persist($tag);
        }
        $em->flush();
        $output->writeln('Done.');

        // Tags->Posts
        $output->writeln('Linking Tags->Posts...');
        foreach ($tags as $tag) {
            $tag->addPost($posts[mt_rand(1, count($posts) - 1)]);
            $em->persist($tag);
        }
        $em->flush();
        $output->writeln('Done.');

        // Tags->Sections
        $output->writeln('Linking Tags->Sections...');
        foreach ($tags as $tag) {
            $tag->addSection($sections[mt_rand(1, count($sections) - 1)]);
            $em->persist($tag);
        }
        $em->flush();
        $output->writeln('Done.');

        /**
         * USERS
         */

        // Users->Comments
        $output->writeln('Linking Users->Comments...');
        foreach ($users as $user) {
            $user->addComment($comments[mt_rand(1, count($comments) - 1)]);
            $em->persist($user);
        }
        $em->flush();
        $output->writeln('Done.');

        // Users->Posts
        $output->writeln('Linking Users->Posts...');
        foreach ($users as $user) {
            $user->addPost($posts[mt_rand(1, count($posts) - 1)]);
            $em->persist($user);
        }
        $em->flush();
        $output->writeln('Done.');

        // Users->Privileges
        $output->writeln('Linking Users->Privileges...');
        foreach ($users as $user) {
            $user->addPrivilege($privileges[mt_rand(1, count($privileges) - 1)]);
            $em->persist($user);
        }
        $em->flush();
        $output->writeln('Done.');

        // Users->Roles
        $output->writeln('Linking Users->Roles...');
        foreach ($users as $user) {
            $user->addRole($roles[mt_rand(1, count($roles) - 1)]);
            $em->persist($user);
        }
        $em->flush();
        $output->writeln('Done.');

        // Users->Votes
        $output->writeln('Linking Users->Votes...');
        foreach ($users as $user) {
            $user->addVote($votes[mt_rand(1, count($votes) - 1)]);
            $em->persist($user);
        }
        $em->flush();
        $output->writeln('Done.');

        /**
         * VOTES
         */

        // Votes->Posts
        $output->writeln('Linking Votes->Posts...');
        foreach ($votes as $vote) {
            $vote->addPost($posts[mt_rand(1, count($posts) - 1)]);
            $em->persist($vote);
        }
        $em->flush();
        $output->writeln('Done.');

        // Votes->Users
        $output->writeln('Linking Votes->Users...');
        foreach ($votes as $vote) {
            $vote->addUser($users[mt_rand(1, count($users) - 1)]);
            $em->persist($vote);
        }
        $em->flush();
        $output->writeln('Done.');
    }
}