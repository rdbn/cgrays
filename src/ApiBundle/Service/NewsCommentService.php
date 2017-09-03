<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 03.09.17
 * Time: 19:31
 */

namespace ApiBundle\Service;

use AppBundle\Entity\News;
use AppBundle\Entity\User;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class NewsCommentService
{
    /**
     * @var Connection
     */
    private $dbal;

    /**
     * @var User
     */
    private $user;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * NewsLikeService constructor.
     * @param Connection $dbal
     * @param TokenStorageInterface $tokenStorage
     * @param LoggerInterface $logger
     */
    public function __construct(Connection $dbal, TokenStorageInterface $tokenStorage, LoggerInterface $logger)
    {
        $this->dbal = $dbal;
        $this->user = $tokenStorage->getToken()->getUser();
        $this->logger = $logger;
    }

    /**
     * @param $newsId
     * @param $text
     * @return array
     * @throws \Exception
     */
    public function getComment($newsId, $text)
    {
        $date = new \DateTime();
        $date = $date->format('Y-m-d H:i:s');

        $this->dbal->beginTransaction();
        try {
            $this->dbal->insert('news_comment', [
                'news_id' => $newsId,
                'user_id' => $this->user->getId(),
                'text' => $text,
                'created_at' => $date,
            ]);
            $this->dbal->commit();
        } catch (DBALException $e) {
            $this->dbal->rollBack();
            $this->logger->error($e->getMessage());
            throw new \Exception($e->getMessage());
        }

        return [
            'username' => $this->user->getUsername(),
            'avatar' => $this->user->getAvatar(),
            'comment' => $text,
            'created_at' => $date,
        ];
    }
}