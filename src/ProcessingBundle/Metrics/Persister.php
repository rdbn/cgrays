<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 12.02.2018
 * Time: 21:43
 */

namespace ProcessingBundle\Metrics;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Psr\Log\LoggerInterface;

class Persister
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
     * Persister constructor.
     * @param Connection $dbal
     * @param LoggerInterface $logger
     */
    public function __construct(Connection $dbal, LoggerInterface $logger)
    {
        $this->dbal = $dbal;
        $this->logger = $logger;
    }

    /**
     * @param $insertSql
     */
    public function persister($insertSql)
    {
        try {
            $stmt = $this->dbal->prepare($insertSql);
            $stmt->execute();
        } catch (DBALException $e) {
            $this->logger->error($e->getMessage());
        }
    }
}