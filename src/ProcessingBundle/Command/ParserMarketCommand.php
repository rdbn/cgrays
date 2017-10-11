<?php

namespace ProcessingBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ParserMarketCommand extends ContainerAwareCommand
{
    const PARSER_START_ID = 'parser_start_id';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('processing:parser_market')
            ->addArgument('game-id', InputArgument::REQUIRED, 'Game id skins parser')
            ->addArgument('count', InputArgument::OPTIONAL, 'Count skins parser', 100)
            ->addArgument('delay', InputArgument::OPTIONAL, 'Delay skins parser', 60 )
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $gameId = (int) $input->getArgument('game-id');
        $count = (int) $input->getArgument('count');
        $delay = (int) $input->getArgument('delay');

        $container = $this->getContainer();
        $redis = $container->get('snc_redis.default');
        $parserMarketHandler = $container->get('processing.service.parser_market_handler');
        $startId = (int) $redis->get(self::PARSER_START_ID);

        while (true) {
            $parserMarketHandler->handler($startId, $gameId, $count);

            $date = new \DateTime();
            $container->get('logger')->info("{$date->format('Y-m-d H:i:s')}: start id - {$startId}");

            if ($parserMarketHandler->getCount() < $count) {
                $redis->del([self::PARSER_START_ID]);
                break;
            } else {
                $startId += $count;
                $redis->set(self::PARSER_START_ID, $startId);
            }

            sleep($delay);
        }
    }
}
