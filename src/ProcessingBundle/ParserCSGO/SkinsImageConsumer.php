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

class SkinsImageConsumer implements ConsumerInterface
{
    /**
     * @var SkinsImageHandler
     */
    private $imageHandler;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SkinsConsumer constructor.
     * @param SkinsImageHandler $imageHandler
     * @param LoggerInterface $logger
     */
    public function __construct(SkinsImageHandler $imageHandler, LoggerInterface $logger)
    {
        $this->imageHandler = $imageHandler;
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
            $this->imageHandler->handler($parameters);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            sleep(30);

            return self::MSG_REJECT_REQUEUE;
        }

        return self::MSG_ACK;
    }

}