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
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        // findBy seems to be the only working method in fixtures
        $comments   = $em->getRepository('AppBundle:Comment')->findAll();
        $comment    = $em->getRepository('AppBundle:Comment')->find(1);
        $pages      = $em->getRepository('AppBundle:Page')->findAll();
        $page       = $em->getRepository('AppBundle:Page')->find(1);
        $posts      = $em->getRepository('AppBundle:Post')->findAll();
        $post       = $em->getRepository('AppBundle:Post')->find(1);
        $privileges = $em->getRepository('AppBundle:Privilege')->findAll();
        $privilege  = $em->getRepository('AppBundle:Privilege')->find(1);
        $roles      = $em->getRepository('AppBundle:Role')->findAll();
        $role       = $em->getRepository('AppBundle:Role')->find(1);
        $sections   = $em->getRepository('AppBundle:Section')->findAll();
        $section    = $em->getRepository('AppBundle:Section')->find(1);
        $tags       = $em->getRepository('AppBundle:Tag')->findAll();
        $tag        = $em->getRepository('AppBundle:Tag')->find(1);
        $users      = $em->getRepository('AppBundle:User')->findAll();
        $user       = $em->getRepository('AppBundle:User')->find(1);

        $output->writeln(count($comments).' Comments found');
        $output->writeln(count($pages).' Pages found');
        $output->writeln(count($posts).' Posts found');
        $output->writeln(count($privileges).' Privileges found');
        $output->writeln(count($roles).' Roles found');
        $output->writeln(count($sections).' Sections found');
        $output->writeln(count($tags).' Tags found');
        $output->writeln(count($users).' Users found');

        $comment->setPost($posts[mt_rand(1, count($posts) - 1)]);
        $comment->setUser($users[mt_rand(1, count($users) - 1)]);
        $em->persist($comment);
        $output->writeln('Comment');

        $page->addSection($sections[mt_rand(1, count($sections) - 1)]);
        $em->persist($page);
        $output->writeln('Page');

        $post->addComment($comments[mt_rand(1, count($comments) - 1)]);
        $post->addSection($sections[mt_rand(1, count($sections) - 1)]);
        $post->addTag($tags[mt_rand(1, count($tags) - 1)]);
        $post->setUser($users[mt_rand(1, count($users) - 1)]);
        $em->persist($post);
        $output->writeln('Post');

        $privilege->addRole($roles[mt_rand(1, count($roles) - 1)]);
        $em->persist($privilege);
        $output->writeln('Privilege');

        $role->addPrivilege($privileges[mt_rand(1, count($privileges) - 1)]);
        // $role->addUser($users[mt_rand(1, count($users) - 1)]);
        $em->persist($role);
        $output->writeln('Role');

        $section->addPage($pages[mt_rand(1, count($pages) - 1)]);
        $section->addPost($posts[mt_rand(1, count($posts) - 1)]);
        $section->addTag($tags[mt_rand(1, count($tags) - 1)]);
        $em->persist($section);
        $output->writeln('Section');

        $tag->addPage($pages[mt_rand(1, count($pages) - 1)]);
        $tag->addPost($posts[mt_rand(1, count($posts) - 1)]);
        $tag->addSection($sections[mt_rand(1, count($sections) - 1)]);
        $em->persist($tag);
        $output->writeln('Tag');

        $user->addPost($posts[mt_rand(1, count($posts) - 1)]);
        // $user->addRole($roles[mt_rand(1, count($roles) - 1)]);
        $em->persist($user);
        $output->writeln('User');

        $em->flush();
    }
}