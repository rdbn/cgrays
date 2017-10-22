<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 14.10.17
 * Time: 19:30
 */

namespace ApiBundle\Service;

use AppBundle\Entity\CasesBalanceUser;
use AppBundle\Entity\CasesDomain;
use AppBundle\Entity\Currency;
use AppBundle\Entity\Skins;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;

class CasesUserSellSkinsHandler
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
     * @param int $skinsId
     * @param int $userId
     * @param $domainId
     * @param $currencyCode
     * @throws \Exception
     */
    public function handler(int $skinsId, int $userId, $domainId, $currencyCode)
    {
        $casesDomain = $this->em->getRepository(CasesDomain::class)
            ->findOneBy(['uuid' => $domainId]);

        if (!$casesDomain) {
            throw new \Exception('Not found domain');
        }

        $currency = $this->em->getRepository(Currency::class)
            ->findOneBy(['code' => $currencyCode]);

        if (!$currency) {
            throw new \Exception('Not found currency');
        }

        $this->dbal->beginTransaction();
        try {
            $skins = $this->em->getRepository(Skins::class)
                ->findSkinsForUpdateById($skinsId);

            $userBalance = $this->em->getRepository(CasesBalanceUser::class)
                ->findUserBalanceForUpdateByUserIdCurrencyIdDomain(
                    $userId,
                    $currency->getId(),
                    $casesDomain->getId()
                );

            $this->dbal->update(
                'cases_balance_user',
                ['balance' => (int) $skins['steam_price'] + (int) $userBalance['balance']],
                ['id' => $userBalance['id']]
            );

            $this->dbal->commit();
        } catch (DBALException $e) {
            $this->dbal->rollBack();
            throw new \Exception($e->getMessage());
        }
    }
}