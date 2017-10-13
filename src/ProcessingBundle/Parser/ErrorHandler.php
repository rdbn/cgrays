<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 13.10.17
 * Time: 18:23
 */

namespace ProcessingBundle\Parser;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Psr\Log\LoggerInterface;

class ErrorHandler
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
     * ErrorHandler constructor.
     * @param Connection $dbal
     * @param LoggerInterface $logger
     */
    public function __construct(Connection $dbal, LoggerInterface $logger)
    {
        $this->dbal = $dbal;
        $this->logger = $logger;
    }

    public function handler($msg)
    {
        $this->dbal->beginTransaction();
        try {
            $this->dbal->insert('a_failed_link_skins', ['msg' => $msg]);
            $this->dbal->commit();
        } catch (DBALException $e) {
            $this->dbal->rollBack();
            $this->logger->error($e->getMessage());
        }
    }
}