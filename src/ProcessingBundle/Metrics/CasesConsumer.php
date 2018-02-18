<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 12.02.2018
 * Time: 10:33
 */

namespace ProcessingBundle\Metrics;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use ProcessingBundle\Metrics\Event\HitCasesEvent;
use ProcessingBundle\Metrics\Event\OpenCasesEvent;
use ProcessingBundle\Metrics\Event\PickUpSkinsEvent;
use ProcessingBundle\Metrics\Event\SellSkinsEvent;
use Psr\Log\LoggerInterface;

class CasesConsumer implements ConsumerInterface
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
            $eventType = $parameters['event_type'];
            unset($parameters['event_type']);
            switch ($eventType) {
                case "hits":
                    $this->persister->persister(HitCasesEvent::handle($parameters));
                    break;
                case "open":
                    $this->persister->persister(OpenCasesEvent::handle($parameters));
                    break;
                case "pick_up_skins":
                    $this->persister->persister(PickUpSkinsEvent::handle($parameters));
                    break;
                case "sell_skins":
                    $this->persister->persister(SellSkinsEvent::handle($parameters));
                    break;
                default:
                    throw new \Exception("Not found event {$parameters['event_type']}");
            }
        } catch (\Exception $e) {
            $this->logger->error($msg->getBody());
            $this->logger->error($e->getMessage());
            return self::MSG_REJECT_REQUEUE;
        }

        return self::MSG_ACK;
    }

}