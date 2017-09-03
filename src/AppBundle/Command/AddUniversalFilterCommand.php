<?php

namespace AppBundle\Command;

use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddUniversalFilterCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:add_universal_filter')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $dbal = $container->get('doctrine.dbal.default_connection');
        $logger = $container->get('logger');

        $dbal->beginTransaction();
        try {
            $dbal->insert('quality', ['localized_tag_name' => 'Другое', 'internal_name' => 'another']);
            $dbal->insert('weapon', ['localized_tag_name' => 'Другое', 'internal_name' => 'another']);
            $dbal->insert('rarity', ['localized_tag_name' => 'Другое', 'internal_name' => 'another', 'color' => '#fff']);
            $dbal->insert('decor', ['localized_tag_name' => 'Другое', 'internal_name' => 'another']);
            $dbal->insert('type_skins', ['localized_tag_name' => 'Другое', 'internal_name' => 'another']);
            $dbal->insert('item_set', ['localized_tag_name' => 'Другое', 'internal_name' => 'another']);

            $dbal->commit();
        } catch (DBALException $e) {
            $dbal->rollBack();
            $logger->error($e->getMessage());
        }
    }
}
