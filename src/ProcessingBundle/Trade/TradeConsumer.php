<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 12.07.17
 * Time: 22:03
 */

namespace ProcessingBundle\Trade;

use Doctrine\DBAL\Connection;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class TradeConsumer implements ConsumerInterface
{
    /**
     * @var Connection
     */
    private $dbal;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * TradeConsumer constructor.
     * @param Connection $dbal
     * @param LoggerInterface $logger
     */
    public function __construct(Connection $dbal, LoggerInterface $logger)
    {
        $this->dbal = $dbal;
        $this->logger = $logger;
    }

    /**
     * @param AMQPMessage $msg
     * @return integer
     */
    public function execute(AMQPMessage $msg)
    {
        $product = json_decode($msg->getBody(), 1);



        return self::MSG_ACK;
    }

}