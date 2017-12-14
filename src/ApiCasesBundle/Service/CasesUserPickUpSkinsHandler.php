<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 14.10.17
 * Time: 22:43
 */

namespace ApiCasesBundle\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;

class CasesUserPickUpSkinsHandler
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
     * CasesUserPickUpSkinsHandler constructor.
     * @param EntityManager $em
     * @param Connection $dbal
     */
    public function __construct(EntityManager $em, Connection $dbal)
    {
        $this->em = $em;
        $this->dbal = $dbal;
    }

    /**
     * @param array $resultGame
     * @param $userId
     * @throws \Exception
     */
    public function handler(array $resultGame, $userId)
    {
        $date = new \DateTime();

        $this->dbal->beginTransaction();
        try {
            $this->dbal->insert(
                'cases_skins_pick_up_user',
                [
                    'skins_id' => $resultGame['skins_id'],
                    'cases_domain_id' => $resultGame['cases_skins_id'],
                    'user_id' => $userId,
                    'created_at' => $date->format('Y-m-d H:i:s')
                ]
            );

            $this->dbal->commit();
        } catch (DBALException $e) {
            $this->dbal->rollBack();
            throw new \Exception($e->getMessage());
        }
    }
}