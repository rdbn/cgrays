<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 12.07.17
 * Time: 22:07
 */

namespace ProcessingBundle\OnlineUser;

use Doctrine\DBAL\Connection;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class IsOnlineConsumer implements ConsumerInterface
{
    /**
     * @var Connection
     */
    private $dbal;

    /**
     * @var IsOnlineHandler
     */
    private $isOnlineHandler;

    /**
     * IsOnlineConsumer constructor.
     * @param Connection $dbal
     * @param IsOnlineHandler $isOnlineHandler
     */
    public function __construct(Connection $dbal, IsOnlineHandler $isOnlineHandler)
    {
        $this->dbal = $dbal;
        $this->isOnlineHandler = $isOnlineHandler;
    }

    /**
     * @param AMQPMessage $msg
     * @return integer
     */
    public function execute(AMQPMessage $msg)
    {
        $user = json_decode($msg->getBody(), 1);

        if (!isset($user['username']) || !isset($user['is_online'])) {
            return self::MSG_REJECT;
        }

        $this->isOnlineHandler
            ->handler(
                $user['username'],
                $user['is_online']
            );

        return self::MSG_ACK;
    }

}