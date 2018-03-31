<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 08.10.17
 * Time: 20:52
 */

namespace ProcessingBundle\ParserCSGO;

use Doctrine\DBAL\Connection;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class SkinsConsumer implements ConsumerInterface
{
    /**
     * @var Connection
     */
    private $dbal;

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
     * @param Connection $dbal
     * @param SkinsHandler $skinsHandler
     * @param LoggerInterface $logger
     */
    public function __construct(Connection $dbal, SkinsHandler $skinsHandler, LoggerInterface $logger)
    {
        $this->dbal = $dbal;
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
            $this->skinsHandler->handler($parameters, $parameters['price']);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->dbal->insert('a_failed_link_skins', ['msg' => $msg->getBody()]);
        }

        return self::MSG_ACK;
    }

}