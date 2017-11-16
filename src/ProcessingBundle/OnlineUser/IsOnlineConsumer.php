<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 12.07.17
 * Time: 22:07
 */

namespace ProcessingBundle\OnlineUser;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * IsOnlineConsumer constructor.
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
        $user = json_decode($msg->getBody(), 1);

        if (!isset($user['username']) || !isset($user['is_online'])) {
            return self::MSG_REJECT;
        }

        $date = new \DateTime();
        $currentDateTime = $date->format('Y-m-d H:i:s');

        $this->dbal->beginTransaction();
        try {
            $this->dbal->update(
                'users',
                [
                    'is_online' => (boolean) $user['is_online'] ? 'TRUE' : 'FALSE',
                    'last_online' => $currentDateTime,
                ],
                ['username' => $user['username']]
            );

            $this->dbal->commit();
        } catch (DBALException $e) {
            $this->dbal->rollBack();
            $this->logger->error($e->getMessage());
        }

        return self::MSG_ACK;
    }

}