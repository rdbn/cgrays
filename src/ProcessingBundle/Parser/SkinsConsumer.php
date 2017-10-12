<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 08.10.17
 * Time: 20:52
 */

namespace ProcessingBundle\Parser;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class SkinsConsumer implements ConsumerInterface
{
    /**
     * @var SkinsHandler
     */
    private $skinsHandler;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SkinsConsumer constructor.
     * @param SkinsHandler $skinsHandler
     * @param LoggerInterface $logger
     */
    public function __construct(SkinsHandler $skinsHandler, LoggerInterface $logger)
    {
        $this->skinsHandler = $skinsHandler;
        $this->logger = $logger;
    }

    /**
     * @param AMQPMessage $msg
     * @return int
     */
    public function execute(AMQPMessage $msg)
    {
        $parameters = json_decode($msg->getBody(), 1);
        try {
            $this->skinsHandler->handler($parameters);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return self::MSG_REJECT_REQUEUE;
        }

        return self::MSG_ACK;
    }

}