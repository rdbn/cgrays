<?php

namespace DotaBundle\Command;

use Doctrine\DBAL\Driver\PDOException;
use SteamBundle\Loader\ApiLoader;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ParseRarityCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:dota:parse_rarity')
            ->setDescription('Parse all rareness');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $dbal = $container->get('doctrine.dbal.default_connection');
        $logger = $container->get('logger');

        $rareness = $container->get('steam.api_dota_rarities')
            ->getResult();

        foreach ($rareness['result']['rarities'] as $item) {
            try {
                $dbal->insert(
                    "rarity_dota",
                    [
                        "name" => $item['name'],
                        'title' => $item['localized_name'],
                        'order_item' => $item['order'],
                        'color' => $item['color'],
                    ]
                );
            } catch (PDOException $e) {
                $logger->error($e->getMessage());
            }

        }
    }
}
