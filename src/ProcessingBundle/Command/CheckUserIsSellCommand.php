<?php

namespace ProcessingBundle\Command;

use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
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
            ->addArgument(
                'second_duration',
                InputArgument::OPTIONAL,
                'Макисамальна приемлемая продолжительность не онлайн',
                60
            )
            ->setDescription('Снимаем галку продажи предметов у пользователей которые больше не онлайн.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $secondDuration = $input->getArgument('second_duration');

        $container = $this->getContainer();
        $dbal = $container->get('doctrine.dbal.default_connection');
        $logger = $container->get('logger');

        $users = $container->get('app.repository.user')
            ->findUserIsNotOnline($secondDuration);

        foreach ($users as $user) {
            $dbal->beginTransaction();
            try {
                $dbal->update('users', ['is_sell' => 'FALSE'], ['id' => $user['id']]);
                $dbal->commit();
            } catch (DBALException $e) {
                $dbal->rollBack();
                $logger->error($e->getMessage());
            }
        }
    }
}
