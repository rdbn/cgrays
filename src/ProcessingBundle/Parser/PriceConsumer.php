<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 08.10.17
 * Time: 20:51
 */

namespace ProcessingBundle\Parser;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class PriceConsumer implements ConsumerInterface
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
     * PriceConsumer constructor.
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
     * @return int
     */
    public function execute(AMQPMessage $msg)
    {
        $parameters = json_decode($msg->getBody(), 1);

        $this->dbal->beginTransaction();
        try {
            $this->dbal->update('skins', ['steam_price' => $parameters['price']], ['id' => $parameters['id']]);
            $this->dbal->commit();
        } catch (DBALException $e) {
            $this->dbal->rollBack();
            $this->logger->error($e->getMessage());

            return self::MSG_REJECT_REQUEUE;
        }

        return self::MSG_ACK;
    }
}