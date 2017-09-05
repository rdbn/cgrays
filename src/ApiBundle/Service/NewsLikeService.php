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
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class NewsLikeService
{
    /**
     * @var EntityManager
     */
    private $em;

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
     * @param EntityManager $em
     * @param TokenStorageInterface $tokenStorage
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManager $em, TokenStorageInterface $tokenStorage, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->user = $tokenStorage->getToken()->getUser();
        $this->logger = $logger;
    }

    /**
     * @param $newsId
     * @return self
     */
    public function addLike($newsId)
    {
        $dbal = $this->em->getConnection();
        $dbal->beginTransaction();
        try {
            $dbal->insert('news_like', ['news_id' => $newsId, 'user_id' => $this->user->getId()]);
            $dbal->commit();
        } catch (DBALException $e) {
            $dbal->rollBack();
            $this->logger->error($e->getMessage());
        }

        return $this;
    }

    public function getCountLike($newsId)
    {
        return $this->em->getRepository(News::class)
            ->findCountLikeNewsByNewsId($newsId);
    }
}