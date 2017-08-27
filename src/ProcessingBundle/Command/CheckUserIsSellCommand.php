<?php

namespace ProcessingBundle\Command;

use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckUserIsSellCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('processing:check_user_is_sell')
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
        $users = $container->get('app.repository.user')
            ->findUserIsNotOnline();

        foreach ($users as $user) {
            $dbal->beginTransaction();
            try {
                $dbal->update('users', ['is_sell' => false], ['id' => $user['id']]);
                $dbal->commit();
            } catch (DBALException $e) {
                $dbal->rollBack();
                $logger->error($e->getMessage());
            }
        }
    }
}
