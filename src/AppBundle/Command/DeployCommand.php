<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeployCommand extends ContainerAwareCommand
{

    /**
     * Configuration
     */
    protected function configure()
    {
        $this->setName('deploy')
             ->setDescription('Run script after deploying application');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<question>Deploying app</question>");

        // Update database schema
        $output->writeln("<comment>Updating database schema</comment>");
        $command    = $this->getApplication()->find('doctrine:schema:update');
        $arguments  = ['--force' => true];
        $greetInput = new ArrayInput($arguments);
        $command->run($greetInput, $output);

        // Deploying dev environment
        if ($this->getContainer()->getParameter('symfony_env') == 'dev') {
            $output->writeln("<question>Deploying dev environment</question>");
            // Fixture
            $output->writeln("<comment>Loading fixtures</comment>");
            $command    = $this->getApplication()->find(
                'doctrine:fixture:load'
            );
            $arguments  = [];
            $greetInput = new ArrayInput($arguments);
            $greetInput->setInteractive(false);
            $command->run($greetInput, $output);
        }

        $output->writeln("<question>Application deployed</question>");
    }
}