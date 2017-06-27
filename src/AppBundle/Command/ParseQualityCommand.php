<?php

namespace AppBundle\Command;

use Doctrine\DBAL\Driver\PDOException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ParseQualityCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:parse_quality')
            ->setDescription('Parse all quality');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $dbal = $container->get('doctrine.dbal.default_connection');
        $logger = $container->get('logger');

        $qualities = json_decode(file_get_contents(__DIR__ . '/../../../quality.json'), 1);
        foreach ($qualities as $name => $property) {
            try {
                $dbal->insert(
                    'quality',
                    ['name' => $name, 'title' => $name, 'color' => $property['hexColor']]
                );
            } catch (PDOException $e) {
                $logger->error($e->getMessage());
            }
        }
    }
}
