<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 17.10.17
 * Time: 10:45
 */

namespace ApiBundle\Service;

use Predis\Client;
use Psr\Log\LoggerInterface;

class GenerateUUIDService
{
    /**
     * @var Client
     */
    private $redis;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * GenerateUUIDService constructor.
     * @param Client $redis
     * @param LoggerInterface $logger
     */
    public function __construct(Client $redis, LoggerInterface $logger)
    {
        $this->redis = $redis;
        $this->logger = $logger;
    }

    public function getUUID()
    {

    }

    public function isCheckUUID($uuid)
    {
        $uuid = $this->redis->get($uuid);
        if ($uuid) {
            return true;
        }

        return false;
    }
}