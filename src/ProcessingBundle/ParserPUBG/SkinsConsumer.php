<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 30.03.2018
 * Time: 17:24
 */

namespace ProcessingBundle\ParserPUBG;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class SkinsConsumer implements ConsumerInterface
{
    /**
     * @var LoggerInterface
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

    private $delayed = 30;

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
     * @return int|mixed
     */
    public function execute(AMQPMessage $msg)
    {
        $skins = json_decode($msg->getBody(), 1);

        try {
            $this->skinsHandler->handle($skins);
        } catch (\Exception $e) {
            $this->logger->error("{$msg->getBody()}: {$e->getMessage()}");

            sleep($this->delayed);
            $this->delayed += 30;
            return self::MSG_REJECT_REQUEUE;
        }

        $this->logger->info($msg->getBody());

        return self::MSG_ACK;
    }

}