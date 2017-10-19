<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 11.09.17
 * Time: 10:24
 */

namespace ApiBundle\Service;

use AppBundle\Entity\CasesSkins;
use AppBundle\Entity\User;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CaseOpenHandler
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Connection
     */
    private $dbal;

    /**
     * @var User
     */
    private $user;

    /**
     * @var GamesService
     */
    private $gamesService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * CaseOpenService constructor.
     * @param EntityManager $em
     * @param Connection $dbal
     * @param TokenStorageInterface $tokenStorage
     * @param GamesService $gamesService
     * @param LoggerInterface $logger
     */
    public function __construct(
        EntityManager $em,
        Connection $dbal,
        TokenStorageInterface $tokenStorage,
        GamesService $gamesService,
        LoggerInterface $logger
    )
    {
        $this->em = $em;
        $this->dbal = $dbal;
        $this->user = $tokenStorage->getToken()->getUser();
        $this->gamesService = $gamesService;
        $this->logger = $logger;
    }

    /**
     * @param $domainId
     * @param $id
     * @param $userId
     * @return array
     * @throws \Exception
     */
    public function handler($domainId, $id, $userId)
    {
        $skins = $this->em->getRepository(CasesSkins::class)
            ->findSkinsByCasesId($domainId, $id);

        if (!count($skins)) {
            return [];
        }

        $date = new \DateTime();

        $this->dbal->beginTransaction();
        try {
            $skins = $skins[mt_rand(0, (count($skins) - 1))];
            $this->em->getRepository(CasesSkins::class)
                ->findCasesSkinsByCasesIdForUpdate($skins['cases_skins_id']);

            $this->dbal->update(
                'cases_skins',
                ['count_drop' => $skins['count_drop'] + 1],
                ['id' => $skins['cases_skins_id']]
            );

            $this->dbal->insert(
                'cases_skins_drop_user',
                [
                    'skins_id' => $skins['id'],
                    'user_id' => $userId,
                    'created_at' => $date->format('Y-m-d H:i:s'),
                ]
            );

            $this->dbal->commit();
        } catch (DBALException $e) {
            $this->dbal->rollBack();

            $this->logger->error($e->getMessage());
            throw new \Exception($e->getMessage());
        }

        $this->gamesService->flushRedisGame($userId, $skins['id']);
        unset($skins['id'], $skins['cases_skins_id'], $skins['count_drop'], $skins['count']);

        return $skins;
    }
}