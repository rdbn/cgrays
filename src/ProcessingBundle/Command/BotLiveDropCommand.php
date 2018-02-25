<?php

namespace ProcessingBundle\Command;

use AppBundle\Entity\BotLiveDrop;
use AppBundle\Entity\CasesSkins;
use AppBundle\Services\Helper\MbStrimWidthHelper;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BotLiveDropCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('processing:live_drop')
            ->addArgument('duration_sleep', InputArgument::REQUIRED, 'Задержка между отправками скинов.')
            ->setDescription('Отправляет скины в очередь.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $durationSleep = $input->getArgument('duration_sleep');

        $container = $this->getContainer();
        $em = $container->get('doctrine.orm.entity_manager');
        $dbal = $container->get('doctrine.dbal.default_connection');
        $liveDropProducer = $container->get('old_sound_rabbit_mq.live_drop_producer');
        $logger = $container->get('logger');


        try {
            $botLiveDrops = $em->getRepository(BotLiveDrop::class)
                ->findBotUser(date('H'));

            $countBot = count($botLiveDrops);
            $logger->info("Count bot users: {$countBot}");
            if (!$countBot) {
                return;
            }
        } catch (DBALException $e) {
            $logger->error($e->getMessage());
            return;
        }

        foreach ($botLiveDrops as $botLiveDrop) {
            $countCasesSkinsDomain = $em->getRepository(CasesSkins::class)
                ->getCountCasesSkins($botLiveDrop['cases_domain_id']);

            $skinsSendLiveDrop = $em->getRepository(CasesSkins::class)
                ->findCasesSkinsByDomainId($botLiveDrop['cases_domain_id'], rand(0, ($countCasesSkinsDomain - $countBot)));

            $logger->info('Start.');
            $logger->info("Count skins: {$countCasesSkinsDomain}");
            try {
                $dbal->insert('cases_skins_drop_user', [
                    'user_id' => $botLiveDrop['user_id'],
                    'skins_id' => $skinsSendLiveDrop['skins_id'],
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                $skinsSendLiveDrop['skin_name'] = MbStrimWidthHelper::strimWidth($skinsSendLiveDrop['skin_name']);
                $liveDropProducer->publish(json_encode($skinsSendLiveDrop));

                $skinsSendLiveDrop['user_id'] = $botLiveDrop['user_id'];
                $logger->info(json_encode($skinsSendLiveDrop));
            } catch (DBALException $e) {
                $logger->error($e->getMessage());
            }

            sleep($durationSleep);
            $logger->info('End.');
        }
    }
}