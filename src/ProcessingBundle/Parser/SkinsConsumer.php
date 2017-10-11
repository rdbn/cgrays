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

class SkinsConsumer implements ConsumerInterface
{
    /**
     * @var SkinsHandler
     */
    private $skinsHandler;

    public function __construct(SkinsHandler $skinsHandler)
    {
        $this->skinsHandler = $skinsHandler;
    }

    /**
     * @param AMQPMessage $msg
     * @return int
     */
    public function execute(AMQPMessage $msg)
    {
        $parameters = json_decode($msg->getBody(), 1);
        $this->skinsHandler->handler($parameters);

        return self::MSG_ACK;
    }

}