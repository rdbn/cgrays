<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 30.03.2018
 * Time: 17:24
 */

namespace ProcessingBundle\ParserPUBG;

use AppBundle\Entity\SkinsPUBG;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class PriceConsumer implements ConsumerInterface
{
    /**
     * @var LoggerInterface
     */
    private $dbal;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SkinsConsumer constructor.
     * @param Connection $dbal
     * @param EntityManager $em
     * @param LoggerInterface $logger
     */
    public function __construct(Connection $dbal, EntityManager $em, LoggerInterface $logger)
    {
        $this->dbal = $dbal;
        $this->em = $em;
        $this->logger = $logger;
    }

    /**
     * @param AMQPMessage $msg
     * @return int|mixed
     */
    public function execute(AMQPMessage $msg)
    {
        $skins = json_decode($msg->getBody(), 1);
        $skinsEntity = $this->em->getRepository(SkinsPUBG::class)
            ->findOneBy(['name' => $skins['name']]);

        if (!$skinsEntity) {
            $this->logger->error("{$msg->getBody()}");
            return self::MSG_ACK;
        }

        $this->dbal->update(
            'skins_pubg',
            ['steam_price' => (float)str_replace(" ", "", $skins['price'])],
            ['name' => $skins['name']]
        );

        $this->logger->info($msg->getBody());

        return self::MSG_ACK;
    }

}