<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 21.07.17
 * Time: 10:45
 */

namespace AppBundle\Services;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Psr\Log\LoggerInterface;

class TradeService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var ProducerInterface
     */
    private $producer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * TradeService constructor.
     * @param EntityManager $em
     * @param ProducerInterface $producer
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManager $em, ProducerInterface $producer, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->producer = $producer;
        $this->logger = $logger;
    }

    /**
     * @param User $user
     * @param $productPriceId
     */
    public function handler(User $user, $productPriceId)
    {

    }
}