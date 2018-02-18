<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 12.02.2018
 * Time: 10:59
 */

namespace ApiCasesBundle\Service;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Psr\Log\LoggerInterface;

class MetricsEventSender
{
    /**
     * @var ProducerInterface
     */
    private $producer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * StatisticEventSender constructor.
     * @param ProducerInterface $producer
     * @param LoggerInterface $logger
     */
    public function __construct(ProducerInterface $producer, LoggerInterface $logger)
    {
        $this->producer = $producer;
        $this->logger = $logger;
    }

    /**
     * @param array $metrics
     */
    public function sender(array $metrics)
    {
        $date = date('Y-m-d');
        array_unshift($metrics, "'{$date}'");

        $this->producer->publish(json_encode($metrics));
    }
}