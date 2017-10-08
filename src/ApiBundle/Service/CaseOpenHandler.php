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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * CaseOpenService constructor.
     * @param EntityManager $em
     * @param Connection $dbal
     * @param TokenStorageInterface $tokenStorage
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManager $em, Connection $dbal, TokenStorageInterface $tokenStorage, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->dbal = $dbal;
        $this->user = $tokenStorage->getToken()->getUser();
        $this->logger = $logger;
    }

    /**
     * @param $domainId
     * @param $id
     * @return array
     * @throws \Exception
     */
    public function handler($domainId, $id)
    {
        $skins = $this->em->getRepository(CasesSkins::class)
            ->findSkinsByCasesId($domainId, $id);

        if (!count($skins)) {
            return [];
        }

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

            $this->dbal->commit();
        } catch (DBALException $e) {
            $this->dbal->rollBack();

            $this->logger->error($e->getMessage());
            throw new \Exception($e->getMessage());
        }

        //unset($skins['cases_skins_id'], $skins['count_drop']);

        return $skins;
    }
}