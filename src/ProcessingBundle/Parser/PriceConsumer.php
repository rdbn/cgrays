<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 08.10.17
 * Time: 20:51
 */

namespace ProcessingBundle\Parser;

use Doctrine\DBAL\DBALException;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class PriceConsumer implements ConsumerInterface
{
    /**
     * @var PriceHandler
     */
    private $handler;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * PriceConsumer constructor.
     * @param PriceHandler $handler
     * @param LoggerInterface $logger
     */
    public function __construct(PriceHandler $handler, LoggerInterface $logger)
    {
        $this->handler = $handler;
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
            $this->handler->handle($parameters);
        } catch (DBALException $e) {
            $this->logger->error($e->getMessage());
            return self::MSG_REJECT_REQUEUE;
        }

        $this->logger->info("Message: {$msg->getBody()}");

        return self::MSG_REJECT_REQUEUE;
    }
}