<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 14.10.17
 * Time: 19:30
 */

namespace ApiCasesBundle\Service;

use AppBundle\Entity\CasesBalanceUser;
use AppBundle\Entity\CasesDomain;
use AppBundle\Entity\Skins;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;

class UserSellSkinsHandler
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
     * CasesUserSellSkinsHandler constructor.
     * @param EntityManager $em
     * @param Connection $dbal
     */
    public function __construct(EntityManager $em, Connection $dbal)
    {
        $this->em = $em;
        $this->dbal = $dbal;
    }

    /**
     * @param Skins $skins
     * @param int $userId
     * @param $domainId
     * @param $currencyId
     * @return int
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Exception
     */
    public function handler(Skins $skins, int $userId, $domainId, $currencyId)
    {
        $casesDomain = $this->em->getRepository(CasesDomain::class)
            ->findOneBy(['uuid' => $domainId]);

        if (!$casesDomain) {
            throw new \Exception('Not found domain');
        }

        $this->dbal->beginTransaction();
        try {
            $userBalance = $this->em->getRepository(CasesBalanceUser::class)
                ->findUserBalanceForUpdateByUserIdCurrencyIdDomain(
                    $userId,
                    $currencyId,
                    $casesDomain->getId()
                );

            $balance = (float) $skins->getSteamPrice() + (float) $userBalance['balance'];
            $this->dbal->update('cases_balance_user', ['balance' => $balance], ['id' => $userBalance['id']]);

            $this->dbal->commit();
        } catch (DBALException $e) {
            $this->dbal->rollBack();
            throw new \Exception($e->getMessage());
        }

        return $balance;
    }
}