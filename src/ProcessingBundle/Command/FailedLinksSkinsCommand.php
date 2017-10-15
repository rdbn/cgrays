<?php

namespace ProcessingBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FailedLinksSkinsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('processing:failed_links_skins')
            ->addArgument('limit', InputArgument::REQUIRED, 'Limit parser links')
            ->setDescription('Resend failed link parse.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $limit = (int) $input->getArgument('limit');

        $container = $this->getContainer();
        $dbal = $container->get('doctrine.dbal.default_connection');
        $producer = $container->get('old_sound_rabbit_mq.parser_skins_producer');
        $logger = $container->get('logger');
        $failedLinksSkins = $container->get('app.repository.failed_links_skins')
            ->findAllFailedLinks($limit);

        foreach ($failedLinksSkins as $item) {
            $dbal->beginTransaction();
            try {
                $dbal->delete('a_failed_link_skins', ['id' => $item['id']]);
                $producer->publish($item['msg']);
                $dbal->commit();
            } catch (\Exception $e) {
                $dbal->rollBack();
                $logger->error($e->getMessage());
            }
        }

        $logger->info('Error link count: ' . count($failedLinksSkins));
    }
}
