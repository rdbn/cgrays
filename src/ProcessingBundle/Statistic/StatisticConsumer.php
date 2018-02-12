<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 12.02.2018
 * Time: 10:33
 */

namespace ProcessingBundle\Statistic;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class StatisticConsumer implements ConsumerInterface
{
    /**
     * @var Persister
     */
    private $persister;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * StatisticConsumer constructor.
     * @param Persister $persister
     * @param LoggerInterface $logger
     */
    public function __construct(Persister $persister, LoggerInterface $logger)
    {
        $this->persister = $persister;
        $this->logger = $logger;
    }

    /**
     * @param AMQPMessage $msg
     * @return int|mixed
     */
    public function execute(AMQPMessage $msg)
    {
        $parameters = json_decode($msg->getBody(), 1);
        if (!is_array($parameters) && !count($parameters)) {
            $this->logger->error($msg->getBody());
            return self::MSG_REJECT;
        }

        try {
            switch ($parameters['event_type']) {
                case "hit_cases":
                    $insertSQL = HitCasesEvent::handle($parameters);
                    break;
                case "cases_open":
                    $insertSQL = OpenCasesEvent::handle($parameters);
                    break;
                case "pick_up_skins":
                    $insertSQL = PickUpSkinsEvent::handle($parameters);
                    break;
                case "sell_skins":
                    $insertSQL = SellSkinsEvent::handle($parameters);
                    break;
                default:
                    throw new \Exception('Not found event.');
            }
        } catch (\Exception $e) {
            $this->logger->error($msg->getBody());
            $this->logger->error($e->getMessage());
            return self::MSG_REJECT;
        }

        try {
            $this->persister->persister($insertSQL);
        } catch (\Exception $e) {
            $this->logger->error($msg->getBody());
            $this->logger->error($e->getMessage());
            return self::MSG_REJECT_REQUEUE;
        }

        return self::MSG_ACK;
    }

}