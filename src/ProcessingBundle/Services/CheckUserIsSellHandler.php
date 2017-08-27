<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 21.08.17
 * Time: 10:38
 */

namespace ProcessingBundle\Services;

use AppBundle\Repository\UserRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Psr\Log\LoggerInterface;

class CheckUserIsSellHandler
{
    /**
     * @var Connection
     */
    private $dbal;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * CheckUserIsSellHandler constructor.
     * @param Connection $dbal
     * @param UserRepository $userRepository
     * @param LoggerInterface $logger
     */
    public function __construct(Connection $dbal, UserRepository $userRepository, LoggerInterface $logger)
    {
        $this->dbal = $dbal;
        $this->userRepository = $userRepository;
        $this->logger = $logger;
    }

    public function handler()
    {
        $users = $this->userRepository->findUserIsNotOnline();
        foreach ($users as $user) {
            $this->dbal->beginTransaction();
            try {
                $this->dbal->update('users', ['is_sell' => false], ['id' => $user['id']]);
                $this->dbal->commit();
            } catch (DBALException $e) {
                $this->dbal->rollBack();
                $this->logger->error($e->getMessage());
            }
        }
    }
}