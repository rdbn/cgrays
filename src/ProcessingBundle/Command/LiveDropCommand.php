<?php

namespace ProcessingBundle\Command;

use AppBundle\Entity\CasesSkins;
use AppBundle\Entity\User;
use AppBundle\Services\Helper\MbStrimWidthHelper;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LiveDropCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('processing:live_drop')
            ->addArgument('uuid', InputArgument::REQUIRED, 'uuid domain')
            ->addArgument('user_ids', InputArgument::REQUIRED, 'user ids')
            ->addArgument('duration_sleep', InputArgument::REQUIRED, 'Задержка между отправками скинов.')
            ->setDescription('Отправляет скины в очередь.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $uuid = $input->getArgument('uuid');
        $userIds = $input->getArgument('user_ids');
        $durationSleep = $input->getArgument('duration_sleep');

        $container = $this->getContainer();
        $em = $container->get('doctrine.orm.entity_manager');
        $dbal = $container->get('doctrine.dbal.default_connection');
        $liveDropProducer = $container->get('old_sound_rabbit_mq.live_drop_producer');
        $logger = $container->get('logger');

        $users = $em->getRepository(User::class)
            ->findUsersByUserIds($userIds);

        $countUsers = count($users);

        $countCasesSkinsDomain = $em->getRepository(CasesSkins::class)
            ->getCountCasesSkins($uuid);

        $skinsSendLiveDrop = $em->getRepository(CasesSkins::class)
            ->findCasesSkinsByDomainId($uuid, $countUsers, rand(0, ($countCasesSkinsDomain - $countUsers)));

        $logger->info('Start.');
        $logger->info("Count user: {$countUsers}");
        $logger->info("Count skins: {$countCasesSkinsDomain}");
        foreach ($skinsSendLiveDrop as $index => $item) {
            $date = new \DateTime();
            try {
                $dbal->insert('cases_skins_drop_user', [
                    'user_id' => $users[$index]['user_id'],
                    'skins_id' => $item['skins_id'],
                    'created_at' => $date->format('Y-m-d H:i:s'),
                ]);
                $item['skins_name'] = MbStrimWidthHelper::strimWidth($item['skins_name']);
                $liveDropProducer->publish(json_encode($item));

                $item['user_id'] = $users[$index]['user_id'];
                $logger->info(json_encode($item));
            } catch (DBALException $e) {
                $logger->error($e->getMessage());
            }

            sleep($durationSleep);
        }

        $logger->info('End.');
    }
}
