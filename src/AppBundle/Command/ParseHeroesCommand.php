<?php

namespace AppBundle\Command;

use Doctrine\DBAL\Driver\PDOException;
use SteamBundle\Director\SteamApiDirector;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ParseHeroesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:parse_heroes')
            ->setDescription('Parse all heroes');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $dbal = $container->get('doctrine.dbal.default_connection');
        $logger = $container->get('logger');

        $heroes = $container->get('steam.api_heroes')
            ->getResult();

        foreach ($heroes['result']['heroes'] as $hero) {
            try {
                $dbal->insert(
                    'heroes',
                    ['name' => $hero['name'], 'title' => $hero['localized_name']]
                );
            } catch (PDOException $e) {
                $logger->error($e->getMessage());
            }
        }
    }
}
