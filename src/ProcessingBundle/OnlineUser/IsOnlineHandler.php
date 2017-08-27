<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 20.08.17
 * Time: 23:03
 */

namespace ProcessingBundle\OnlineUser;

use AppBundle\Repository\UserRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Psr\Log\LoggerInterface;

class IsOnlineHandler
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
     * IsOnlineHandler constructor.
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

    /**
     * @param $username
     * @param $isOnline
     */
    public function handler($username, $isOnline)
    {
        $date = new \DateTime();
        $currentDateTime = $date->format('Y-m-d H:i:s');

        $this->dbal->beginTransaction();
        try {
            $user = $this->userRepository->findUserForUpdateByUsername($username);
            $this->dbal->update(
                'users',
                [
                    'is_online' => (boolean) $isOnline ? 'TRUE' : 'FALSE',
                    'last_online' => $currentDateTime,
                ],
                ['id' => $user['id']]
            );

            $this->dbal->commit();
        } catch (DBALException $e) {
            $this->dbal->rollBack();
            $this->logger->error($e->getMessage());
        }
    }
}